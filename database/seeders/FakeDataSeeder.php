<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class FakeDataSeeder extends Seeder {
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = User::factory(20)->create();

        foreach($users as $user) {
            $project = Project::factory(1)->create();

            $project->first()->users()->attach($user->id);

            Task::factory(10)->create([
                'project_id' => $project->first()->id,
                'user_id' => $user->id,
            ]);
        }
    }
}
