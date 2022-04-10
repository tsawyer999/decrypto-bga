<?php

require_once(__DIR__ . "/../repositories/game.repository.php");
require_once(__DIR__ . "/../models/dictionary-random-picker.php");
require_once(__DIR__ . "/../models/turn.model.php");

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

        $turn = new Turn(0, 0);
        $this->gameRepository->saveTurn($turn);
    }

    public function moveToNextTurn(): void
    {
        $currentTurn = $this->gameRepository->getCurrentTurn();
        $teams = $this->teamRepository->getTeams();

        $nextTurn = $this->calculateNextTurn($currentTurn, $teams);
        $this->gameRepository->saveTurn($nextTurn);
    }

    private function calculateNextTurn(Turn $currentTurn, $teams): Turn
    {
        $round_number = $currentTurn->round_number;
        $turn_number = $currentTurn->turn_number;

        if ($turn_number + 1 == count($teams))
        {
            $round_number++;
            $turn_number = 0;
        }
        else
        {
            $turn_number++;
        }

        return new Turn($round_number, $turn_number);
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

    public function getPlayerIdForGiveHints(): int
    {
        $turn = $this->gameRepository->getCurrentTurn();
        $teams = $this->teamRepository->getTeams();

        $numberOfPlayers = count($teams[$turn->turn_number]->playerIds);
        return $teams[$turn->turn_number]->playerIds[$turn->round_number % $numberOfPlayers];
    }
}
