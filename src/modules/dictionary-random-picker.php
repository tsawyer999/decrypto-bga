<?php

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