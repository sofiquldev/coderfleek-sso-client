<?php

namespace CoderFleek\SSO\Exceptions;

/**
 * SSO Authentication Exception
 * 
 * Thrown when there is an error during the SSO authentication process:
 * - Invalid SSO token
 * - Failed server verification
 * - Invalid state parameter
 * - Other authentication failures
 * 
 * @package CoderFleek\SSO\Exceptions
 */
class SsoAuthenticationException extends \Exception {}
