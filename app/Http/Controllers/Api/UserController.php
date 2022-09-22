<?php

namespace App\Http\Controllers\Api;

use App\Enums\CoreRoles;
use App\Enums\UserStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\OrganisationResource;
use App\Http\Resources\UserResource;
use App\Mail\SignupStarted;
use App\Models\EmailVerification;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum'])->only('profile','deauthenticate','updateStatus','updateUser','show','listOrganisationUsers');
        $this->middleware('isapproved')->only('updateStatus','updateUser','show','listOrganisationUsers');
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
        
        $aims_user = new User;
        $aims_user->name = $request->name;
        $aims_user->email = $request->email;
        $aims_user->password = Hash::make($request->password);
        $aims_user->current_organisation_id = $request->organisation;
        $aims_user->save();

        $proposed_organisation = Organisation::find($request->organisation);

        $aims_user->attachRoles([$request->role],$proposed_organisation);

        $organisation_users = $proposed_organisation->users;
        
        if ($organisation_users->count() === 1) {
            //first organisation user and can be approved automatically
            $aims_user->status = UserStatus::Approved;
            $aims_user->save();
        }

        if ($organisation_users->count() > 1) {
            // email users in the organisation1=
            $users_except_applicant = $organisation_users->where('id','!=',$aims_user->id);
            Log::debug(['organisation_users' => $users_except_applicant]);
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

    public function profile(Request $request)
    {
        $user = $request->user();
        return response()->success(new UserResource($user));
    }

    public function deauthenticate()
    {
        auth()->user()->tokens()->delete();

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
        if (!$request->user()->isAbleTo('update-users'))
        {
            return response()->error('Unauthorized',403); 
        }

        $validator = Validator::make($request->all(),[
            'name' => 'required',
            'organisation' => 'required|exists:organisations,id',
            'role' => 'required|in:Subscriber,Manager,Contributor'
        ]);

        if ($validator->fails()) {
			
			return response()->error(__('messages.invalid_request'),422,$validator->messages()->toArray());
        }

        $aims_user = User::find($id);

        if (!$aims_user) {
            return response()->error(__('messages.not_found'),404);
        }

        $aims_user->name = $request->name;
        $aims_user->current_organisation_id = $request->organisation;
        $aims_user->save();

        $organisation = Organisation::find($request->organisation);

        $aims_user->syncRoles($request->role,$organisation); 

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
            return response()->success(UserResource::collection(User::all()));
        }

        return response()->success(UserResource::collection(User::where('current_organisation_id',$request->user()->current_organisation_id))->get());
    }

}