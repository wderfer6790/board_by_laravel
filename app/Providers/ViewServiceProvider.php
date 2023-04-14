<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer(['layouts.main', 'layouts.article', 'article'], function($view) {
            if(auth()->check()) {
                $view->with('user_thumbnail', auth()->user()->file->count() > 0 ? asset(auth()->user()->file->get(0)->path) : asset('storage/image/no_image.png'));
            }
        });
    }
}
