<?php

declare(strict_types=1);

namespace App\Model;

class Answer
{
    private string $value;

    private bool $correct;

    public function __construct(
        string $value,
        bool $correct = false,
    ) {
        $this->value = $value;
        $this->correct = $correct;
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getCorrect(): bool
    {
        return $this->correct;
    }
}