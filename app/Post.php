<?php

namespace App;

use Grimzy\LaravelMysqlSpatial\Eloquent\Builder;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Post extends Model implements HasMedia
{
    use HasMediaTrait, Searchable;

    protected $appends = [
        'address', 'tags', 'category'
    ];



    protected $fillable = ['title', 'description', 'external', 'start_at', 'end_at', 'name', 'phone', 'email', 'user_id', 'category_id', 'address_id'];

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'address_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'posts_tags');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getAddressAttribute(): ?Address
    {
        return $this->address()->first();
    }

    public function getTagsAttribute(): Collection
    {
        return $this->tags()->get();
    }

    public function getCategoryAttribute(): Category
    {
        return $this->category()->first();
    }

    public function scopeDistance($query, $lat, $lng)
    {
        return $query->selectRaw(
            'posts.*, round(st_distance_sphere(addresses.coordinates, ST_GeomFromText(?))) as distance',
            [(new Point($lat, $lng))->toWKT()]
        )->join('addresses', 'posts.address_id', '=', 'addresses.id');
    }

    public function scopeDistanceWithin($query, $lat, $lng, $distance)
    {
        return $query->whereHas('address', function(Builder $query) use ($lat, $lng, $distance) {
            $query->distanceSphere('coordinates', new Point($lat, $lng), (int) $distance);
        });
    }

    public function scopeTags($query, $tags)
    {
        return $query->whereHas('tags', function(Builder $query) use ($tags) {
            $query->whereIn('id', $tags);
        });
    }

    public function scopeCategories($query, $categories)
    {
        return $query->whereHas('categories', function(Builder $query) use ($categories) {
            $query->whereIn('id', $categories);
        });
    }

    public function toArray()
    {
        $media = $this->getFirstMedia();
        $array = parent::toArray();
        $array['photo'] = $media ? $media->getFullUrl() : '';
        $array['photo_alt'] = $media ? $media->getCustomProperty('alt') : '';
        $array['phone_number'] = substr($this->phone_number, 0, 6);
        return $array;
    }

    public function searchableAs()
    {
        return 'posts';
    }

    public function toSearchableArray()
    {
        $address = $this->address()->first();
        return [
            'title' => $this->title,
            'description' => $this->description,
            'city' => $address ? $address->city : '',
            'street' => $address ? $address->street : '',
        ];
    }
}
