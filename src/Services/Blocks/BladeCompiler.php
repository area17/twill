<?php

namespace A17\Twill\Services\Blocks;

use Exception;
use Throwable;
use Illuminate\View\Factory;
use Illuminate\Support\Facades\Blade;
use Symfony\Component\Debug\Exception\FatalThrowableError;

class BladeCompiler
{
    public static function render($string, $data)
    {
        $php = Blade::compileString($string);

        $data['__env'] = app(Factory::class);

        $obLevel = ob_get_level();
        ob_start();
        extract($data, EXTR_SKIP);

        try {
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

        $compiled = ob_get_clean();

        return $compiled;
    }
}
