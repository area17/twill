<?php

namespace App\Providers;

use A17\Twill\Models\Block;
use A17\Twill\Models\Feature;
use A17\Twill\Models\Setting;
use App\Policies\BlocksPolicy;
use App\Policies\FeaturePolicy;
use App\Policies\SettingPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Setting::class => SettingPolicy::class,
        Feature::class => FeaturePolicy::class,
        Block::class => BlocksPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
