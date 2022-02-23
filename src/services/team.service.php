<?php

require_once(__DIR__ . "/../repositories/team.repository.php");

class TeamService
{
    private $teamRepository;

    function __construct(TeamRepository $teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    function newTeam($name): void
    {
        $this->teamRepository->newTeam($name);
    }

    function getTeams(): array
    {
        return $this->teamRepository->getTeams();
    }

    function changeTeamName($teamId, $teamName): void
    {
        $this->teamRepository->changeTeamName($teamId, $teamName);
    }

    function switchTeam($playerId): int
    {
        return $this->teamRepository->switchTeam($playerId);
    }
}
