<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;


class PostController extends Controller
{
    use AuthorizesRequests;

    use AuthorizesRequests;

    public function index()
    {
        $posts = Post::with(['user', 'comments.user'])->latest()->get();
        return Inertia::render('Blog/Index', ['posts' => $posts]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Post $post)
    {
        $this->authorize('update', $post);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $post->update($request->all());
        return redirect()->back();
    }

    public function destroy(Post $post)
    {
        $this->authorize('update', $post);
        $post->delete();
        return redirect()->back();
    }
}