# CoderFleek SSO Client for Laravel

A Laravel package for integrating with CoderFleek's Single Sign-On (SSO) service.

## Installation

### 1. Install the Package

```bash
composer require coderfleek/sso-client
```

### 2. Publish Configuration

```bash
php artisan vendor:publish --tag=cf-sso-install
```

### 3. Add Environment Variables

Add these variables to your `.env` file:

```env
CF_SSO_URL=https://sso.test
CF_APP_ID=your_app_id
CF_APP_SECRET=your_app_secret
CF_REDIRECT_URI=https://your-app.com/cf/auth/callback
CF_ROUTE_PREFIX=cf
CF_AUTO_REFRESH=true
CF_REFRESH_THRESHOLD=30
```

### 4. Update User Model

Update your `User` model to implement the SSO interface:

```php
use CoderFleek\SSO\Contracts\SsoAuthenticatable;

class User extends Authenticatable implements SsoAuthenticatable
{
    protected $fillable = [
        'name',
        'email',
        'sso_id',
    ];

    public function getSsoIdentifier()
    {
        return $this->sso_id;
    }

    public function setSsoIdentifier($identifier)
    {
        $this->sso_id = $identifier;
    }
}
```

### 5. Run Migrations

```bash
php artisan migrate
```

This will add the `sso_id` column to your users table.

## Configuration

The package configuration file will be published at `config/cf-sso.php`. Available options:

```php
return [
    'prefix' => env('CF_ROUTE_PREFIX', 'cf'),
    'server_url' => env('CF_SSO_URL'),
    'app_id' => env('CF_APP_ID'),
    'app_secret' => env('CF_APP_SECRET'),
    'redirect_uri' => env('CF_REDIRECT_URI'),
    'auto_refresh' => env('CF_AUTO_REFRESH', true),
    'refresh_threshold' => env('CF_REFRESH_THRESHOLD', 30),
];
```

## Usage

### Protect Routes

```php
// In routes/web.php
Route::middleware(['sso.auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
```

### Login Link

```php
<a href="{{ route('sso.login') }}">Login with SSO</a>
```

### Logout

```php
<form method="POST" action="{{ route('sso.logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
```

## Events

The package dispatches several events you can listen for:

- `SsoAuthenticated`: When a user successfully authenticates
- `SsoLoggedOut`: When a user logs out

## Security

This package includes:

- CSRF protection via state parameter
- Automatic token refresh
- Secure session handling
- Server-side token verification

## License

The MIT License (MIT). Please see LICENSE file for more information.
