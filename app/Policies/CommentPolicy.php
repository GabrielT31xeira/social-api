<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\Comment;
use App\Models\User;

class CommentPolicy
{
    public function delete(User $user, Comment $comment): Response
    {
        $comment->loadMissing('post:id,user_id');

        return (string) $user->getKey() === (string) $comment->user_id
            || (string) $user->getKey() === (string) $comment->post?->user_id
            ? Response::allow()
            : Response::deny(__('errors.forbidden'));
    }
}
