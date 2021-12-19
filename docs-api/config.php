<?php

use Doctum\Doctum;
use Doctum\RemoteRepository\GitHubRemoteRepository;
use Doctum\Version\GitVersionCollection;
use Symfony\Component\Finder\Finder;

$dir = '../src';

$iterator = Finder::create()
    ->files()
    ->name('*.php')
    ->in($dir);

$versions = GitVersionCollection::create($dir)
    ->add('main', 'main branch')
    ->add('1.1', '1.1 branch')
    ->add('1.2', '1.2 branch')
    ->add('2.x', '2.x branch');

return new Doctum($iterator, [
    'title' => 'Twill API',
    'versions' => $versions,
    'language' => 'en',
    'build_dir' => __DIR__ . '/build/%version%',
    'cache_dir' => __DIR__ . '/cache/%version%',
    'remote_repository' => new GitHubRemoteRepository('area17/twill', dirname($dir)),
    'default_opened_level' => 2,
]);
