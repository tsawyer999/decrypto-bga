<?php

require_once(__DIR__ . "/../repositories/code.repository.php");

class CodeService
{
    private $codeRepository;

    public function __construct(CodeRepository $codeRepository)
    {
        $this->codeRepository = $codeRepository;
    }

    public function generateAllSequences(int $sequence_length, int $param_number_words): array
    {
        $availableItems = [];

        for ($i=1; $i<=$param_number_words; $i++)
        {
            array_push($availableItems, $i);
        }

        $results = [];
        $this->appendSequences($availableItems, $sequence_length, $results, 1, []);
        return $results;
    }

    private function appendSequences(array $availableItems, int $maxLevel, array &$results, int $level, array $selectedItems): void
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

                $this->appendSequences($clonedAvailableItems, $maxLevel, $results, $level + 1, $this->pureArrayPush($selectedItems, $value));
            }
        }
    }

    private function pureArrayPush($selectedItems, $value): array
    {
        $r = array_merge(array(), $selectedItems);
        array_push($r, $value);

        return $r;
    }
}
