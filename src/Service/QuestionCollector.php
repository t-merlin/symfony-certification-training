<?php

declare(strict_types=1);

namespace App\Service;

use App\Collection\QuestionCollection;
use App\Model\Answer;
use App\Model\Question;
use Symfony\Component\Yaml\Yaml;

class QuestionCollector
{
    public static function collect(): QuestionCollection
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
                if (array_key_exists('code_snippet', $answer)) {
                    $answerModel->setCodeSnippet($answer['code_snippet']);
                }
                $questionModel->addAnswer($answerModel);

                if ($answer['correct'] === true) {
                    $questionModel->addCorrectAnswer($answerModel);
                }
            }

            $collection->add($questionModel);
        }

        $collection->shuffle();

        return $collection;
    }
}