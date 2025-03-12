<?php

declare(strict_types=1);

namespace App\Service;

use App\Collection\QuestionCollection;
use App\Enum\TrainingModeEnum;
use App\Model\Answer;
use App\Model\Question;
use Symfony\Component\Yaml\Yaml;

class QuestionCollector
{
    protected const TRAINING_LIMIT_QUESTIONS = 20;
    protected const EXAMEN_LIMIT_QUESTIONS = 80;

    public static function collect(string $trainingMode): QuestionCollection
    {
        $paths = Yaml::parse(file_get_contents(__DIR__ . '/../../config/questions/questions.yaml'));
        $questions = [];

        foreach ($paths as $path) {
            $questions = array_merge(
                $questions,
                Yaml::parse(file_get_contents(__DIR__ . '/../../config/questions/' . $path))
            );
        }

        $collection = new QuestionCollection();

        foreach ($questions as $question) {
            $questionModel = new Question(
                $question['question'],
                $question['correction_help'] ?? null,
                $question['code_snippet'],
            );

            foreach ($question['answers'] as $answer) {
                $answerModel = new Answer($answer['value'], $answer['correct']);
                $questionModel->addAnswer($answerModel);

                if ($answer['correct'] === true) {
                    $questionModel->addCorrectAnswer($answerModel);
                }
            }

            $collection->add($questionModel);
        }

        $collection->shuffle();

        $questionsLimitNumber = self::getQuestionLimit($trainingMode);
        $collection->slice($questionsLimitNumber);

        return $collection;
    }

    protected static function getQuestionLimit(string $trainingMode): int
    {
        return $trainingMode === TrainingModeEnum::TRAINING->value ?
            self::TRAINING_LIMIT_QUESTIONS :
            self::EXAMEN_LIMIT_QUESTIONS
        ;
    }
}