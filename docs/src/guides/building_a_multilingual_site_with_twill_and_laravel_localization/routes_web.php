<?php

// #region newsroute
use App\Models\Article;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::get('news', function () {
        return view('site.articles.index', [
            'articles' => Article::published()->orderBy('created_at', 'desc')->get(),
        ]);
    })->name('articles');
});
// #endregion newsroute

// #region newsarticleroute
use App\Models\Article;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::get('news', function () {
        return view('site.articles.index', [
            'articles' => Article::published()->orderBy('created_at', 'desc')->get(),
        ]);
    })->name('articles');

    Route::get('news/{article}', function (Article $article) {
        return view('site.articles.show', [
            'article' => $article,
        ]);
    })->name('article');
});
// #endregion newsarticleroute

// #region newsrouteupdated
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => ['localize', 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath'],
], function () {
    Route::get(LaravelLocalization::transRoute('routes.articles'), function () {
        return view('site.articles.index', [
            'articles' => Article::published()->orderBy('created_at', 'desc')->get(),
        ]);
    })->name('articles');

    Route::get(LaravelLocalization::transRoute('routes.article'), function (Article $article) {
        return view('site.articles.show', [
            'article' => $article,
        ]);
    })->name('article');
});
// #endregion newsrouteupdated
