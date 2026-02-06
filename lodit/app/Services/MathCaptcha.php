<?php

namespace App\Services;

class MathCaptcha
{
    /**
     * Check if user is online (has internet connection)
     * Uses faster DNS check with short timeout
     */
    public static function isOnline()
    {
        // Quick DNS check with 1 second timeout (faster than socket connection)
        $host = gethostbyname('google.com');
        
        // If DNS resolution failed, we're offline
        if ($host === 'google.com') {
            return false;
        }
        
        return true;
    }
    
    /**
     * Generate a random math captcha
     * Returns array with question, operand1, operand2, operator, and answer
     */
    public static function generate()
    {
        $operand1 = rand(1, 20);
        $operand2 = rand(1, 20);
        $operator = rand(0, 1); // 0 for +, 1 for -
        
        if ($operator == 0) {
            $operation = '+';
            $answer = $operand1 + $operand2;
        } else {
            $operation = '-';
            // Ensure result is positive
            if ($operand1 < $operand2) {
                $temp = $operand1;
                $operand1 = $operand2;
                $operand2 = $temp;
            }
            $answer = $operand1 - $operand2;
        }
        
        $question = "{$operand1} {$operation} {$operand2}";
        
        return [
            'question' => $question,
            'answer' => $answer,
        ];
    }
    
    /**
     * Validate captcha answer
     */
    public static function validate($userAnswer, $correctAnswer)
    {
        return intval($userAnswer) === intval($correctAnswer);
    }
}

