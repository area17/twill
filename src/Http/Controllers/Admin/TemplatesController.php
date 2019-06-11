<?php

namespace A17\Twill\Http\Controllers\Admin;

class TemplatesController extends Controller
{
    /**
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('templates.index');
    }

    /**
     * @param string $view
     * @return \Illuminate\View\View
     */
    public function view($view)
    {
        return view('templates.' . $view);
    }

    /**
     * @param string view
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function xhr($view)
    {
        $response = [
            'data' => view('templates.' . $view)->render(),
            'has_more' => (rand(0, 10) > 5),
        ];
        return response()->json($response);
    }
}
