<?php

namespace A17\Twill\Services\Blocks;

use Throwable;
use Illuminate\View\Factory;
use Illuminate\Support\Facades\Blade;

class BladeCompiler
{
    /**
     * @param $data
     * @return mixed
     */
    protected static function absorbApplicationEnvironment($data)
    {
        $data['__env'] = app(Factory::class);

        return $data;
    }

    /**
     * @throws \Throwable
     */
    protected static function compile(string $php, array $data)
    {
        $obLevel = self::initializeOutputBuffering();

        try {
            extract(self::absorbApplicationEnvironment($data), EXTR_SKIP);

            eval('?' . '>' . $php);
        } catch (Throwable $throwable) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }

            throw $throwable;
        }
    }

    /**
     * @return false|string
     */
    protected static function getRendered()
    {
        return ob_get_clean();
    }

    /**
     * @return int
     */
    protected static function initializeOutputBuffering()
    {
        $obLevel = ob_get_level();

        ob_start();

        return $obLevel;
    }

    /**
     * @param $string
     * @param $data
     * @return false|string
     * @throws \Throwable
     */
    public static function render($string, $data)
    {
        self::compile(Blade::compileString($string), $data);

        return self::getRendered();
    }
}
