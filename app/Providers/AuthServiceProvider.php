<?php

namespace App\Providers;

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
        \App\Models\DaphniaMagnaTemplate::class => \App\Policies\BioassayPolicy::class,
        \App\Models\DaphniaMagnaChronic::class => \App\Policies\BioassayPolicy::class,
        \App\Models\IsochrysisGalbana::class => \App\Policies\BioassayPolicy::class,
        \App\Models\SelenastrumCapricornutum::class => \App\Policies\BioassayPolicy::class,
        \App\Models\TisbeLongicornisWater::class => \App\Policies\BioassayPolicy::class,
        \App\Models\TisbeLongicornisRiles::class => \App\Policies\BioassayPolicy::class,
        \App\Models\ArbaciaFertilization::class => \App\Policies\BioassayPolicy::class,
        \App\Models\ArbaciaLarvalStage::class => \App\Policies\BioassayPolicy::class,
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
