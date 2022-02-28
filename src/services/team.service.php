<?php

require_once(__DIR__ . "/../models/team.model.php");
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
}
