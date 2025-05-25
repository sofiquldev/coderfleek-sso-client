<?php

namespace CoderFleek\SSO;

use CoderFleek\SSO\Contracts\SsoClient;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use CoderFleek\SSO\Exceptions\SsoAuthenticationException;
use CoderFleek\SSO\Exceptions\SsoTokenException;

/**
 * CoderFleek SSO Client Manager
 * 
 * This class handles the core SSO functionality including authentication,
 * token management, and user session handling.
 * 
 * @package CoderFleek\SSO
 */
class SsoClientManager implements SsoClient
{
    /**
     * Configuration array containing SSO settings
     *
     * @var array
     */
    protected $config;

    /**
     * Create a new SSO client manager instance
     *
     * @param array $config Configuration array from cf-sso config file
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }    /**
     * Generate and return SSO login redirect response
     * 
     * This method:
     * 1. Generates a random state token for CSRF protection
     * 2. Stores the state in session
     * 3. Builds the authorization URL with required parameters
     * 4. Returns redirect response to SSO server
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect()
    {
        // Generate random state for CSRF protection
        $state = Str::random(32);
        session(['cf_state' => $state]);

        // Build query parameters
        $query = http_build_query([
            'app_id' => $this->config['app_id'],
            'redirect_uri' => $this->config['redirect_uri'],
            'state' => $state
        ]);

        // Return redirect to SSO server
        return redirect($this->config['server_url'] . '/auth/sso/initiate?' . $query);
    }    /**
     * Verify SSO callback and retrieve user data
     * 
     * This method:
     * 1. Validates the state parameter against stored state (CSRF protection)
     * 2. Verifies the SSO token with the SSO server
     * 3. Stores the authentication data in session
     * 4. Returns the authenticated user data
     *
     * @param \Illuminate\Http\Request $request The callback request containing state and sso_token
     * @return array User data from SSO server
     * @throws SsoAuthenticationException If authentication fails or state is invalid
     */
    public function user($request)
    {
        // Validate state parameter for CSRF protection
        if ($request->state !== session('cf_state')) {
            throw new SsoAuthenticationException('Invalid state parameter');
        }

        try {
            // Verify SSO token with server
            $response = Http::post($this->config['server_url'] . '/api/v1/auth/sso/verify', [
                'sso_token' => $request->sso_token,
                'app_id' => $this->config['app_id'],
                'app_secret' => $this->config['app_secret']
            ]);

            if ($response->successful()) {
                // Store authentication data and return user
                $data = $response->json();
                $this->storeAuthData($data);
                return $data['user'];
            }

            throw new SsoAuthenticationException($response->json('error_description', 'Authentication failed'));
        } catch (\Exception $e) {
            throw new SsoAuthenticationException($e->getMessage());
        }
    }    /**
     * Handle SSO logout
     * 
     * This method:
     * 1. Attempts to notify SSO server about logout (if token exists)
     * 2. Clears all local SSO session data
     * 3. Logs any errors but continues with local logout
     *
     * Note: Local logout proceeds even if SSO server logout fails
     */
    public function logout()
    {
        try {
            // Notify SSO server about logout if we have a token
            if (session('cf_access_token')) {
                Http::withToken(session('cf_access_token'))
                    ->post($this->config['server_url'] . '/api/v1/auth/logout');
            }
        } catch (\Exception $e) {
            // Log error but continue with local logout
            \Log::error('SSO Logout Error: ' . $e->getMessage());
        }

        // Always clear local session data
        $this->clearAuthData();
    }    /**
     * Refresh an expired access token
     * 
     * This method:
     * 1. Sends refresh token to SSO server
     * 2. Validates the response
     * 3. Updates local session with new tokens
     *
     * @param string $token The refresh token to use
     * @return bool True if refresh was successful
     * @throws SsoTokenException If token refresh fails
     */
    public function refreshToken($token)
    {
        try {
            // Request new tokens from SSO server
            $response = Http::post($this->config['server_url'] . '/api/v1/auth/token/refresh', [
                'refresh_token' => $token,
                'app_id' => $this->config['app_id'],
                'app_secret' => $this->config['app_secret']
            ]);

            if ($response->successful()) {
                // Store new tokens in session
                $data = $response->json();
                $this->storeAuthData($data);
                return true;
            }

            throw new SsoTokenException('Token refresh failed');
        } catch (\Exception $e) {
            throw new SsoTokenException($e->getMessage());
        }
    }    /**
     * Store SSO authentication data in session
     * 
     * Stores the following data:
     * - Access token for API requests
     * - Refresh token for getting new access tokens
     * - Session ID from SSO server
     * - User data
     *
     * @param array $data Authentication data from SSO server
     * @return void
     */
    protected function storeAuthData(array $data): void
    {
        session([
            'cf_access_token' => $data['access_token'],
            'cf_refresh_token' => $data['refresh_token'],
            'cf_session_id' => $data['session_id'],
            'cf_user' => $data['user']
        ]);
    }

    /**
     * Clear all SSO-related data from session
     * 
     * Removes:
     * - Access token
     * - Refresh token
     * - Session ID
     * - User data
     * - State parameter
     *
     * @return void
     */
    protected function clearAuthData(): void
    {
        session()->forget([
            'cf_access_token',
            'cf_refresh_token',
            'cf_session_id',
            'cf_user',
            'cf_state'
        ]);
    }
}
