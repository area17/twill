<?php

namespace A17\Twill;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class TwillPackageServiceProvider extends ServiceProvider
{
//    public function registerCapsule(string $name): void {
//        $config = Config::get('twill-navigation', []);
//
//        $config[$name] = [
//            'title' => $name,
//            'module' => true,
//        ];
//
//        Config::set('twill-navigation', $config);
//    }


//    public function boot(): void
//    {
//        $this->loadRoutesFrom($this->getPackageDirectory() . '/routes/admin.php');
//    }

//    protected function getCapsuleName(): string
//    {
//        return str_replace('ServiceProvider', '', $this->getClassName());
//    }
//
//    protected function getClassName(): string {
//        $provider = explode('\\', get_class($this));
//
//        return array_pop($provider);
//    }
//
//    protected function getPackageDirectory(): string
//    {
//        $class = new ReflectionClass(get_class($this));
//
//        $path = Str::replaceLast('/' . $this->getClassName() . '.php', '', $class->getFileName());
//
//        if (Str::endsWith($path, '/src')) {
//            $path = Str::replaceLast('/src', '', $path);
//        }
//
//        return $path;
//    }
}
