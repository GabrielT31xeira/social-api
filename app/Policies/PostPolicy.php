<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    public function delete(User $user, Post $post): Response
    {
        return (string) $user->getKey() === (string) $post->user_id
            ? Response::allow()
            : Response::deny(__('errors.forbidden'));
    }
}
