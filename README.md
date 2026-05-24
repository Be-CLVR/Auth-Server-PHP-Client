# Brendan&MacKenzie - Auth Server PHP Client

Brendan&MacKenzie's Auth Server PHP Client is a package to connect Laravel API projects to the Authentication Server of Brendan&MacKenzie.

## Requirements
* An Auth Server Client Token
* PHP 7.3 or higher
* firebase/php-jwt ^6.4
* guzzlehttp/guzzle ^7.5


## Installation ###
1. Run `composer require be-clvr/auth-server-php-client` inside your Laravel project.
2. Add your Auth Server Client Token to your environment file, for example: `AUTH_SERVER_TOKEN=[YOUR TOKEN]`.
3. Optional: if you would like to publish the configuration file for this package you can do so by entering:
`php artisan vendor:publish --provider="BeCLVR\AuthServerClient\AuthServerServiceProvider"`
4. Paste the Auth Server public key in your `storage/app` folder.

And your done! You're now connect to the auth server.

## Configuration ##

### Register users ##
You can register users on the auth server with your application (client) inside your project. All you need to do is add the following logic inside the file where you want to register new users to the Auth Server.

```
// Import the AuthServer.
use BeCLVR\AuthServerClient\AuthServer;

class {

    // Inject the AuthServer in your class.
    private $authServerClient;

    public function __construct(AuthServer $authServerClient
    {
        $this->authServerClient = $authServerClient;
    }

    // Use the register function inside your logic to register an user to the auth server.
    public function function() {
        $authProfile = $this->authServerClient->register($user, $initialUser, $externalTid);

        // If you want to retrieve the profile_id from the user
        $authProfile->getProfileId();
    
        // If you want to retrieve the reset token to set the users password with
        $authProfile->getToken();
    }
}
```

### Adding the middleware ###
You can verify and authenticate an user with the middleware included in this package. The middleware checks if the `access_token` -cookie and `profile_token` -cookie are set and valid. If the tokens are valid, the user will be logged in, in your application.

You can enable the middleware by enabling them in your middleware groups:

```
 protected $middlewareGroups = [
        'web' => [
            ...
            \BeCLVR\AuthServerClient\Http\Middleware\AuthServerValidation::class,
        ]
 ]
```

You can add the middleware to certain routes with our alias:

```
Route::group(['middleware' => ['api', 'authserver']], function () {}

Route::get('/user, [UserController::class, 'show'])->middleware('authserver');
```

Or apply the middleware from within your controller by requiring it from the constructor:

```
class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('authserver');
    }

    // other methods... (will use this middleware)
}
```

Optionally you can publish the Middleware and customize it in your own project:
`php artisan vendor:publish --tag=authserver-middleware`

If you publish this middleware, make sure you correct the namespace of the AuthServerValidation class.

## Acknowledgements ##
 - [Brendan&MacKenzie](https://www.be-clvr.com)


 ## Authors

- [@wouterdeberg](https://github.com/wouterdeberg)
- [@Be-CLVR](https://github.com/Be-CLVR)

## Issues
- [Report an issue here](https://github.com/Be-CLVR/Auth-Server-PHP-Client/issues/new)
- [List of open issues](https://github.com/Be-CLVR/Auth-Server-PHP-Client/issues)
