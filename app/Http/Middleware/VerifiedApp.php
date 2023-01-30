<?php

namespace App\Http\Middleware;

use App\Models\VerifiedApplication;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class VerifiedApp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$this->hasAppKey($request))
        {
            Log::info(["checkToken-none"=>$request->headers]);
            return response()->error('Unauthorized', 403);

        }
        $sent_token = $request->app_token ?? $request->header('app-token') ?? $request->header('app_token');
        $token = VerifiedApplication::where('app_token',$sent_token)->first();
        if(!$token)
        {
            Log::info(["checkToken-nomatch"=>$request->headers]);
            return response()->error('Unauthorized', 403);
        }
        if ($token->disabled) return response()->error('Unauthorized', 403);

        
        $token_ip_addresses = $token->ip_addresses;
        if ($token_ip_addresses) {
            if(!in_array($request->ip(),$token_ip_addresses))
            {
                Log::info(["checkToken-badip"=>$request->headers]);
                return response()->error('Unauthorized', 403);
                
            }
        }
        
        return $next($request);
    }

    private function hasAppKey(Request $request)
    {
        if ($request->has('app_token') || $request->hasHeader('app-token') || $request->hasHeader('app_token')) return true;
                
        return false;

    }
}
