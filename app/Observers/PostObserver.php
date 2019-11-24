<?php

namespace App\Observers;

use App\Notifications\NewPostNearby;
use App\Post;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Log;

class PostObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  \App\Post $post
     *
     * @return void
     */
    public function created(Post $post)
    {

        Log::info(' post ' . $post->address->coordinates->toWKT());
        Log::info(' user' . User::first()->address->coordinates->toWKT());

        /** @var User[] $users */
        $users = User::whereHas('address', function(Builder $builder) use ($post) {
            $builder->distanceSphere('coordinates', $post->address->coordinates, 20000);
        })->get();
        foreach ($users as $user) {
            Log::info("Notyfing user {$user->id} about post {$post->id}");
            $user->notify(new NewPostNearby($post));
        }
    }
}
