<?php

namespace App\Http\Controllers;

use App\Address;
use App\Jobs\AddAltToMedia;
use App\Post;
use App\Tag;
use App\User;
use Carbon\Carbon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tags = $this->getIdsFromParam($request, 'tags');
        $categories= $this->getIdsFromParam($request, 'categories');
        $lat = $request->input('lat', null);
        $lng = $request->input('lng', null);
        $distance = $request->input('distance', 20000);

        $query = Post::query();
        if ($tags->isNotEmpty()) {
            $query->whereHas('tags', function (Builder $query) use ($tags) {
                $query->whereIn('id', $tags);
            });
        }
        if ($categories->isNotEmpty()) {
            $query->whereHas('category', function (Builder $query) use ($categories) {
                $query->whereIn('id', $categories);
            });
        }

        if ($lat && $lng) {
            $query->whereHas('address', function (Builder $query) use ($lat, $lng, $distance) {
                $query->distanceSphere('coordinates', new Point($lat, $lng), (int) $distance);
            });
        }

        return $query->get();
    }

    public function getIdsFromParam(Request $request, string $field): Collection
    {
        return collect(explode(",", $request->input($field, '')))->filter();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = User::whereApiToken($request->get('token'))->first();

        if ($request->has('external')) {
            $requestAddress = $request->post('address');
            $address = Address::create([
                'city' => $requestAddress['city'],
                'zip_code' => $requestAddress['zip_code'],
                'street' => $requestAddress['street'],
                'house_number' => $requestAddress['house_number'],
                'coordinates' => new \Grimzy\LaravelMysqlSpatial\Types\Point($requestAddress['lat'], $requestAddress['lng']),
            ]);
        } else {
            $address = $user->address;
        }

        /**
         * @var $post Post
         */
        $post = Post::make([
            'title' => $request->post('title'),
            'description' => $request->post('title'),
            'external' => $request->has('external') ? true : false,
            'start_at' => Carbon::parse($request->post('start_at')),
            'end_at' => Carbon::parse($request->post('end_at')),
            'name' => $request->post('name'),
            'phone' => phone($request->post('phone'), 'pl'),
            'email' => $request->post('email'),
            'user_id' => $user->id,
            'category_id' => $request->post('category_id'),
            'address_id' => $address->id
        ]);

        $post->save();
        if ($request->has('tags')) {
            $post->tags()->sync($request->post('tags'));
        }

        $post->addMediaFromRequest('photo')->toMediaCollection();
        $this->dispatch(new AddAltToMedia($post->getFirstMedia()));

        return response($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return $post;
    }
}
