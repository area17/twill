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
    protected static function compile(string $php, array $data): void
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

    protected static function getRendered(): bool|string
    {
        return ob_get_clean();
    }

    protected static function initializeOutputBuffering(): int
    {
        $obLevel = ob_get_level();

        ob_start();

        return $obLevel;
    }

    /**
     * @param $string
     * @param $data
     * @throws \Throwable
     */
    public static function render($string, array $data): bool|string
    {
        self::compile(Blade::compileString($string), $data);

        return self::getRendered();
    }
}
