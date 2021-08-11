<?php

require_once __DIR__ . '/PrettierPHPFixer.php';

$config = new PhpCsFixer\Config();
return $config->registerCustomFixers([new PrettierPHPFixer()])->setRules([
    '@PSR12' => true,
    'Prettier/php' => true
]);
