<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class OverrideServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $toOverride = collect([
            // Status Filter
            \Statamic\Query\Scopes\Filters\Status::class =>
                \App\Overrides\Query\Scopes\Filters\Status::class,

            // Entry
            \Statamic\Entries\Entry::class =>
                \App\Overrides\Entries\Entry::class,

            // Publish Action
            \Statamic\Actions\Publish::class =>
                \App\Overrides\Actions\Publish::class,

            // Unpublish Action
            \Statamic\Actions\Unpublish::class =>
                \App\Overrides\Actions\Unpublish::class,

            // EntryPolicy
            \Statamic\Policies\EntryPolicy::class =>
                \App\Overrides\Policies\EntryPolicy::class,
        ]);

        $toOverride->each(function($classOverriding, $classToOverride) {
            $this->app->singleton(
                $classToOverride,
                $classOverriding
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
