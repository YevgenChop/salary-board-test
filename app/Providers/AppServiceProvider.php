<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\GithubUser\GitHubUserService;
use App\Services\GithubUser\GitHubUserServiceInterface;

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
        $this->app->bind(
            GitHubUserServiceInterface::class,
            GitHubUserService::class
        );
    }
}
