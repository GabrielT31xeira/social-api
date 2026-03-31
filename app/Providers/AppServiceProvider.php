<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Policies\CommentPolicy;
use App\Policies\PostPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(Comment::class, CommentPolicy::class);

        Route::bind('post', fn (string $value) => Post::query()
            ->whereKey($value)
            ->firstOr(fn () => throw new NotFoundHttpException(__('post.error.not_found'))));

        Route::bind('comment', fn (string $value) => Comment::query()
            ->whereKey($value)
            ->firstOr(fn () => throw new NotFoundHttpException(__('comment.error.not_found'))));

        Route::bind('user', fn (string $value) => User::query()
            ->whereKey($value)
            ->firstOr(fn () => throw new NotFoundHttpException(__('auth.user_not_found'))));

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(10)->by($request->ip());
        });

        RateLimiter::for('reactions', function (Request $request) {
            return Limit::perMinute(60)->by((string) optional($request->user())->id ?: $request->ip());
        });
    }
}
