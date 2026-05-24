<?php

namespace BeCLVR\AuthServerClient;

use BeCLVR\AuthServerClient\Facades\AuthServer;
use BeCLVR\AuthServerClient\Http\Middleware\AuthServerValidation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;

class AuthServerServiceProvider extends ServiceProvider
{
  public function register()
  {
    $this->app->bind('authserver', function($app) {
      return new AuthServer();
    });

    $this->mergeConfigFrom(__DIR__.'/config/authserver.php', 'authserver');
  }

  public function boot()
  {
    if ($this->app->runningInConsole()) {
      $this->publishes([
        __DIR__.'/config/authserver.php' => config_path('authserver.php'),
      ], 'config');

      $this->publishes([
        __DIR__.'/Http/Middleware/AuthServerValidation.php' => base_path('app/Http/Middleware/AuthServerValidation.php')
    ], 'authserver-middleware');
    }
    
    $router = $this->app->make(Router::class);
    $router->aliasMiddleware('authserver', AuthServerValidation::class);
  }
}