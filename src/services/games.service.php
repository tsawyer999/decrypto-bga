<?php

require_once(__DIR__ . "/../repositories/games.repository.php");
require_once(__DIR__ . "/../models/dictionary-random-picker.php");
require_once(__DIR__ . "/../models/turn.model.php");
require_once(__DIR__ . "/../models/hint.model.php");

class GamesService
{
    private GamesRepository $gamesRepository;
    private TeamsRepository $teamsRepository;

    public function __construct(GamesRepository $gameRepository, TeamsRepository $teamRepository)
    {
        $this->gamesRepository = $gameRepository;
        $this->teamsRepository = $teamRepository;
    }

    public function startGame(int $sequence_length, int $param_number_words): void
    {
        $this->setWordsForAllTeams($param_number_words);
        $this->setCodesForGame($sequence_length, $param_number_words);

        $turn = new Turn(0, 0);
        $this->gamesRepository->saveTurn($turn);
    }

    public function moveToNextTurn(): void
    {
        $currentTurn = $this->gamesRepository->getCurrentTurn();
        $teams = $this->teamsRepository->getTeams();

        $nextTurn = $this->calculateNextTurn($currentTurn, $teams);
        $this->gamesRepository->saveTurn($nextTurn);
    }

    private function calculateNextTurn(Turn $currentTurn, array $teams): Turn
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

    public function getWordsForPlayer(int $player_id): array
    {
        return $this->gamesRepository->getWordsForPlayer($player_id);
    }

    private function setCodesForGame($sequence_length, $param_number_words)
    {
        $codes = $this->generateAllCodes($sequence_length, $param_number_words);
        $this->saveCodes($codes);
    }

    private function saveCodes(array $codes): void
    {
        $this->gamesRepository->saveCodes($codes);
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
        $words = $this->gamesRepository->getWords();
        $dictionary = new DictionaryRandomPicker($words);

        $teams = $this->teamsRepository->getTeams();
        foreach ($teams as $team)
        {
            $teamWords = [];
            for ($i=0; $i<$param_number_words; $i++)
            {
                $word = $dictionary->pick();
                array_push($teamWords, $word);
            }
            $this->gamesRepository->saveWords($team->id, $teamWords);
        }
    }

    public function getPlayerIdForGiveHints(): int
    {
        $turn = $this->gamesRepository->getCurrentTurn();
        $teams = $this->teamsRepository->getTeams();

        $numberOfPlayers = count($teams[$turn->turn_number]->playerIds);
        return $teams[$turn->turn_number]->playerIds[$turn->round_number % $numberOfPlayers];
    }

    public function giveHints(int $playerId, array $hints): void
    {
        $turn = $this->gamesRepository->getCurrentTurn();

        $hint = new Hint();
        $hint->player_id = $playerId;
        $hint->turn_id = $turn->id;
        $hint->value = $hints;

        $this->gamesRepository->saveHints($hint);
    }

    public function getHintsForCurrentTurn(): array
    {
        $turn = $this->gamesRepository->getCurrentTurn();

        return $this->gamesRepository->getHints($turn->id);
    }

    public function newCodeForPlayer(int $current_player_id): array
    {
        $codes = $this->gamesRepository->getAvailableCodes($current_player_id);
        $randomIndex = bga_rand(0, count($codes));
        $selectedCode = $codes[$randomIndex];
        $turn = $this->gamesRepository->getCurrentTurn();

        $this->gamesRepository->saveDrawCode($turn->id, $selectedCode->id, $current_player_id);

        return $selectedCode->value;
    }
}
