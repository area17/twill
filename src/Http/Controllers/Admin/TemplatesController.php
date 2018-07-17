<?php

namespace A17\Twill\Http\Controllers\Admin;

class TemplatesController extends Controller
{
    public function index()
    {
        return view('templates.index');
    }

    public function view($view)
    {
        return view('templates.'.$view);
    }

    public function xhr($view)
    {
        $response = [
            'data'     => view('templates.'.$view)->render(),
            'has_more' => (rand(0, 10) > 5),
        ];

        return response()->json($response);
    }
}
