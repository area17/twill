<?php

namespace A17\Twill\Services\Blocks;

use Exception;
use Throwable;
use Illuminate\View\Factory;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Debug\Exception\FatalThrowableError;

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
     * @param string $php
     * @param array $data
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
    protected static function compile(string $php, array $data)
    {
        $obLevel = self::initializeOutputBuffering();

        try {
            extract(self::absorbApplicationEnvironment($data), EXTR_SKIP);

            eval('?' . '>' . $php);
        } catch (Exception $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw $e;
        } catch (Throwable $e) {
            while (ob_get_level() > $obLevel) {
                ob_end_clean();
            }
            throw new FatalThrowableError($e);
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
     * @throws \Symfony\Component\Debug\Exception\FatalThrowableError
     */
    public static function render($string, $data)
    {
        self::compile(Blade::compileString($string), $data);

        return self::getRendered();
    }
}
