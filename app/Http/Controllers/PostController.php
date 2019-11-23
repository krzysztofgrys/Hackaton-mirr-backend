<?php

namespace App\Http\Controllers;

use App\Address;
use App\Jobs\AddAltToMedia;
use App\Post;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $requestAddress = $request->post('address');
        $address = Address::create([
            'city' => $requestAddress['city'],
            'zip_code' => $requestAddress['zip_code'],
            'street' => $requestAddress['street'],
            'house_number' => $requestAddress['house_number'],
            'coordinates' => new \Grimzy\LaravelMysqlSpatial\Types\Point($requestAddress['lat'], $requestAddress['lng']),
        ]);

        /**
         * @var $post Post
         */
        $post = Post::make([
            'title' => $request->post('title'),
            'description' => $request->post('title'),
            'start_at' => Carbon::parse($request->post('start_at')),
            'end_at' => Carbon::parse($request->post('end_at')),
            'name' => $request->post('name'),
            'phone' => phone($request->post('phone'), 'pl'),
            'email' => $request->post('email'),
            'user_id' => $request->user()->id,
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
