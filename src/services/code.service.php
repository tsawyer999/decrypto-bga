<?php

require_once(__DIR__ . "/../repositories/code.repository.php");
require_once(__DIR__ . "/../models/dictionary-random-picker.php");

class CodeService
{
    private CodeRepository $codeRepository;
    private TeamRepository $teamRepository;

    public function __construct(CodeRepository $codeRepository, TeamRepository $teamRepository)
    {
        $this->codeRepository = $codeRepository;
        $this->teamRepository = $teamRepository;
    }

    public function saveCodes(array $codes): void
    {
        $this->codeRepository->saveCodes($codes);
    }

    public function generateAllCodes(int $sequence_length, int $param_number_words): array
    {
        $availableItems = [];

        for ($i=1; $i<=$param_number_words; $i++)
        {
            array_push($availableItems, $i);
        }

        $results = [];
        $this->appendCodes($availableItems, $sequence_length, $results, 1, []);
        return $results;
    }

    private function appendCodes(array $availableItems, int $maxLevel, array &$results, int $level, array $selectedItems): void
    {
        for ($i=0; $i<count($availableItems); $i++)
        {
            $value = $availableItems[$i];

            if ($level === $maxLevel)
            {
                array_push($results, $this->pureArrayPush($selectedItems, $value));
            }
            else
            {
                $clonedAvailableItems = array_merge(array(), $availableItems);
                array_splice($clonedAvailableItems, $i, 1);

                $this->appendCodes($clonedAvailableItems, $maxLevel, $results, $level + 1, $this->pureArrayPush($selectedItems, $value));
            }
        }
    }

    private function pureArrayPush($selectedItems, $value): array
    {
        $r = array_merge(array(), $selectedItems);
        array_push($r, $value);

        return $r;
    }

    public function getWordsForPlayer($player_id): array
    {
        return $this->codeRepository->getWordsForPlayer($player_id);
    }

    public function setWordsForAllTeams(int $param_number_words): void
    {
        $words = $this->codeRepository->getWords();
        $dictionary = new DictionaryRandomPicker($words);

        $teams = $this->teamRepository->getTeams();
        foreach ($teams as $team)
        {
            $teamWords = [];
            for ($i=0; $i<$param_number_words; $i++)
            {
                $word = $dictionary->pick();
                array_push($teamWords, $word);
            }
            $this->codeRepository->saveWords($team->id, $teamWords);
        }
    }
}
