<?php

namespace CoderFleek\SSO\Contracts;

/**
 * SSO Authenticatable Interface
 * 
 * This interface should be implemented by User models that need to support
 * SSO authentication. It provides methods to manage the SSO identifier
 * that links the local user account with the SSO server's user account.
 * 
 * @package CoderFleek\SSO\Contracts
 */
interface SsoAuthenticatable
{
    /**
     * Get the SSO identifier for the user
     * 
     * This should return the unique identifier that links
     * this user with their SSO account.
     * 
     * @return mixed
     */
    public function getSsoIdentifier();

    /**
     * Set the SSO identifier for the user
     * 
     * This should store the unique identifier that links
     * this user with their SSO account.
     * 
     * @param mixed $identifier
     * @return void
     */
    public function setSsoIdentifier($identifier);
}
