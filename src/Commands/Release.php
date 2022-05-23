<?php

namespace A17\Twill\Commands;

use A17\Twill\TwillServiceProvider;
use Illuminate\Support\Facades\Artisan;

class Release extends Command
{
    protected $signature = 'twill:release {version}';

    protected $description = 'Create a new twill version tag';

    public function handle()
    {
        $this->versionIsGreaterThanLatestVersion($this->argument('version'));
//        $this->checkTwillServiceProviderVersion($this->argument('version'));
//        $this->checkPackageJsonVersionMatches($this->argument('version'));

        // Everything is now fine.
        Artisan::call('twill:build');
    }

    private function executeInTwillDir(string $command): string
    {
        $twillDir = $this->getTwillDir();
        return shell_exec("cd $twillDir && $command");
    }

    private function versionIsGreaterThanLatestVersion(string $version): void
    {
        $splittedVersion = explode('.', $version);
        if (count($splittedVersion) < 3) {
            $this->error('Version number must be of the following format: 1.2.3 (Major, Minor, Patch)');
            exit(1);
        }

        $tags = collect(explode(PHP_EOL, $this->executeInTwillDir('git tag --list')));

        $tags = $tags->reject(function ($tag) {
            return empty($tag) || str_contains($tag, '-') || count(explode('.', $tag)) < 3;
        });

        $tags = $tags->sort(function ($tag1, $tag2) {
            return $this->newVersionIsGreaterThanOld($tag1, $tag2);
        });

        if ($this->newVersionIsGreaterThanOld($version, $tags->last())) {
            $this->line('New version is greater than old version');
            return;
        }

        $this->error('New version is lower than te last tagged one: ' . $tags->last());
    }

    private function newVersionIsGreaterThanOld(string $newVersion, string $oldVersion): bool
    {
        [$major1, $minor1, $patch1] = explode('.', $newVersion);
        [$major2, $minor2, $patch2] = explode('.', $oldVersion);

        if ($major1 === $major2) {
            if ($minor1 === $minor2) {
                return $patch1 > $patch2;
            }

            return $minor1 > $minor2;
        }

        return $major1 > $major2;
    }

    private function checkPackageJsonVersionMatches(string $version): void
    {
        $array = json_decode(file_get_contents($this->getTwillDir('package.json')));

        if ($array->version !== $version) {
            $this->error("The package.json version ({$array->version}) does not match that of the release ($version)");
            exit(1);
        }
    }

    private function checkTwillServiceProviderVersion(string $version): void
    {
        if (TwillServiceProvider::VERSION !== $version) {
            $twillServiceProviderVersion = TwillServiceProvider::VERSION;
            $this->error(
                "The TwillServiceProvider::VERSION version ($twillServiceProviderVersion) does not match that of the release ($version)"
            );
            exit(1);
        }
    }

    private function getTwillDir(string $path = ''): string
    {
        return __DIR__ . '/../../' . $path;
    }

}
