<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Post extends Model implements HasMedia
{
    use HasMediaTrait;

    protected $appends = [
        'address'
    ];


    protected $fillable = ['title', 'description', 'external', 'start_at', 'end_at', 'name', 'phone', 'email', 'user_id', 'category_id', 'address_id'];

    public function address(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'posts_tags');
    }

    public function category(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getAddressAttribute(): Address
    {
        return $this->address()->first();
    }

    public function toArray()
    {
        $array = parent::toArray();
        $array['photo'] = $this->getFirstMedia()->getFullUrl();
        return $array;
    }
}
