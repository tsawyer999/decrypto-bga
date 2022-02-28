<?php

require_once(__DIR__ . "/../data/words.php");

require_once(__DIR__ . "/../models/team.model.php");
require_once(__DIR__ . "/../models/dictionary-random-picker.php");

require_once(__DIR__ . "/../repositories/team.repository.php");

class TeamService
{
    private $teamRepository;
    private $teamId;

    function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
        $this->teamId = 0;
    }

    public function createTeam(): void
    {
        $this->teamId++;
        $team = new Team($this->teamId, "team", $this->teamId, []);
        $this->teamRepository->saveTeam($team);
    }

    public function getTeams(): array
    {
        return $this->teamRepository->getTeams();
    }

    public function changeTeamName($teamId, $teamName): void
    {
        $this->teamRepository->changeTeamName($teamId, $teamName);
    }

    public function switchTeam($playerId): int
    {
        return $this->teamRepository->switchTeam($playerId);
    }

    public function setWordsForAllTeams(int $param_number_words): void
    {
        $words = $this->teamRepository->getWords();
        $teamWords = [];
        $dictionary = new DictionaryRandomPicker($words);

        $teams = $this->teamRepository->getTeams();
        foreach ($teams as $team)
        {
            for ($i=0; $i<$param_number_words; $i++)
            {
                $word = $dictionary->pick();
                array_push($teamWords, $word);
            }
            $this->teamRepository->updateWords($team->id, $teamWords);
        }
    }
}
