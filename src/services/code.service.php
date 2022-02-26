<?php

require_once(__DIR__ . "/../repositories/code.repository.php");

class CodeService
{
    private $codeRepository;

    function __construct(CodeRepository $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    function initializeWordsForAllTeams(int $numberOfWords): void
    {
        /*
                $words = str_getcsv(french);
                $dictionary = new DictionaryRandomPicker($words);

                $teams = $this->teamService->getTeams();
                foreach ($teams as $team)
                {
                    for ($i=0; $i<$numberOfWords; $i++) {
                        $word = $dictionary->pick();

                        $this->codeRepository->newWord();
                        $team

                    }
        }
        */
    }
}
