<?php

namespace A17\CmsToolkit\Http\Controllers\Front\Behaviors;

trait HandleSpfRequests
{
    protected $default_options = [];

    protected function isSpfRequest()
    {
        return request('spf') == "navigate" || request('spf') == "prefetch";
    }

    protected function isSpfLoad()
    {
        return request('spf') == "load";
    }

    protected function spfResponse($view, $name, $section, $options = [], $load = false)
    {
        $partial = [
            "name" => $name,
            "body" => [
                $section => $view->renderSections()[$section],
            ],
            "attr" => [
                "body" => [
                    "class" => "body--{$name}",
                ],
            ],
        ];

        $response = array_replace_recursive($load ? [] : $this->default_options, $partial, $options);

        return response()->json($response);
    }

}
