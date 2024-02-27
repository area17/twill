<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\ResponseFactory;
use Illuminate\View\Factory as ViewFactory;
use Illuminate\View\View;
use Throwable;

class TemplatesController extends Controller
{
    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    public function __construct(ViewFactory $viewFactory, ResponseFactory $responseFactory)
    {
        parent::__construct();

        $this->viewFactory = $viewFactory;
        $this->responseFactory = $responseFactory;
    }

    /**
     * @return View
     */
    public function index()
    {
        return $this->viewFactory->make('templates.index');
    }

    /**
     * @param  string  $view
     * @return View
     */
    public function view($view)
    {
        return $this->viewFactory->make('templates.' . $view);
    }

    /**
     * @param string view
     * @return JsonResponse
     *
     * @throws Throwable
     */
    public function xhr($view)
    {
        $response = [
            'data' => $this->viewFactory->make('templates.' . $view)->render(),
            'has_more' => (rand(0, 10) > 5),
        ];

        return $this->responseFactory->json($response);
    }
}
