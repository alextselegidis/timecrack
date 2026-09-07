<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Tracking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrackingTotalsTest extends TestCase
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

    /**
     * Durations that carry stray seconds, which is where the rounding used to drift apart.
     */
    private function seedTrackings(): void
    {
        $rows = [
            ['10:00:00', '10:29:31', 0.49],  // 30 min, billable rounds to 29
            ['11:00:00', '11:59:29', 1.00],  // 59 min, billable capped at the duration
            ['13:00:00', '14:00:31', null],  // 61 min, no billable hours at all
            ['15:00:00', '15:00:29', 0],     // 0 min, a sub minute tracking
            ['16:00:00', '17:30:44', 1.25],  // 91 min, 75 billable
        ];

        foreach ($rows as [$startedAt, $endedAt, $billableHours]) {
            Tracking::create([
                'project_id' => $this->project->id,
                'user_id' => $this->admin->id,
                'started_at' => '2026-09-01 ' . $startedAt,
                'ended_at' => '2026-09-01 ' . $endedAt,
                'billable_hours' => $billableHours,
            ]);
        }
    }

    public function test_billable_and_non_billable_minutes_always_add_up_to_the_duration(): void
    {
        $this->seedTrackings();

        foreach (Tracking::all() as $tracking) {
            $this->assertSame(
                $tracking->duration_minutes,
                $tracking->billable_minutes + $tracking->non_billable_minutes,
                'Row ' . $tracking->id . ' does not add up.'
            );
            $this->assertGreaterThanOrEqual(0, $tracking->non_billable_minutes);
        }
    }

    public function test_the_sql_totals_equal_the_sum_of_the_rows(): void
    {
        $this->seedTrackings();

        $totals = Tracking::query()->selectTotals();
        $rows = Tracking::all();

        $this->assertSame($rows->sum('duration_minutes'), $totals['duration']);
        $this->assertSame($rows->sum('billable_minutes'), $totals['billable']);
        $this->assertSame($rows->sum('non_billable_minutes'), $totals['non_billable']);
        $this->assertSame($totals['duration'], $totals['billable'] + $totals['non_billable']);
    }

    public function test_the_history_totals_match_the_rows_it_lists(): void
    {
        $this->seedTrackings();

        $totals = $this->actingAs($this->admin)->get(route('trackings'))->assertOk()->viewData('totals');

        $this->assertSame(Tracking::all()->sum('duration_minutes'), $totals['duration']);
        $this->assertSame('4h 1m', duration_label($totals['duration']));
        $this->assertSame('2h 43m', duration_label($totals['billable']));
        $this->assertSame('1h 18m', duration_label($totals['non_billable']));
    }

    public function test_the_export_total_row_equals_the_sum_of_its_data_rows(): void
    {
        $this->seedTrackings();

        $response = $this->actingAs($this->admin)->get(route('trackings.export'))->assertOk();
        $lines = array_filter(explode("\n", trim($response->streamedContent())));

        // Drop the byte order mark and the header row, then split the total row off the data rows.
        array_shift($lines);
        $total = str_getcsv(array_pop($lines));
        $rows = array_map('str_getcsv', $lines);

        // The user column shifts the hours columns by one for an administrator.
        foreach ([4 => 'duration', 5 => 'billable', 6 => 'non_billable'] as $column => $label) {
            $this->assertSame(
                number_format(array_sum(array_column($rows, $column)), 2, '.', ''),
                $total[$column],
                'The ' . $label . ' column does not add up to its total.'
            );
        }
    }
}
