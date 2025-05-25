<?php

namespace CoderFleek\SSO\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use CoderFleek\SSO\Contracts\SsoClient;

/**
 * SSO Controller
 * 
 * Handles all SSO-related HTTP requests including:
 * - Initiating SSO login
 * - Processing SSO callback
 * - Handling SSO logout
 * 
 * @package CoderFleek\SSO\Http\Controllers
 */
class SsoController extends Controller
{
    /**
     * The SSO client instance
     *
     * @var \CoderFleek\SSO\Contracts\SsoClient
     */
    protected $ssoClient;

    /**
     * Create a new controller instance
     * 
     * @param \CoderFleek\SSO\Contracts\SsoClient $ssoClient
     */
    public function __construct(SsoClient $ssoClient)
    {
        $this->ssoClient = $ssoClient;
    }

    /**
     * Initiate SSO login process
     * 
     * Redirects the user to the SSO server's login page
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login()
    {
        return $this->ssoClient->redirect();
    }

    /**
     * Handle the SSO callback
     * 
     * This method:
     * 1. Validates the SSO response
     * 2. Authenticates the user locally
     * 3. Redirects to the intended destination
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function callback(Request $request)
    {
        try {
            $user = $this->ssoClient->user($request);
            Auth::login($user);
            
            return Redirect::intended(
                Config::get('cf-sso.default_redirect', '/dashboard')
            );
        } catch (\Exception $e) {
            return Redirect::route('sso.login')
                ->withErrors(['error' => 'SSO authentication failed']);
        }
    }

    /**
     * Handle SSO logout
     * 
     * This method:
     * 1. Logs out from SSO server
     * 2. Logs out locally
     * 3. Invalidates the session
     * 4. Regenerates CSRF token
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->ssoClient->logout();
        Auth::logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return Redirect::to('/');
    }
}
