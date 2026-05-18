<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::connection('mongodb')->table('faculty_profiles', function (Blueprint $collection): void {
            $collection->index('user_id');
            $collection->index('department');
            $collection->index('xp');
        });

        Schema::connection('mongodb')->table('evaluations', function (Blueprint $collection): void {
            $collection->index('faculty_id');
            $collection->index('status');
            $collection->index('created_at');
        });

        Schema::connection('mongodb')->table('wellbeing_surveys', function (Blueprint $collection): void {
            $collection->index('faculty_id');
            $collection->index('surveyed_at');
            $collection->index('burnout_index');
        });
    }
}
