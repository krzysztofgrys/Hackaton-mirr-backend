<?php

namespace App\Observers;

use App\Notifications\NewPostNearby;
use App\Post;
use App\User;
use Illuminate\Database\Eloquent\Builder;

class PostObserver
{
    /**
     * Handle the post "created" event.
     *
     * @param  \App\Post  $post
     * @return void
     */
    public function created(Post $post)
    {
        /** @var User[] $users */
        $users = User::whereHas('address', function(Builder $builder) use ($post) {
           $builder->distanceSphere('coordinates', $post->address->coordinates, 20000);
        });
        foreach ($users as $user) {
            $user->notify(new NewPostNearby($post));
        }
    }
}
