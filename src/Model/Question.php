<?php

declare(strict_types=1);

namespace App\Model;

class Question
{
    private string $text;

    private array $answers;

    private array $correctAnswers;

    private ?string $codeSnippet;

    private array $correctionNotes;

    public function __construct(
        string $question,
        ?array $correctionNotes,
        ?string $codeSnippet = null,
    ) {
        $this->text = $question;
        $this->codeSnippet = $codeSnippet;
        $this->correctAnswers = [];
        $this->setCorrectionNotes($correctionNotes);
    }

    public function addAnswer(Answer $answer): void
    {
        $this->answers[] = $answer;
    }

    public function addCorrectAnswer(Answer $answer): void
    {
        $this->correctAnswers[] = $answer;
    }

    public function setCorrectionNotes(?array $correctionNotes): void
    {
        if ($correctionNotes) {
            $this->correctionNotes = $correctionNotes;
        }
    }

    public function getCorrectionNotes(): array
    {
        return $this->correctionNotes;
    }

    public function getText(): string
    {
        return $this->text;
    }

    /** @return Answer[] */
    public function getAnswers(): array
    {
        return $this->answers;
    }

    public function getCodeSnippet(): ?string
    {
        return $this->codeSnippet;
    }

    public function hasCodeSnippet(): bool
    {
        return $this->codeSnippet !== null;
    }

    /** @return Answer[] */
    public function getCorrectAnswers(): array
    {
        return $this->correctAnswers;
    }

    public function hasMultipleCorrectAnswers(): bool
    {
        return count($this->getCorrectAnswers()) > 1;
    }
}