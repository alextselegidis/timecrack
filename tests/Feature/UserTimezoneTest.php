<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Tracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTimezoneTest extends TestCase
{
    use RefreshDatabase;

    public function test_trackings_are_displayed_in_the_timezone_of_the_user(): void
    {
        $project = Project::factory()->create();

        $tracking = fn(User $user) => Tracking::create([
            'project_id' => $project->id,
            'user_id' => $user->id,
            'started_at' => '2026-08-26 22:30:00',
            'ended_at' => '2026-08-26 23:30:00',
            'billable_hours' => 1,
        ]);

        $tokyo = User::factory()->create(['timezone' => 'Asia/Tokyo']);
        $athens = User::factory()->create(['timezone' => 'Europe/Athens']);

        $tracking($tokyo);
        $tracking($athens);

        // 22:30 UTC is 07:30 the next day in Tokyo and 01:30 the next day in Athens.
        $this->actingAs($tokyo)->get('/trackings')->assertSee('27/08/2026')->assertSee('07:30');
        $this->actingAs($athens)->get('/trackings')->assertSee('27/08/2026')->assertSee('01:30');
    }

    public function test_an_admin_can_change_the_timezone_of_another_user(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create();

        $this->actingAs($admin)
            ->put('/setup/users/' . $user->id, [
                'name' => $user->name,
                'email' => $user->email,
                'role' => 'user',
                'timezone' => 'Asia/Tokyo',
            ])
            ->assertRedirect(route('setup.users.edit', $user->id));

        $this->assertSame('Asia/Tokyo', $user->fresh()->timezone);
    }

    public function test_a_user_can_change_the_timezone_of_the_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/account', [
                'name' => $user->name,
                'email' => $user->email,
                'timezone' => 'America/New_York',
            ])
            ->assertRedirect('/account');

        $this->assertSame('America/New_York', $user->fresh()->timezone);
    }
}
