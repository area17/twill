<?php

namespace A17\Twill\Http\Controllers\Admin;

use Illuminate\Routing\ResponseFactory;
use Illuminate\View\Factory as ViewFactory;

class TemplatesController extends Controller
{
    public function __construct(protected ViewFactory $viewFactory, protected ResponseFactory $responseFactory)
    {
        parent::__construct();
    }

    public function index(): \Illuminate\Contracts\View\View
    {
        return $this->viewFactory->make('templates.index');
    }

    public function view(string $view): \Illuminate\Contracts\View\View
    {
        return $this->viewFactory->make('templates.' . $view);
    }

    /**
     * @param string view
     * @throws \Throwable
     */
    public function xhr($view): \Illuminate\Http\JsonResponse
    {
        $response = [
            'data' => $this->viewFactory->make('templates.' . $view)->render(),
            'has_more' => (rand(0, 10) > 5),
        ];
        return $this->responseFactory->json($response);
    }
}
