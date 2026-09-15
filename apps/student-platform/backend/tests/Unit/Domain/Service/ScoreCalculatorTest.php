<?php

namespace App\Tests\Unit\Domain\Service;

use App\Domain\Service\ScoreCalculator;
use PHPUnit\Framework\TestCase;

class ScoreCalculatorTest extends TestCase
{
    public function test_perfect_score_returns_one(): void
    {
        $score = ScoreCalculator::calculate('1_0_2', '1_0_2');

        $this->assertEquals(1.0, $score);
        $this->assertEquals(1.0, $score);
    }

    public function test_zero_score_when_all_wrong(): void
    {
        $score = ScoreCalculator::calculate('9_9_9', '1_0_2');

        $this->assertEquals(0.0, $score);
    }

    public function test_partial_score(): void
    {
        $score = ScoreCalculator::calculate('1_1_2', '1_0_2');

        $this->assertEquals(0.6666666666666666, $score);
        $this->assertEquals(0.6666666666666666, $score);
    }

    public function test_two_out_of_three_correct(): void
    {
        $score = ScoreCalculator::calculate('-2_40_99', '-2_40_56');

        $this->assertEquals(0.6666666666666666, $score);
    }
}
