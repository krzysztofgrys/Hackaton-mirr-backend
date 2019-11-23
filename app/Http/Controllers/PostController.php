<?php

namespace App\Http\Controllers;

use App\Address;
use App\Category;
use App\Post;
use App\User;
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
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort(404);
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
        $post = Post::create([
            'title' => $request->post('title'),
            'description' => $request->post('title'),
            'external' => $request->post('external'),
            'start_at' => Carbon::parse($request->post('start_at')),
            'end_at' => Carbon::parse($request->post('end_at')),
            'name' => $request->post('name'),
            'phone' => phone($request->post('phone')),
            'email' => $request->post('email'),
        ]);
        $post->user()->associate($user);
        if ($post->external) {
            $requestAddress = $request->post('address');
            $address = Address::create([
                'city' => $requestAddress['city'],
                'zip_code' => $requestAddress['zip_code'],
                'street' => $requestAddress['street'],
                'house_number' => $requestAddress['house_number'],
                'coordinates' => new \Grimzy\LaravelMysqlSpatial\Types\Point($requestAddress['lat'], $requestAddress['lng']),
            ]);
            $post->address()->associate($address);
        } else {
            $post->address()->associate($user->address);
        }

        $category = Category::findOrFail($request->post('category_id'));
        $post->category()->associate($category);
        $post->save();
        if ($request->has('tags')) {
            $post->tags()->sync($request->post('tags'));
        }
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

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        abort(404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Post $post)
    {
        abort(404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Post $post
     * @return \Illuminate\Http\Response
     */
    public function destroy(Post $post)
    {
        abort(404);
    }
}
