<?php

namespace A17\Twill\Services\MediaLibrary;

/**
 * Base class to implement image service parameter compatibility.
 *
 * This class was introduced along with the TwicPics image service to implement a basic
 * compatibility layer with Imgix-type parameters. There are a few instances in the
 * Twill and Twill Image [1] source code where parameters were hardcoded, such as:
 *
 * - `w`
 * - `h`
 * - `fm`
 * - `q`
 * - `fit=crop`
 *
 * This was adopted internally as the minimum set of parameters for which
 * Twill image services need to provide compatibility.
 *
 * [1] https://github.com/area17/twill-image
 *
 * @see TwicPicsParamsProcessor
 */
abstract class AbstractParamsProcessor
{
    /**
     * @var array<string, string>
     */
    public const COMPATIBLE_PARAMS = [
        'w' => 'width',
        'h' => 'height',
        'fm' => 'format',
        'q' => 'quality',
        'fit' => 'fit',
    ];

    protected $params;

    protected $width;

    protected $height;

    protected $format;

    protected $quality;

    protected $fit;

    /**
     * Abstract method to be implemented in concrete params processor classes.
     * This method is called after all parameters have been processed and
     * must return a finalized params array to generate the image URL.
     *
     * @return array
     */
    abstract public function finalizeParams();

    /**
     * Receives the original params array and calls the appropriate handler method
     * for each param. Custom handlers can be defined by following this naming
     * convention: `handleParamNAME`, where NAME is the name of the param.
     *
     * @param array $params
     * @return array
     */
    public function process($params)
    {
        $this->params = $params;

        foreach ($params as $key => $value) {
            $handler = "handleParam{$key}";

            if (method_exists($this, $handler)) {
                $this->{$handler}($key, $value);
            } else {
                $this->handleParam($key, $value);
            }
        }

        return $this->finalizeParams();
    }

    /**
     * The generic param handler. Known parameter values will be extracted into the
     * corresponding properties as defined in COMPATIBLE_PARAMS. Unknown params
     * will remain untouched.
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    protected function handleParam($key, $value)
    {
        if ($property = static::COMPATIBLE_PARAMS[$key] ?? false) {
            $this->{$property} = $value;

            unset($this->params[$key]);
        }
    }
}
