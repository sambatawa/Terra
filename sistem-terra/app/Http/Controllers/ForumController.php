<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Like;
use Illuminate\Support\Facades\Auth;

class ForumController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'comments.user', 'likes'])->latest();
        
        // Filter by kategori
        if ($request->has('category') && $request->category != 'all') {
            $query->where('category', $request->category);
        }
        
        $posts = $query->get();
        return view('forum', compact('posts'));
    }

    public function storePost(Request $request)
    {
        $request->validate([
            'title' => 'required|max:200',
            'content' => 'required',
            'category' => 'required|in:pupuk_nutrisi,pestisida_obat,benih_bibit,alat_tani,sarana_produksi,umum',
            'image' => 'image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('forum_images', 'public');
        }

        Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'content' => $request->content,
            'category' => $request->category,
            'image' => $imagePath
        ]);

        return back()->with('success', 'Diskusi berhasil diposting!');
    }

    public function storeComment(Request $request, $postId)
    {
        $request->validate(['content' => 'required']);

        Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $postId,
            'content' => $request->content
        ]);

        return back();
    }

    public function toggleLike($postId)
    {
        $like = Like::where('user_id', Auth::id())->where('post_id', $postId)->first();

        if ($like) {
            $like->delete(); // Kalau sudah like, jadi unlike
        } else {
            Like::create([
                'user_id' => Auth::id(),
                'post_id' => $postId
            ]); // Kalau belum, jadi like
        }

        return back();
    }
    
    public function deletePost($id)
    {
        $post = Post::findOrFail($id);
        if($post->user_id == Auth::id() || Auth::user()->role == 'penyuluh') {
            $post->delete();
            return back()->with('success', 'Postingan dihapus.');
        }
        return abort(403);
    }
}