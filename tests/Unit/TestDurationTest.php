<?php

namespace Tests\Unit;

use App\Support\TestDuration;
use PHPUnit\Framework\TestCase;

class TestDurationTest extends TestCase
{
    private const HOURS_48 = 48 * 60;

    public function test_within_the_30_minute_margin_is_ok()
    {
        $check = TestDuration::check('2026-10-05 12:00', '2026-10-07 12:25', self::HOURS_48);

        $this->assertSame('ok', $check['status']);
        $this->assertSame(48 * 60 + 25, $check['elapsed']);
    }

    public function test_more_than_30_minutes_short_or_long_is_flagged()
    {
        $this->assertSame('short', TestDuration::check('2026-10-05 12:00', '2026-10-07 11:00', self::HOURS_48)['status']);
        $this->assertSame('long', TestDuration::check('2026-10-05 12:00', '2026-10-07 14:00', self::HOURS_48)['status']);
    }

    public function test_missing_dates_are_incomplete()
    {
        $this->assertSame('incomplete', TestDuration::check('2026-10-05 12:00', null, self::HOURS_48)['status']);
        $this->assertSame('incomplete', TestDuration::check('', '2026-10-07 12:00', self::HOURS_48)['status']);
    }

    // En Arbacia fecundación el término se anota solo como hora (HH:MM)
    public function test_end_given_only_as_time_uses_the_start_day_or_the_next()
    {
        $this->assertSame(55, TestDuration::check('2026-10-05 10:00', '10:55', 60)['elapsed']);
        $this->assertSame(60, TestDuration::check('2026-10-05 23:30', '00:30', 60)['elapsed']);
    }

    public function test_long_durations_are_shown_in_days()
    {
        $this->assertSame('48 h 0 min', TestDuration::format(48 * 60));
        $this->assertSame('21 d 3 h', TestDuration::format(21 * 24 * 60 + 3 * 60));
    }
}
