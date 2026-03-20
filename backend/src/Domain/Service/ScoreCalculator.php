<?php

namespace App\Domain\Service;
class ScoreCalculator
{
    public static function calculate(string $answers, string $solution): float
    {
        $given = explode('_', $answers);
        $expected = explode('_', $solution);
        $correct = 0;

        for ($i = 0; $i < count($expected); $i++) {
            if (isset($given[$i]) && $given[$i] == $expected[$i]) {
                $correct++;
            }
        }

        return $correct / count($expected);
    }
}
