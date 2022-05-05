<?php

require_once(__DIR__ . "/../models/team.model.php");
require_once(__DIR__ . "/../repositories/teams.repository.php");

class TeamsService
{
    private TeamsRepository $teamsRepository;
    private int $teamId;

    function __construct(TeamsRepository $teamRepository)
    {
        $this->teamsRepository = $teamRepository;
        $this->teamId = 0;
    }

    public function createTeam(): void
    {
        $this->teamId++;
        $team = new Team($this->teamId, "team", $this->teamId, []);
        $this->teamsRepository->saveTeam($team);
    }

    public function getTeams(): array
    {
        return $this->teamsRepository->getTeams();
    }

    public function changeTeamName($teamId, $teamName): void
    {
        $this->teamsRepository->changeTeamName($teamId, $teamName);
    }

    public function switchTeam($playerId): int
    {
        return $this->teamsRepository->switchTeam($playerId);
    }
}
