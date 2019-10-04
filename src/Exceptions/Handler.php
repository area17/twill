<?php

namespace A17\Twill\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Config\Repository as Config;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\ResponseFactory;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\Factory as ViewFactory;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Exceptions\HttpResponseException;

class Handler extends ExceptionHandler
{
    /**
     * Exceptions excluded from reporting.
     *
     * @var string[]
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * @var bool
     */
    protected $isJsonOutputFormat = false;

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var Application
     */
    protected $app;

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
     * @param Application $app
     * @param ViewFactory $viewFactory
     * @param ResponseFactory $responseFactory
     * @param Config $config
     */
    public function __construct(
        Container $container,
        Redirector $redirector,
        UrlGenerator $urlGenerator,
        Application $app,
        ViewFactory $viewFactory,
        ResponseFactory $responseFactory,
        Config $config
    ) {
        parent::__construct($container);

        $this->redirector = $redirector;
        $this->urlGenerator = $urlGenerator;
        $this->app = $app;
        $this->viewFactory = $viewFactory;
        $this->responseFactory = $responseFactory;
        $this->config = $config;
    }

    /**
     * @param Exception $e
     * @return mixed
     * @throws Exception
     */
    public function report(Exception $e)
    {
        return parent::report($e);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param Exception $e
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|string|Response
     */
    public function render($request, Exception $e)
    {
        $e = $this->prepareException($e);

        $this->isJsonOutputFormat = $request->ajax() || $request->wantsJson();

        if ($e instanceof HttpResponseException) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->handleUnauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        }

        return $this->renderHttpExceptionWithView($request, $e);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Exception $e
     * @return \Illuminate\Http\Response|Response
     */
    public function renderHttpExceptionWithView($request, $e)
    {
        if ($this->config->get('app.debug')) {
            return $this->convertExceptionToResponse($e);
        }

        $statusCode = $this->isHttpException($e) ? $e->getStatusCode() : 500;
        $headers = $this->isHttpException($e) ? $e->getHeaders() : [];

        $isSubdomainAdmin = empty($this->config->get('twill.admin_app_path')) && $request->getHost() == $this->config->get('twill.admin_app_url');
        $isSubdirectoryAdmin = !empty($this->config->get('twill.admin_app_path')) && Str::startsWith($request->path(), $this->config->get('twill.admin_app_path'));

        if ($isSubdomainAdmin || $isSubdirectoryAdmin) {
            $view = $this->viewFactory->exists("admin.errors.$statusCode") ? "admin.errors.$statusCode" : "twill::errors.$statusCode";
        } else {
            $view = $this->config->get('twill.frontend.views_path') . ".errors.{$statusCode}";
        }

        if ($this->viewFactory->exists($view)) {
            return $this->responseFactory->view($view, ['exception' => $e], $statusCode, $headers);
        }

        if ($this->isHttpException($e)) {
            return $this->renderHttpException($e);
        }

        return parent::render($request, $e);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param AuthenticationException $exception
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function handleUnauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return $this->responseFactory->json(['error' => 'Unauthenticated.'], 401);
        }

        return $this->redirector->guest($this->urlGenerator->route('admin.login'));
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @param ValidationException $exception
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $exception)
    {
        return $this->responseFactory->json($exception->errors(), $exception->status);
    }
}
