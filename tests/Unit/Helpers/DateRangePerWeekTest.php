<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;

class DateRangePerWeekTest extends TestCase
{
    /** @test */
    public function it_return_correct_date_range_per_week_for_the_given_month()
    {
        $this->assertEquals([
            13 => ['2023-04-01', '2023-04-02'],
            14 => ['2023-04-03', '2023-04-04', '2023-04-05', '2023-04-06', '2023-04-07', '2023-04-08', '2023-04-09'],
            15 => ['2023-04-10', '2023-04-11', '2023-04-12', '2023-04-13', '2023-04-14', '2023-04-15', '2023-04-16'],
            16 => ['2023-04-17', '2023-04-18', '2023-04-19', '2023-04-20', '2023-04-21', '2023-04-22', '2023-04-23'],
            17 => ['2023-04-24', '2023-04-25', '2023-04-26', '2023-04-27', '2023-04-28', '2023-04-29', '2023-04-30'],
        ], get_date_range_per_week('2023-04'));

        $this->assertEquals([
            22 => ['2023-06-01', '2023-06-02', '2023-06-03', '2023-06-04'],
            23 => ['2023-06-05', '2023-06-06', '2023-06-07', '2023-06-08', '2023-06-09', '2023-06-10', '2023-06-11'],
            24 => ['2023-06-12', '2023-06-13', '2023-06-14', '2023-06-15', '2023-06-16', '2023-06-17', '2023-06-18'],
            25 => ['2023-06-19', '2023-06-20', '2023-06-21', '2023-06-22', '2023-06-23', '2023-06-24', '2023-06-25'],
            26 => ['2023-06-26', '2023-06-27', '2023-06-28', '2023-06-29', '2023-06-30'],
        ], get_date_range_per_week('2023-06'));

        $this->assertEquals([
            39 => ['2023-10-01'],
            40 => ['2023-10-02', '2023-10-03', '2023-10-04', '2023-10-05', '2023-10-06', '2023-10-07', '2023-10-08'],
            41 => ['2023-10-09', '2023-10-10', '2023-10-11', '2023-10-12', '2023-10-13', '2023-10-14', '2023-10-15'],
            42 => ['2023-10-16', '2023-10-17', '2023-10-18', '2023-10-19', '2023-10-20', '2023-10-21', '2023-10-22'],
            43 => ['2023-10-23', '2023-10-24', '2023-10-25', '2023-10-26', '2023-10-27', '2023-10-28', '2023-10-29'],
            44 => ['2023-10-30', '2023-10-31'],
        ], get_date_range_per_week('2023-10'));
    }
}
