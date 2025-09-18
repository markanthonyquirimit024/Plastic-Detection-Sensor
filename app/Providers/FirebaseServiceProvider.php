<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('firebase.database', function () {
            $serviceAccount = base_path(config('firebase.credentials.file'));

            return (new Factory)
                ->withServiceAccount($serviceAccount)
                ->withDatabaseUri(config('firebase.database.url'))
                ->createDatabase();
        });
    }
}
