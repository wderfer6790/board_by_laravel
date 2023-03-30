<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Eloquent\Relations\Relation;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*Relation::enforceMorphMap([
            'user' => 'App\Models\User',
            'article' => 'App\Models\Article',
            'reply' => 'App\Models\Reply',
        ]);*/
    }
}
