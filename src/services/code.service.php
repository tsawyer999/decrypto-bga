<?php

require_once(__DIR__ . "/../data/words.php");
require_once(__DIR__ . "/../repositories/code.repository.php");
require_once("team.service.php");

class CodeService
{
    private $codeRepository;
    private $teamService;

    function __construct(CodeRepository $codeRepository, TeamService $teamService)
    {
        $this->codeRepository = $codeRepository;
        $this->teamService = $teamService;
    }

    function initializeWordsForAllTeams(int $numberOfWords): void
    {
        $words = str_getcsv(french);
        $dictionary = new DictionaryRandomPicker($words);

        $teams = $this->teamService->getTeams();
        foreach ($teams as $team)
        {
            for ()
            $word = $dictionary->pick();

            $this->codeRepository->newWord();
            $team
        }
    }
}
