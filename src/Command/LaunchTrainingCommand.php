<?php

declare(strict_types=1);

namespace App\Command;

use App\Collection\QuestionCollection;
use App\Model\Answer;
use App\Model\Question;
use App\Service\QuestionCollector;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: "trainer:launch",
    description: "Launch training session"
)]
class LaunchTrainingCommand extends Command
{
    private QuestionCollection $questionCollection;

    private QuestionCollection $answeredQuestions;

    private int $correctAnswers = 0;

    private int $wrongAnswers = 0;

    public function __construct()
    {
        parent::__construct();
        $this->questionCollection = QuestionCollector::collect();
        $this->answeredQuestions = new QuestionCollection();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->greetTester($io);
        $this->collectQuestions($io);
        $this->startTraining($input, $output, $io);
        $this->renderResults($io);

        return Command::SUCCESS;
    }

    protected function greetTester(SymfonyStyle $io): void
    {
        $io->title('Symfony Certification Training !');
        $io->text('This CLI tool will help you preparing your certification ! Hope you\'ll have a good time using it !');
        $io->note('If you see an error according to you in one of the questions, please open an issue !');
    }

    protected function collectQuestions(SymfonyStyle $io): void
    {
        $table = $io->createTable();
        $table->setHeaders(['Number of questions available']);
        $table->setRow(0, [$this->questionCollection->count()]);

        $table->render();
    }

    protected function startTraining(
        InputInterface $input,
        OutputInterface $output,
        SymfonyStyle $io
    ): void {
        $io->newLine();
        $io->section('Starting training session');
        $helper = $this->getHelper('question');

        $outputStyle = new OutputFormatterStyle('#000000', '#ECECEC');
        $output->getFormatter()->setStyle('snippet', $outputStyle);

        foreach ($this->questionCollection->all() as $index => $question) {
            $this->displayQuestion($io, ++$index, $question->getText());

            if ($question->hasCodeSnippet()) {
                $this->displayQuestionCodeSnippet($io, $question);
            }

            $answers = $question->getAnswers();
            shuffle($answers);

            $choiceQuestion = new ChoiceQuestion(
                ' Choose your answer :',
                $answers,
                0
            );

            if ($question->hasMultipleCorrectAnswers()) {
                $choiceQuestion->setMultiselect(true);
            }

            $answers = $helper->ask($input, $output, $choiceQuestion);

            $isCorrectAnswer = $this->handleAnswers($answers, $question);
            $this->answeredQuestions->add($question);
            $this->provideCorrectionHelp($question, $isCorrectAnswer, $io);

            $io->newLine();
        }
    }

    protected function displayQuestion(
        SymfonyStyle $io,
        int $questionNumber,
        string $question
    ): void {
        $io->text('QUESTION N°' . $questionNumber . ' : ' . $question);
    }

    protected function displayQuestionCodeSnippet(
        SymfonyStyle $io,
        Question $question
    ): void {
        if ($question->hasCodeSnippet()) {
            $io->newLine();

            $codeSnippet = $question->getCodeSnippet();
            $lines = explode("\n", $codeSnippet);
            $maxLength = max(array_map('strlen', $lines));

            $io->text('<snippet>   ' . str_repeat(' ', $maxLength) . '   </snippet>');

            foreach ($lines as $line) {
                $io->text('<snippet>   ' . str_pad($line, $maxLength) . '   </snippet>');
            }

            $io->text('<snippet>   ' . str_repeat(' ', $maxLength) . '   </snippet>');

            $io->newLine();
        }
    }

    protected function handleAnswers(
        Answer|array $answers,
        Question $question
    ): bool {
        $correctAnswers = $question->getCorrectAnswers();

        if ($answers instanceof Answer) {
            $answers = [$answers];
        }

        sort($answers);
        sort($correctAnswers);

        $isCorrectAnswer = $answers === $correctAnswers;
        $isCorrectAnswer ? $this->correctAnswers++ : $this->wrongAnswers++;

        return $isCorrectAnswer;
    }

    protected function provideCorrectionHelp(
        Question $question,
        bool $isCorrectAnswer,
        SymfonyStyle $io
    ): void {
        if ($isCorrectAnswer) {
            $io->text('✅ <info>CORRECT !</info>');
        } else {
            $io->text('❌ <error>WRONG !</error>');
        }

        $io->text('Here is some additional links/notes about the question :');

        $table = $io->createTable();
        $table->setHeaders(['Links & Notes']);

        foreach ($question->getCorrectionNotes() as $index => $note) {
            $table->setRow($index, [$note]);
        }

        $table->render();
    }

    protected function renderResults(SymfonyStyle $io): void
    {
        $io->section('Training results');

        $table =  $io->createTable();
        $table->setHeaders(
            [
                'Number of questions asked',
                'Number of correct answers',
                'Number of wrong answers'
            ]
        );

        $table->setRow(
            0,
            [
                $this->questionCollection->count(),
                $this->correctAnswers,
                $this->wrongAnswers
            ]
        );

        $table->render();
    }
}