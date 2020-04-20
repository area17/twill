<?php

namespace A17\Twill\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Factory as ViewFactory;

class Handler extends ExceptionHandler
{

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    /**
     * @var ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Container $container
     * @param Redirector $redirector
     * @param UrlGenerator $urlGenerator
     * @param ResponseFactory $responseFactory
     */
    public function __construct(
        Container $container,
        Redirector $redirector,
        UrlGenerator $urlGenerator,
        ResponseFactory $responseFactory,
        Config $config,
        ViewFactory $viewFactory
    ) {
        parent::__construct($container);

        $this->redirector = $redirector;
        $this->urlGenerator = $urlGenerator;
        $this->responseFactory = $responseFactory;
        $this->viewFactory = $responseFactory;
        $this->config = $config;
    }

    /**
     * Convert an authentication exception into a response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Auth\AuthenticationException  $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $request->expectsJson()
        ? $this->responseFactory->json(['message' => $exception->getMessage()], 401)
        : $this->redirector->guest($exception->redirectTo() ?? $this->urlGenerator->route('admin.login'));
    }

    /**
     * Get the Twill error view used to render a specified HTTP status code.
     *
     * @param  integer $statusCode
     * @return string
     */
    protected function getTwillErrorView($statusCode, $frontend = false)
    {
        if ($frontend) {
            return $this->config->get('twill.frontend.views_path') . ".errors.{$statusCode}";
        }

        return $this->viewFactory->exists("admin.errors.$statusCode") ? "admin.errors.$statusCode" : "twill::errors.$statusCode";
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json($exception->errors(), $exception->status);
    }
}
