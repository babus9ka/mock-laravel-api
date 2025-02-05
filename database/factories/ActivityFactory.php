<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Activity;

class ActivityFactory extends Factory
{
    protected $model = Activity::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word,
            'parent_id' => null, // Можно позже добавить логику вложенности
        ];
    }

    /**
     * Рекурсивное создание дочерних активностей
     *
     * @param int $level
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function withChildActivities($level = 3)
    {
        // Ограничиваем максимальный уровень вложенности
        if ($level <= 0) {
            return collect(); // Возвращаем пустую коллекцию, если глубина достигнута
        }

        $activities = collect();
        $childrenCount = rand(1, 3); // Количество дочерних активностей

        for ($i = 0; $i < $childrenCount; $i++) {
            $childActivity = Activity::factory()->create([
                'name' => $this->faker->word . ' ' . $this->faker->word,
                'parent_id' => null, // Можно указать родителя, если необходимо
            ]);
            
            // Рекурсивно создаем дочерние активности для дочерней активности
            $activities->push($childActivity);
            $activities = $activities->merge($this->withChildActivities($level - 1));
        }

        return $activities;
    }
}
