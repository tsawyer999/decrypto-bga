<?php

require_once(__DIR__ . "/../repositories/team.repository.php");

class TeamService
{
    private $teamRepository;

    function __construct($teamRepository)
    {
        $this->teamRepository = $teamRepository;
    }

    function newTeam($name)
    {
        return $this->teamRepository->newTeam($name);
    }

    function getTeams()
    {
        return $this->teamRepository->getTeams();
    }

    function changeTeamName($teamId, $teamName) {
        return $this->teamRepository->changeTeamName($teamId, $teamName);
    }

    function switchTeam($playerId) {
        return $this->teamRepository->switchTeam($playerId);
    }
}
