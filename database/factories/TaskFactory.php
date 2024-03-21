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
        $randomStartedAt = $this->faker->dateTime();
        $randomDuration = $this->faker->randomElement([15, 30, 60, 90]);
        $randomDurationIntervalObject =new DateInterval('PT' . $randomDuration . 'M');
        $randomEndedAt = clone $randomStartedAt;
        $randomEndedAt->add($randomDurationIntervalObject);

        return [
            'project_id' => null,
            'user_id' => null,
            'started_at' => $randomStartedAt,
            'ended_at' => $randomEndedAt,
            'summary' => $this->faker->sentence(),
        ];
    }
}
