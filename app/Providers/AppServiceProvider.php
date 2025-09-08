<?php

// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\UserAd;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        View::composer('partials.sidebar', function ($view) {
            $user = Auth::user();
            $count = 0;

            if ($user && $user->role?->name) {
                $roleLower = mb_strtolower($user->role->name, 'UTF-8');

                // إن رغبت بتقييد المدير لإعلانات تخص معهده فقط، أضف whereHas على ad()->institute_id هنا.
                $count = UserAd::unreadForRole($roleLower)->count();
            }

            $view->with('inboxUnreadCount', $count);
        });
    }
}
