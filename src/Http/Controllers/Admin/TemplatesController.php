<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Routing\ResponseFactory;
use Illuminate\View\Factory as ViewFactory;

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

    /**
     * @param Application $app
     * @param ViewFactory $viewFactory
     * @param ResponseFactory $responseFactory
     */
    public function __construct(Application $app, ViewFactory $viewFactory, ResponseFactory $responseFactory)
    {
        parent::__construct($app);

        $this->viewFactory = $viewFactory;
        $this->responseFactory = $responseFactory;
    }

    public function index()
    {
        return $this->viewFactory->make('templates.index');
    }

    public function view($view)
    {
        return $this->viewFactory->make('templates.' . $view);
    }

    public function xhr($view)
    {
        $response = [
            'data' => $this->viewFactory->make('templates.' . $view)->render(),
            'has_more' => (rand(0, 10) > 5),
        ];
        return $this->responseFactory->json($response);
    }
}
