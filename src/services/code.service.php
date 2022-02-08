<?php

require_once(__DIR__ . "/../repositories/code.repository.php");
require_once("team.service.php");

class CodeService
{
    private $codeRepository;
    private $teamService;

    function __construct($codeRepository, $teamService)
    {
        $this->codeRepository = $codeRepository;
        $this->teamService = $teamService;
    }

    function initializeWordsForAllTeams($numberOfWords)
    {
        $teams = $this->teamService->getTeams();
    }
}
