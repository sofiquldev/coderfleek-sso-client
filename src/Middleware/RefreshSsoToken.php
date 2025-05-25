<?php

namespace CoderFleek\SSO\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use CoderFleek\SSO\Contracts\SsoClient;
use CoderFleek\SSO\Exceptions\SsoTokenException;

/**
 * SSO Token Refresh Middleware
 * 
 * This middleware handles SSO token validation and refresh:
 * - Checks if there's a valid SSO session
 * - Automatically refreshes tokens when they're about to expire
 * - Redirects to SSO login when session is invalid
 * 
 * @package CoderFleek\SSO\Middleware
 */
class RefreshSsoToken
{
    /**
     * The SSO client instance
     *
     * @var \CoderFleek\SSO\Contracts\SsoClient
     */
    protected $ssoClient;

    /**
     * Create a new middleware instance
     *
     * @param \CoderFleek\SSO\Contracts\SsoClient $ssoClient
     */
    public function __construct(SsoClient $ssoClient)
    {
        $this->ssoClient = $ssoClient;
    }

    /**
     * Handle an incoming request
     * 
     * This method:
     * 1. Validates the SSO session
     * 2. Refreshes the token if needed
     * 3. Redirects to login if session is invalid
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $config = Config::get('cf-sso');
        
        if (!$this->hasValidSsoSession()) {
            if (!$request->is('auth/callback')) {
                return $this->ssoClient->redirect();
            }
        }

        // Check if token needs refresh
        if ($config['auto_refresh'] && $this->shouldRefreshToken()) {
            try {
                $this->ssoClient->refreshToken(session('cf_refresh_token'));
            } catch (SsoTokenException $e) {
                return $this->ssoClient->redirect();
            }
        }
        
        return $next($request);
    }
    
    /**
     * Check if there's a valid SSO session
     *
     * Validates presence of required SSO session data:
     * - Access token
     * - Refresh token
     * - User data
     * 
     * @return bool
     */
    protected function hasValidSsoSession(): bool
    {
        return session()->has('cf_access_token') && 
               session()->has('cf_refresh_token') && 
               session()->has('cf_user');
    }

    /**
     * Check if the current token needs to be refreshed
     * 
     * Compares token expiration time with refresh threshold
     * configured in cf-sso settings
     *
     * @return bool
     */
    protected function shouldRefreshToken(): bool
    {
        // Implementation Note:
        // This is currently a placeholder. In a production environment,
        // you should implement proper token expiration checking logic here.
        // For example, checking JWT expiration time or stored token metadata.
        return false;
    }
}
