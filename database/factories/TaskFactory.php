<?php

namespace Database\Factories;

use DateInterval;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws \Exception
     */
    public function definition(): array
    {
        $startedAt = $this->faker->dateTime();
        $duration = $this->faker->randomElement([15, 30, 60, 90]);
        $durationIntervalObject =new DateInterval('PT' . $duration . 'M');
        $endedAt = clone $startedAt;
        $endedAt->add($durationIntervalObject);

        return [
            'project_id' => null,
            'user_id' => null,
            'external_id' => $this->faker->uuid(),
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'summary' => $this->faker->sentence(),
            'is_billable' => $this->faker->boolean(80),
        ];
    }
}
