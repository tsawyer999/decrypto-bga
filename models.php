<?php
class Team {
    public function __construct($name) {
        $this->name = $name;
    }
}

class Round {
    public function __construct() {
        $this->hints = ["word1", "word2", "word3"];
        $this->guess_sequence = [1, 3, 2];
        $this->correct_sequence = [1, 3, 2];
    }
}

class DictionaryRandomPicker {

    var $dictionary = [];
    var $items = [];

    public function __construct(array $dictionary) {
        $this->dictionary = $dictionary;
        $this->reset();
    }

    public function pick(): string {
        $index = array_shift($this->items);
        return $this->dictionary[$index];
    }

    public function reset(): void {
        $range = range(0, count($this->dictionary));
        shuffle($range);
        $this->items = $range;
    }
}