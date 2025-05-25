<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Route Prefix
    |--------------------------------------------------------------------------
    |
    | This prefix will be used for all SSO routes. By default, it uses 'cf'
    | resulting in routes like /cf/auth/login, /cf/auth/callback, etc.
    |
    */
    'prefix' => config('cf-sso.prefix', env('CF_ROUTE_PREFIX', 'cf')),

    /*
    |--------------------------------------------------------------------------
    | SSO Server URL
    |--------------------------------------------------------------------------
    |
    | The base URL of the SSO server. This is where users will be redirected
    | to authenticate. For development, it defaults to http://sso.test
    |
    */
    'server_url' => config('cf-sso.server_url', env('CF_SSO_URL', 'http://sso.test')),

    /*
    |--------------------------------------------------------------------------
    | Application Credentials
    |--------------------------------------------------------------------------
    |
    | Your application's credentials for the SSO server. These must be obtained
    | from the SSO server administrator and kept secure.
    |
    */
    'app_id' => env('CF_APP_ID'),
    'app_secret' => env('CF_APP_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Redirect URI
    |--------------------------------------------------------------------------
    |
    | The URI where the SSO server should redirect after successful authentication.
    | This must match exactly with the redirect URI registered with the SSO server.
    |
    */
    'redirect_uri' => env('CF_REDIRECT_URI'),

    /*
    |--------------------------------------------------------------------------
    | Token Auto Refresh
    |--------------------------------------------------------------------------
    |
    | If enabled, the middleware will automatically attempt to refresh tokens
    | before they expire. This helps prevent session interruptions.
    |
    */
    'auto_refresh' => env('CF_AUTO_REFRESH', true),

    /*
    |--------------------------------------------------------------------------
    | Token Refresh Threshold
    |--------------------------------------------------------------------------
    |
    | The number of minutes before token expiration when a refresh should be
    | attempted. Default is 30 minutes before expiration.
    |
    */
    'refresh_threshold' => env('CF_REFRESH_THRESHOLD', 30), // minutes before expiration

    /*
    |--------------------------------------------------------------------------
    | Synchronized Logout
    |--------------------------------------------------------------------------
    |
    | If enabled, logging out from one application will attempt to notify the
    | SSO server to terminate the session across all connected applications.
    |
    */
    'sync_logout' => env('CF_SYNC_LOGOUT', true),

    /*
    |--------------------------------------------------------------------------
    | Session Key
    |--------------------------------------------------------------------------
    |
    | The session key used to store the SSO token. Change this if you need to
    | avoid conflicts with other packages or existing session data.
    |
    */
    'session_key' => env('CF_SESSION_KEY', 'cf_sso_token'),

    /*
    |--------------------------------------------------------------------------
    | Default Redirect
    |--------------------------------------------------------------------------
    |
    | The default path to redirect to after successful authentication when no
    | intended URL is stored. Typically this would be your dashboard.
    |
    */
    'default_redirect' => env('CF_DEFAULT_REDIRECT', '/dashboard'),

    /*
    |--------------------------------------------------------------------------
    | Documentation
    |--------------------------------------------------------------------------
    |
    | This section is for documenting the configuration options.
    |
    */
    'documentation' => [
        'route_prefix' => 'This is the prefix used for all SSO routes.',
        'server_url' => 'The base URL of the SSO server for authentication.',
        'app_id' => 'Your application\'s ID for the SSO server.',
        'app_secret' => 'Your application\'s secret for the SSO server.',
        'redirect_uri' => 'The URI to redirect to after successful authentication.',
        'auto_refresh' => 'Whether to automatically refresh tokens before they expire.',
        'refresh_threshold' => 'The number of minutes before token expiration to attempt a refresh.',
        'sync_logout' => 'Whether to synchronize logout across connected applications.',
        'session_key' => 'The session key used to store the SSO token.',
        'default_redirect' => 'The default path to redirect to after successful authentication.',
    ],
];
