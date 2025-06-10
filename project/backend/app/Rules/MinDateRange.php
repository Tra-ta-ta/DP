<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Carbon\Carbon;

class MinDateRange implements ValidationRule
{
    protected string $startDate;

    public function __construct(string $startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * Run the validation rule.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $start = Carbon::parse($this->startDate);
        $end = Carbon::parse($value);

        if ($start->diffInDays($end) > 30) {
            $fail('Разница между начальной и конечной датой должна быть не более 30 дней.');
        }
    }
}
