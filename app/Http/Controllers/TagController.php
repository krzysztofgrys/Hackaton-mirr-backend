<?php

namespace App\Http\Controllers;

use App\Tag;

class TagController extends Controller
{
    public function index()
    {
        return Tag::all();
    }
}
