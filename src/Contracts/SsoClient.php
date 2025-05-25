<?php

namespace CoderFleek\SSO\Contracts;

/**
 * SSO Client Interface
 * 
 * Defines the contract for implementing SSO client functionality.
 * Any class implementing this interface must provide methods for:
 * - Initiating SSO login
 * - Processing user authentication
 * - Handling logout
 * - Managing token refresh
 * 
 * @package CoderFleek\SSO\Contracts
 */
interface SsoClient
{
    /**
     * Get SSO login redirect response
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirect();

    /**
     * Process SSO user authentication
     * 
     * @param mixed $request The incoming request with SSO data
     * @return mixed The authenticated user data
     */
    public function user($request);

    /**
     * Handle SSO logout
     * 
     * @return void
     */
    public function logout();

    /**
     * Refresh SSO access token
     * 
     * @param string $token The refresh token
     * @return bool True if refresh was successful
     */
    public function refreshToken($token);
}
