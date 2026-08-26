<?php

namespace Tests\Feature;

use App\Models\ActiveTracking;
use App\Models\Project;
use App\Models\Tracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TimerPauseTest extends TestCase
{
    use RefreshDatabase;

    private function startTimer(User $user): ActiveTracking
    {
        return ActiveTracking::create([
            'user_id' => $user->id,
            'project_id' => Project::factory()->create()->id,
            'started_at' => now(),
        ]);
    }

    public function test_pausing_and_resuming_accumulates_the_paused_duration(): void
    {
        Carbon::setTestNow('2026-08-26 10:00:00');

        $user = User::factory()->create();
        $activeTracking = $this->startTimer($user);

        Carbon::setTestNow('2026-08-26 10:30:00');
        $this->actingAs($user)->post('/timer/pause')->assertRedirect('/dashboard');
        $this->assertNotNull($activeTracking->fresh()->paused_at);

        $this->actingAs($user)->get('/dashboard')->assertSee(__('resume'))->assertSee(__('paused'));

        Carbon::setTestNow('2026-08-26 10:45:00');
        $this->actingAs($user)->post('/timer/pause')->assertRedirect('/dashboard');

        $this->actingAs($user)->get('/dashboard')->assertSee(__('pause'));

        $activeTracking->refresh();
        $this->assertNull($activeTracking->paused_at);
        $this->assertSame(900, $activeTracking->paused_duration);
    }

    public function test_paused_time_becomes_non_billable_when_the_timer_stops(): void
    {
        Carbon::setTestNow('2026-08-26 10:00:00');

        $user = User::factory()->create();
        $this->startTimer($user);

        Carbon::setTestNow('2026-08-26 10:30:00');
        $this->actingAs($user)->post('/timer/pause');

        Carbon::setTestNow('2026-08-26 10:45:00');
        $this->actingAs($user)->post('/timer/pause');

        Carbon::setTestNow('2026-08-26 11:00:00');
        $this->actingAs($user)->post('/timer/stop')->assertRedirect('/dashboard');

        $tracking = Tracking::query()->firstOrFail();

        $this->assertSame(3600, $tracking->duration_seconds);
        $this->assertSame('0.75', $tracking->billable_hours);
        $this->assertSame(900, $tracking->non_billable_seconds);
    }

    public function test_a_running_pause_is_counted_when_the_timer_stops(): void
    {
        Carbon::setTestNow('2026-08-26 10:00:00');

        $user = User::factory()->create();
        $this->startTimer($user);

        Carbon::setTestNow('2026-08-26 10:30:00');
        $this->actingAs($user)->post('/timer/pause');

        Carbon::setTestNow('2026-08-26 11:00:00');
        $this->actingAs($user)->post('/timer/stop');

        $tracking = Tracking::query()->firstOrFail();

        $this->assertSame(1800, $tracking->non_billable_seconds);
    }

    public function test_billable_hours_cannot_exceed_the_unpaused_duration(): void
    {
        Carbon::setTestNow('2026-08-26 10:00:00');

        $user = User::factory()->create();
        $this->startTimer($user);

        Carbon::setTestNow('2026-08-26 10:30:00');
        $this->actingAs($user)->post('/timer/pause');

        Carbon::setTestNow('2026-08-26 11:00:00');
        $this->actingAs($user)
            ->post('/timer/stop', ['billable_hours' => 0.75])
            ->assertSessionHasErrors('billable_hours');
    }
}
