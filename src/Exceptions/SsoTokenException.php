<?php

namespace CoderFleek\SSO\Exceptions;

/**
 * SSO Token Exception
 * 
 * Thrown when there are issues with SSO tokens:
 * - Token refresh failure
 * - Invalid token format
 * - Expired tokens
 * - Missing refresh token
 * 
 * @package CoderFleek\SSO\Exceptions
 */
class SsoTokenException extends \Exception {}
