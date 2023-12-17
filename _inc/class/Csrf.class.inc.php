<?php

class Csrf
{
    /**
     * The key of the token in the request.
     * @var string
     */
    public const REQUEST_KEY = '_token';

    /**
     * The key of the token in the session.
     * @var string
     */
    private const SESSION_KEY = 'CSRF-TOKEN';

    /**
     * The key of the token in the headers.
     * @var string
     */
    private const HEADER_KEY = 'X-CSRF-TOKEN';

    /**
     * Token length.
     * @var string
     */
    private const TOKEN_LENGTH = 40;

    /**
     * Initialize the token in the session.
     *
     * @return void
     */
    public static function init()
    {
        if (!self::get_token_from_session()) {
            $_SESSION[self::SESSION_KEY] = self::generate_token();
        }
    }

    /**
     * Verify CSRF token.
     *
     * @return bool
     */
    public static function verify()
    {
        if (!self::should_verify_csrf()) {
            return true;
        }

        return self::tokens_match();
    }

    /**
     * Get the token.
     *
     * @return string|null
     */
    public static function token()
    {
        if (!self::get_token_from_session()) {
            self::init();
        }

        return self::get_token_from_session();
    }

    /**
     * Check if a csrf token should be verified in the current request.
     *
     * @return bool
     */
    private static function should_verify_csrf()
    {
        return !in_array($_SERVER['REQUEST_METHOD'], ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Check if the session token and the request token match.
     *
     * @return bool
     */
    private static function tokens_match()
    {
        $session_token = self::get_token_from_session();
        $request_token = self::get_token_from_request();

        return is_string($session_token)
            && is_string($request_token)
            && hash_equals($session_token, $request_token);
    }

    /**
     * Return the token from the request.
     *
     * @return string|null
     */
    private static function get_token_from_request()
    {
        return $_REQUEST[self::REQUEST_KEY] ?? self::get_token_from_headers();
    }

    /**
     * Return the token from the request headers.
     *
     * @return string|null
     */
    private static function get_token_from_headers()
    {
        return $_SERVER['HTTP_' . str_replace('-', '_', self::HEADER_KEY)] ?? null;
    }

    /**
     * Return the token from the session.
     *
     * @return string|null
     */
    private static function get_token_from_session()
    {
        return $_SESSION[self::SESSION_KEY] ?? null;
    }

    /**
     * Generate a random CSRF token.
     *
     * @return string|null
     */
    private static function generate_token()
    {
        return str_random(self::TOKEN_LENGTH);
    }
}