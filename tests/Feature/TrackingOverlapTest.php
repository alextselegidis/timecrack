<?php

namespace Tests\Feature;

use App\Models\ActiveTracking;
use App\Models\Project;
use App\Models\Tracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TrackingOverlapTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->project = Project::factory()->create();
    }

    private function tracking(string $startedAt, string $endedAt, ?User $user = null): Tracking
    {
        return Tracking::create([
            'project_id' => $this->project->id,
            'user_id' => ($user ?? $this->admin)->id,
            'started_at' => $startedAt,
            'ended_at' => $endedAt,
            'billable_hours' => 1,
        ]);
    }

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'project_id' => $this->project->id,
            'user_id' => $this->admin->id,
            'started_at' => '2026-08-26T10:30',
            'ended_at' => '2026-08-26T11:30',
            'billable_hours' => 1,
        ], $overrides);
    }

    public function test_an_overlapping_tracking_is_not_saved_before_it_is_confirmed(): void
    {
        $this->tracking('2026-08-26 10:00:00', '2026-08-26 11:00:00');

        $this->actingAs($this->admin)
            ->post('/trackings', $this->payload())
            ->assertSessionHas('overlapping_trackings');

        $this->assertSame(1, Tracking::query()->count());
    }

    public function test_a_confirmed_overlapping_tracking_is_saved_and_flagged(): void
    {
        $this->tracking('2026-08-26 10:00:00', '2026-08-26 11:00:00');

        $this->actingAs($this->admin)
            ->post('/trackings', $this->payload(['overlap_confirmed' => 1]))
            ->assertRedirect(route('trackings'));

        $this->assertSame(2, Tracking::query()->count());

        // Both sides of the overlap are flagged in the history and in the export.
        $this->actingAs($this->admin)->get('/trackings')->assertSee(__('overlap_detected_message'));
        $this->assertCount(2, Tracking::query()->withOverlapFlag()->get()->where('is_overlapping', true));
    }

    public function test_the_export_and_the_sorted_history_carry_the_overlap_column(): void
    {
        $this->tracking('2026-08-26 10:00:00', '2026-08-26 11:00:00');
        $this->tracking('2026-08-26 10:30:00', '2026-08-26 11:30:00');

        $this->actingAs($this->admin)->get('/trackings?sort=project&direction=asc')->assertOk();

        $csv = $this->actingAs($this->admin)->get('/trackings/export/csv')->streamedContent();

        $this->assertStringContainsString(__('overlap'), $csv);
        $this->assertSame(2, substr_count($csv, ',' . __('yes')));
    }

    public function test_a_tracking_that_does_not_overlap_is_saved_right_away(): void
    {
        $this->tracking('2026-08-26 08:00:00', '2026-08-26 09:00:00');

        $this->actingAs($this->admin)
            ->post('/trackings', $this->payload())
            ->assertRedirect(route('trackings'));

        $this->assertSame(0, Tracking::query()->withOverlapFlag()->get()->where('is_overlapping', true)->count());
    }

    public function test_trackings_of_another_user_never_overlap(): void
    {
        $this->tracking('2026-08-26 10:00:00', '2026-08-26 11:00:00', User::factory()->create());

        $this->actingAs($this->admin)
            ->post('/trackings', $this->payload())
            ->assertRedirect(route('trackings'));

        $this->assertSame(0, Tracking::query()->withOverlapFlag()->get()->where('is_overlapping', true)->count());
    }

    public function test_editing_a_tracking_does_not_overlap_with_itself(): void
    {
        $tracking = $this->tracking('2026-08-26 10:00:00', '2026-08-26 11:00:00');

        $this->actingAs($this->admin)
            ->put('/trackings/' . $tracking->id, $this->payload(['message' => 'Edited']))
            ->assertRedirect(route('trackings.edit', $tracking->id));

        $this->assertSame('Edited', $tracking->fresh()->message);
    }

    public function test_stopping_the_timer_informs_about_an_overlap(): void
    {
        Carbon::setTestNow('2026-08-26 10:00:00');
        $this->tracking('2026-08-26 10:30:00', '2026-08-26 11:30:00');

        ActiveTracking::create([
            'user_id' => $this->admin->id,
            'project_id' => $this->project->id,
            'started_at' => now(),
        ]);

        Carbon::setTestNow('2026-08-26 11:00:00');

        $this->actingAs($this->admin)
            ->post('/timer/stop')
            ->assertSessionHas('warning', __('overlap_saved_message'));
    }
}
