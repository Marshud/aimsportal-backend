<?php

namespace App\Http\Controllers\Api;

use App\Enums\CoreRoles;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationResource;
use App\Http\Resources\UserResource;
use App\Mail\NewUserRequestToJoinOrganisation;
use App\Mail\SignupStarted;
use App\Models\EmailVerification;
use App\Models\Organisation;
use App\Models\PasswordResetRequest;
use App\Models\User;
use App\Notifications\UserPasswordResetEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Whoops\Run;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only('profile', 'destroy', 'deauthenticate', 'updateStatus', 'updateUser', 'show', 'listOrganisationUsers', 'listSuperAdministrators', 'createSuperAdministrator');
        $this->middleware('isapproved')->only('updateStatus', 'updateUser', 'show', 'listOrganisationUsers');
        $this->middleware('verified.app')->only('authenticateGuest');
    }   

    public function startEmailSignup(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|max:200',
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        $email_verification = EmailVerification::firstOrNew(
            ['email' => $request->email]            
        );
        $email_verification->code = rand(1452,9999);
        $email_verification->expires_at =  now()->addMinutes(5);
        $email_verification->save();
        
        try {
            Mail::to($request->email)->send(new SignupStarted($email_verification));
        } catch(\Exception $e) {
            Log::error(['EMAIL_SEND_ERROR' => $e->getMessage()]);

            return response()->error(__('messages.email_not_sent'),500);
        }
        

        return response()->success(['message' => __('auth.verification_sent')]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|unique:users,email',
            'name' => 'required',
            'code' => 'required',
            'password' => 'required|min:6|confirmed',
            'organisation' => 'required|exists:organisations,id',
            //'role' => 'array|required',
            'role' => 'required|in:Subscriber,Manager,Contributor'
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        if (!$this->isEmailValid($request->email, $request->code)) {

            throw ValidationException::withMessages([
                'code' => [__('auth.invalid_code')],
            ]);
        }

        $proposed_organisation = Organisation::find($request->organisation);
        $organisation_users = $proposed_organisation->users;

        $maximum_organisation_users = get_system_setting('maximum_organisation_users') ?? 10;

        if ($organisation_users >= $maximum_organisation_users) {
            
            return response()->error(__('messages.invalid_request'), 422, __('messages.users_exceeded'));
        }
        
        $aims_user = new User;
        $aims_user->name = $request->name;
        $aims_user->email = $request->email;
        $aims_user->password = Hash::make($request->password);
        $aims_user->current_organisation_id = $request->organisation;
        $aims_user->save();

        

        $aims_user->attachRoles([$request->role],$proposed_organisation);

        

        //get admin users
        $superAdminUsers = User::whereRoleIs(CoreRoles::SuperAdministrator->value)->get();
        
        if ($organisation_users->count() <= 1) {
            Log::info('only one user');
            Mail::to($superAdminUsers)->send(new NewUserRequestToJoinOrganisation($aims_user));
            
        }

        if ($organisation_users->count() > 1) {

            $approved_users_excluding_applicant = $organisation_users->where('id','!=',$aims_user->id)->where('status', UserStatus::Approved);
            if($approved_users_excluding_applicant->count() > 0) {
                try {
                    Mail::to($approved_users_excluding_applicant)->send(new NewUserRequestToJoinOrganisation($aims_user));
                } catch(\Exception $e) {
                    Log::error(['EMAIL_SEND_ERROR' => $e->getMessage()]);
                }
            }
            else if ($approved_users_excluding_applicant->count() == 0) {
                Mail::to($superAdminUsers)->send(new NewUserRequestToJoinOrganisation($aims_user));
            }
            
            
        }

        return response()->success(__('messages.success'));

    }

    private function isEmailValid($email, $code)
    {
        $verify = EmailVerification::Where('email', $email)->where('code', $code)->first();
        if (!$verify) {
            return false;
        }

        if (now() > $verify->expires_at) {
            return false;
        }

        return true;
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {

            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
            
        }

        $accessToken = $user->createToken($user->name)->plainTextToken;

        $user_info = [
            'token' => $accessToken,
            //'user' => new UserResource($user)
        ];

        return response()->success($user_info);
    }

    public function authenticateGuest(Request $request)
    {
        
        $guestUsername = env('GUEST_USERNAME');
        $guestPassword = env('GUEST_PASSWORD');
        $user = User::where('email', $guestUsername)->first();

        if (!$user) {

            return response()->error('Unauthorized', 403);
            
        }
        if (!$user || !Hash::check($guestPassword, $user->password)) {

            return response()->error('Unauthorized', 403);
            
        }

        $accessToken = $user->createToken($user->name)->plainTextToken;

        $user_info = [
            'token' => $accessToken,
        ];

        return response()->success($user_info);
    }

    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->success(new UserResource($user));
    }

    public function deauthenticate(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->success(['message' => __('messages.success_logout')]);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('approve-users'))
        {
            return response()->error('Unauthorized',403); 
        }

        $validator = Validator::make($request->all(),[
            'status' => Rule::in(array_column(UserStatus::cases(),'value')),
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'), 422, $validator->messages()->toArray());
        }

        $aims_user = User::find($id);

        if (!$aims_user) {
            return response()->error(__('messages.not_found'),404);
        }

        if (!$request->user()->hasRole(CoreRoles::SuperAdministrator->value) 
            && ($aims_user->current_organisation_id != $request->user()->current_organisation_id)
        ) {
            return response()->error(__('messages.unauthorized'),403);
        }

        $aims_user->status = $request->status;
        $aims_user->save();

        return response()->success(['message' => __('messages.success')]);
    }

    public function updateUser(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('approve-users'))
        {
            return response()->error('Unauthorized',403); 
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'organisation' => 'nullable|exists:organisations,id',
            'role' => ["required", Rule::in(['Subscriber','Manager','Contributor', 'Super Administrator'])],
            'language' => 'required|exists:languages,code'
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        if ($request->role == CoreRoles::SuperAdministrator->value) {

            if (!$request->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
                return response()->error('Unauthorized',403); 
            }
        }

        $aims_user = User::find($id);

        if (!$aims_user) {
            return response()->error(__('messages.not_found'),404);
        }

        $aims_user->name = $request->name;
        if ($request->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            $aims_user->email = $request->email;
            $aims_user->status = $request->status;
        }
        $aims_user->current_organisation_id = ($request->organisation) ? $request->organisation : null;
        $aims_user->language = $request->language;
        $aims_user->save();

        $organisation = Organisation::find($request->organisation);

        $aims_user->syncRoles([$request->role],$organisation); 

        return response()->success(new UserResource($aims_user));
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('view-users'))
        {
            return response()->error('Unauthorized',403); 
        }

        $aims_user = User::find($id);

        if (!$aims_user) {
            return response()->error(__('messages.not_found'),404);
        }

        return response()->success(new UserResource($aims_user));
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->isAbleTo('delete-users'))
        {
            return response()->error('Unauthorized',403); 
        }

        $aims_user = User::find($id);

        if (!$aims_user) {
            return response()->error(__('messages.not_found'),404);
        }

        if ($request->user()->id == $id) {
            return response()->error('Unauthorized',403); 
        }

        //check if user has data
        

        $aims_user->delete();
        return response()->success(__('messages.success_deleted'));
    }

    public function listOrganisationUsers(Request $request)
    {
        if (!$request->user()->isAbleTo('view-users'))
        {
            return response()->error('Unauthorized',403); 
        }

        if ($request->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            
            //todo add pagination
            return response()->success(UserResource::collection(User::paginate(50)));
        }

        return response()->success(UserResource::collection(User::where('current_organisation_id',$request->user()->current_organisation_id))->get());
    }

    public function passwordReset(Request $request, User $user)
    {
        if (!$request->hasValidSignature()) {
            return redirect()->away(Config::get('app.frontend-url')."/error");
        }
        return redirect()->away(Config::get('app.frontend-url')."/reset/password/" . $user->id);
    }

    public function sendPasswordResetLink(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        $user = User::where('email', $request->email)->first();

        try {
            $user->notify(new UserPasswordResetEmail());

            PasswordResetRequest::updateOrCreate(
                ['user_id' => $user->getKey()],
                ['expires_at' => Carbon::now()->addMinutes(Config::get('auth.passwords.users.expire'), 60)]
            );

            return response()->success(__('messages.email_sent'));
        }catch(\Exception $e) {
            return response()->error($e->getMessage(), 500);
        }
        
    }

    public function updatePassword(Request $request, User $user)
    {
        $validator = Validator::make($request->all(),[
            'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        if (!$this->userHasValidPasswordResetRequest($user)) {
            return response()->error(__('messages.error'), 422, ['email' => [0 => 'invalid password reset link, try requesting password reset']]);
        }

        $user->update(['password' => Hash::make($request->password)]);
        $user->passwordResetRequest()->update(['expires_at' => now()->subMinute()]);

        return response()->success(__('messages.success'));
    }

    public function listSuperAdministrators(Request $request)
    {
        if (!$request->user()->hasRole(CoreRoles::SuperAdministrator->value)) {
            return response()->error('Unauthorized',403); 
        }
        return response()->success(UserResource::collection(User::whereRoleIs(CoreRoles::SuperAdministrator->value)->get()));
    }

    public function createSuperAdministrator(Request $request)
    {
        if (!$request->user()->hasRole(CoreRoles::SuperAdministrator->value)) 
        {
            return response()->error('Unauthorized',403); 
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'email' => 'email|required|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ],
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        $user = new User;
        $user->email = $request->email;
        $user->name = $request->name;
        $user->language = 'en';
        $user->two_factor_authentication = 1;
        $user->status = UserStatus::Approved;
        $user->password = Hash::make($request->password);
        $user->save();

        $user->attachRole(CoreRoles::SuperAdministrator->value);

        return response()->success(new UserResource($user));
    }
    


    private function userHasValidPasswordResetRequest(User $user) : bool
    {
        if (!$user->passwordResetRequest()->exists()) return false;
        if (now() > $user->passwordResetRequest->expires_at) return false;

        return true;
    }

}