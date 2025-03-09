<?php

declare(strict_types=1);

namespace App\Model;

class Answer
{
    private string $value;

    private bool $codeSnippet;

    private bool $correct;

    public function __construct(
        string $value,
        bool $correct = false,
    ) {
        $this->value = $value;
        $this->correct = $correct;
        $this->setCodeSnippet(false);
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setCodeSnippet(bool $codeSnippet): void
    {
        $this->codeSnippet = $codeSnippet;
    }

    public function isCodeSnippet(): bool
    {
        return $this->codeSnippet;
    }
}