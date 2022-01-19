<?php

use App\Models\Blog;
use App\Repositories\BlogRepository;
use Illuminate\Support\Facades\Route;

/**
 * For the twill demo we put all logic here. But ideally you would use controllers.
 */

Route::get('/', static function () {
    $blogs = Blog::published()->limit(10)->orderBy('created_at', 'desc')->get();
    return view('home', ['blogs' => $blogs]);
})->name('home');

Route::get('/blogs/{slug}', static function (string $blogSlug) {
    $blog = app(BlogRepository::class)->forSlug($blogSlug);
    if (!$blog || !$blog->published()) {
        abort(404);
    }
    return view('site.blog', ['item' => $blog]);
})->name('blog.detail');
