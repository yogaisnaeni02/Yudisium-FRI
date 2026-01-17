<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\User;
use App\Observers\UserObserver;

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
        // Share student data to dashboard layout if user is a student
        View::composer('layouts.dashboard', function ($view) {
            if (Auth::check() && Auth::user()->role === 'student') {
                $student = Student::where('user_id', Auth::user()->id)->first();
                $view->with('student', $student);
            }
        });

        // Observe User model
        User::observe(UserObserver::class);
    }
}
