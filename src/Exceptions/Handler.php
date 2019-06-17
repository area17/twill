<?php

namespace A17\Twill\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Inspector;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    protected $dontReport = [
        AuthorizationException::class,
        AccessDeniedHttpException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    protected $isJsonOutputFormat = false;

    public function report(Exception $e)
    {
        return parent::report($e);
    }

    public function render($request, Exception $e)
    {
        $e = $this->prepareException($e);

        $this->isJsonOutputFormat = $request->ajax() || $request->wantsJson();

        /*
         * See Laravel 5.4 Changelog https://laravel.com/docs/5.4/upgrade
         * The Illuminate\Http\Exception\HttpResponseException has been renamed to Illuminate\Http\Exceptions\HttpResponseException.
         * Note that Exceptions is now plural.
         */
        $laravel53HttpResponseException = 'Illuminate\Http\Exception\HttpResponseException';
        $laravel54HttpResponseException = 'Illuminate\Http\Exceptions\HttpResponseException';

        $httpResponseExceptionClass = class_exists($laravel54HttpResponseException) ? $laravel54HttpResponseException : $laravel53HttpResponseException;

        if ($e instanceof $httpResponseExceptionClass) {
            return $e->getResponse();
        } elseif ($e instanceof AuthenticationException) {
            return $this->handleUnauthenticated($request, $e);
        } elseif ($e instanceof ValidationException) {
            return $this->convertValidationExceptionToResponse($e, $request);
        } elseif ($e instanceof AccessDeniedHttpException) {
            return $this->handleAccessDenied($request, $e);
        } 

        if (config('app.debug', false) && config('twill.debug.use_inspector', false)) {
            return Inspector::renderException($e);
        }

        if (config('app.debug', false) && config('twill.debug.use_whoops', false)) {
            return $this->renderExceptionWithWhoops($e);
        }

        return $this->renderHttpExceptionWithView($request, $e);
    }

    public function renderHttpExceptionWithView($request, $e)
    {
        if (config('app.debug')) {
            return $this->convertExceptionToResponse($e);
        }

        $statusCode = $this->isHttpException($e) ? $e->getStatusCode() : 500;
        $headers = $this->isHttpException($e) ? $e->getHeaders() : [];

        $isSubdomainAdmin = empty(config('twill.admin_app_path')) && $request->getHost() == config('twill.admin_app_url');
        $isSubdirectoryAdmin = !empty(config('twill.admin_app_path')) && starts_with($request->path(), config('twill.admin_app_path'));

        if ($isSubdomainAdmin || $isSubdirectoryAdmin) {
            $view = view()->exists("admin.errors.$statusCode") ? "admin.errors.$statusCode" : "twill::errors.$statusCode";
        } else {
            $view = config('twill.frontend.views_path') . ".errors.{$statusCode}";
        }

        if (view()->exists($view)) {
            return response()->view($view, ['exception' => $e], $statusCode, $headers);
        }

        if ($this->isHttpException($e)) {
            return $this->renderHttpException($e);
        }

        return parent::render($request, $e);
    }

    protected function renderExceptionWithWhoops(Exception $e)
    {
        $this->unsetSensitiveData();

        $whoops = new \Whoops\Run();

        if ($this->isJsonOutputFormat) {
            $handler = new \Whoops\Handler\JsonResponseHandler();
        } else {
            $handler = new \Whoops\Handler\PrettyPageHandler();

            if (app()->environment('local', 'development')) {
                $handler->setEditor(function ($file, $line) {
                    $translations = array('^' .
                        config('twill.debug.whoops_path_guest') => config('twill.debug.whoops_path_host'),
                    );
                    foreach ($translations as $from => $to) {
                        $file = rawurlencode(preg_replace('#' . $from . '#', $to, $file, 1));
                    }
                    return array(
                        'url' => "subl://open?url=$file&line=$line",
                        'ajax' => false,
                    );
                });
            }
        }

        $whoops->pushHandler($handler);

        return response(
            $whoops->handleException($e),
            method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500,
            method_exists($e, 'getHeaders') ? $e->getHeaders() : []
        );
    }

    /**
     * Don't ever display sensitive data in Whoops pages.
     */
    protected function unsetSensitiveData()
    {
        foreach ($_ENV as $key => $value) {
            unset($_SERVER[$key]);
        }

        $_ENV = [];
    }

    protected function handleUnauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }

        return redirect()->guest(route('admin.login'));
    }

    protected function handleAccessDenied($request, AccessDeniedHttpException $exception)
    {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
        session()->flash('status', 'You don\'t have permission to perform this action, redirect you back to the previous location');
        if (back()->getTargetUrl() === url()->current()) {
            return redirect('/');
        };
        return back();
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json($exception->errors(), $exception->status);
    }
}
