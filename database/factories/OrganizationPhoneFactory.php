<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Organization;
use App\Models\OrganizationPhone;

class OrganizationPhoneFactory extends Factory
{
    protected $model = OrganizationPhone::class;

    public function definition(): array
    {
        return [
            'organization_id' => Organization::factory(),
            'phone_number' => $this->faker->phoneNumber,
        ];
    }
}
