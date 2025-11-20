<?php

namespace App\Helpers;

class Captcha
{
    /**
     * Generate a simple math captcha question and store answer in session
     */
    public static function generate(): array
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operations = ['+', '-', '*'];
        $operation = $operations[array_rand($operations)];

        $answer = match ($operation) {
            '+' => $num1 + $num2,
            '-' => $num1 - $num2,
            '*' => $num1 * $num2,
        };

        $question = "$num1 $operation $num2 = ?";

        // Store answer in session
        session(['captcha_answer' => $answer]);

        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }

    /**
     * Validate captcha answer
     */
    public static function validate(?string $userAnswer): bool
    {
        $correctAnswer = session('captcha_answer');

        if ($correctAnswer === null) {
            return false;
        }

        // Clear the captcha from session
        session()->forget('captcha_answer');

        return (int) $userAnswer === (int) $correctAnswer;
    }
}
