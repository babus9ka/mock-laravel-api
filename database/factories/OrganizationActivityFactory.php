<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;
use App\Models\Activity;
use App\Models\OrganizationActivity;

class OrganizationActivityFactory extends Factory
{
    protected $model = OrganizationActivity::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'activity_id' => Activity::factory(),
        ];
    }
}
