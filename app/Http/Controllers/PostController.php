<?php

namespace App\Http\Controllers;

use App\Address;
use App\Jobs\AddAltToMedia;
use App\Post;
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
        $searchQuery = $request->input('query');
        $tags = $this->getIdsFromParam($request, 'tags');
        $categories = $this->getIdsFromParam($request, 'categories');
        $userCoordinates = $request->user()->address->coordinates;
        $lat = $request->input('lat', $userCoordinates->getLat());
        $lng = $request->input('lng', $userCoordinates->getLng());
        $distance = $request->input('distance', 20000);

        $query = Post::query();
        if ($searchQuery) {
            $ids = Post::search($searchQuery)->get()->pluck('id');
            $query->whereIn('id', $ids);
        }
        if ($tags->isNotEmpty()) {
            $query->tags($tags);
        }
        if ($categories->isNotEmpty()) {
            $query->categories($categories);
        }

        $query->distance($lat, $lng)
            ->distanceWithin($lat, $lng, $distance);
        $query->orderBy('distance');

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
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestAddress = $request->post('address');
        $address = Address::create([
            'city' => $requestAddress['city'],
            'street' => $requestAddress['street'],
            'coordinates' => new Point($requestAddress['lat'], $requestAddress['lng']),
        ]);

        /**
         * @var $post Post
         */
        $post = Post::make([
            'title' => $request->post('title'),
            'description' => $request->post('title'),
            'end_at' => Carbon::parse($request->post('end_at')),
            'name' => $request->post('name'),
            'phone_number' => phone($request->post('phone'), 'pl'),
            'email' => $request->post('email'),
            'user_id' => $request->user()->id,
            'category_id' => $request->post('category_id'),
            'address_id' => $address->id,
        ]);

        $post->save();
        if ($request->has('tags')) {
            $post->tags()->sync($request->post('tags'));
        }

        if ($request->has('photo')) {
            $post->addMediaFromBase64($request->input('photo'))->toMediaCollection();
            $this->dispatch(new AddAltToMedia($post));
        }

        return response($post, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Post $post
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Post $post)
    {
        return $post;
    }

    public function getNumber(Post $post)
    {
        activity()->log('Phone number lookup for post ' . $post->id);

        return $post->phone_number;
    }
}
