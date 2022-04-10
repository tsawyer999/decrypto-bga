<?php

require_once(__DIR__ . "/../repositories/game.repository.php");
require_once(__DIR__ . "/../models/dictionary-random-picker.php");

class GameService
{
    private GameRepository $gameRepository;
    private TeamRepository $teamRepository;

    public function __construct(GameRepository $gameRepository, TeamRepository $teamRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->teamRepository = $teamRepository;
    }

    public function startGame(int $sequence_length, int $param_number_words)
    {
        $this->setWordsForAllTeams($param_number_words);
        $this->setCodesForGame($sequence_length, $param_number_words);
        $this->gameRepository->insertTurn(0, 0);
    }

    public function getWordsForPlayer($player_id): array
    {
        return $this->gameRepository->getWordsForPlayer($player_id);
    }

    private function setCodesForGame($sequence_length, $param_number_words)
    {
        $codes = $this->generateAllCodes($sequence_length, $param_number_words);
        $this->saveCodes($codes);
    }

    private function saveCodes(array $codes): void
    {
        $this->gameRepository->saveCodes($codes);
    }

    private function generateAllCodes(int $sequence_length, int $param_number_words): array
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

    private function setWordsForAllTeams(int $param_number_words): void
    {
        $words = $this->gameRepository->getWords();
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
            $this->gameRepository->saveWords($team->id, $teamWords);
        }
    }
}
