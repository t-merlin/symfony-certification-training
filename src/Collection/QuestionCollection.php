<?php

declare(strict_types=1);

namespace App\Collection;

use App\Model\Question;

class QuestionCollection
{
    private array $questions;

    public function add(Question $question): void
    {
        $this->questions[] = $question;
    }

    /** @return Question[] */
    public function all(): array
    {
        return $this->questions;
    }

    public function count(): int
    {
        return count($this->questions);
    }

    public function shuffle(): void
    {
        shuffle($this->questions);
    }

    public function slice(int $length)
    {
        $this->questions = array_slice($this->questions, 0, $length);
    }
}