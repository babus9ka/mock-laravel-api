<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Building;
use App\Models\Organization;
use App\Models\OrganizationPhone;
use App\Models\Activity;
use App\Models\OrganizationActivity;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ru_RU'); // Устанавливаем русский локализатор

        // Создаем здания
        $buildings = Building::factory()->count(10)->create();

        // Создаем организации
        $organizations = Organization::factory()
            ->count(10)
            ->create()
            ->each(function ($organization) use ($buildings) {
                $organization->update(['building_id' => $buildings->random()->id]);
            });

        // Создаем телефоны для организаций
        foreach ($organizations as $organization) {
            OrganizationPhone::factory()->count(rand(1, 3))->create([
                'organization_id' => $organization->id,
            ]);
        }

        // Создаем виды деятельности
        $activities = Activity::factory()->count(5)->create()->each(function ($activity) use ($faker) {
            // Генерируем дочерние активности
            $activity->children()->saveMany(Activity::factory()->withChildActivities(3));
        });

        // Объединяем родительские и дочерние активности в одну коллекцию
        $allActivities = Activity::with('children')->get();

        // Привязываем организации к деятельности (многие-ко-многим)
        foreach ($organizations as $organization) {
            $organization->activities()->attach(
                $allActivities->random(rand(1, 3))->pluck('id')->toArray()
            );
        }
        \Log::info($allActivities->pluck('id')->toArray());


    }
}
