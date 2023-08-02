<?php

namespace A17\Twill\Commands;

use A17\Twill\Commands\Traits\ExecutesInTwillDir;
use A17\Twill\TwillServiceProvider;
use Illuminate\Support\Facades\Artisan;

class Release extends Command
{
    use ExecutesInTwillDir;

    protected $signature = 'twill:release {version}';

    protected $description = 'Create a new twill version tag';

    public function handle()
    {
        $version = $this->argument('version');
        $this->versionIsGreaterThanLatestVersion($version);
        $this->checkChangelogContainsVersion($version);
        $this->isOnMainBranch();
        $this->isNotBehind();
        $this->checkTwillServiceProviderVersion($version);
        $this->checkPackageJsonVersionMatches($version);

        $this->line('Building assets.');
        Artisan::call('twill:build');

        // Copy from dist to twill-assets.
        $this->executeInTwillDir('rm -Rf twill-assets/assets && cp -Rf dist/assets twill-assets/assets');

        $this->line('Force add the assets.');
        $this->executeInTwillDir('git add twill-assets');

        $this->line('Making new commit with assets');
        $this->executeInTwillDir('git commit -m "Updating assets for release ' . $version . '"');

        $this->line('Pushing last commit');
        $this->executeInTwillDir('git push');

        $this->line('Making the tag');
        $this->executeInTwillDir("git tag $version");

        $this->line('Pushing the tag');
        $this->executeInTwillDir("git push origin --tags $version");

        $this->line('Done!');
    }

    /**
     * @return void
     */
    private function isNotBehind()
    {
        $currentBranch = $this->executeInTwillDir('git rev-parse --abbrev-ref HEAD');
        if (!empty($this->executeInTwillDir('git diff origin/' . $currentBranch))) {
            $this->error(
                'It looks like your current branch is not clean, please git pull/commit/stash the latest changes before making a release.'
            );
            exit(1);
        }
    }

    /**
     * @return void
     */
    private function isOnMainBranch()
    {
        $currentBranch = $this->executeInTwillDir('git rev-parse --abbrev-ref HEAD');
        if (!in_array(trim($currentBranch), ['2.x', '3.x'])) {
            $this->error('Current working branch must be a releasable branch (2.x or 3.x)');
            exit(1);
        }
    }

    /**
     * @param string $version
     * @return void
     */
    private function checkChangelogContainsVersion($version)
    {
        if (!str_contains(file_get_contents($this->getTwillDir('CHANGELOG.md')), "## $version")) {
            $this->error('The changelog is currently missing the version you are trying to tag.');
            exit(1);
        }
    }

    /**
     * @param string $version
     * @return void
     */
    private function versionIsGreaterThanLatestVersion($version)
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
            $this->line('New version is greater than old version, continuing release.');
            return;
        }

        $this->error('New version is lower than te last tagged one: ' . $tags->last());
        exit(1);
    }

    /**
     * @param string $newVersion
     * @param string $oldVersion
     * @return bool
     */
    private function newVersionIsGreaterThanOld($newVersion, $oldVersion)
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

    /**
     * @param string $version
     * @return void
     */
    private function checkPackageJsonVersionMatches($version)
    {
        $array = json_decode(file_get_contents($this->getTwillDir('package.json')));

        if ($array->version !== $version) {
            $this->error("The package.json version ({$array->version}) does not match that of the release ($version)");
            exit(1);
        }
    }

    /**
     * @param $version
     * @return void
     */
    private function checkTwillServiceProviderVersion($version)
    {
        if (TwillServiceProvider::VERSION !== $version) {
            $twillServiceProviderVersion = TwillServiceProvider::VERSION;
            $this->error(
                "The TwillServiceProvider::VERSION version ($twillServiceProviderVersion) does not match that of the release ($version)"
            );
            exit(1);
        }
    }
}
