<?php

$rawInput = file_get_contents('input.txt');

class ElvenSignalSystem {
    protected int $counter;
    protected int $markerLength;
    protected array $data;

    public function __construct(int $markerLength)
    {
        $this->counter = 0;
        $this->data = [];
        $this->markerLength = $markerLength;
    }

    public function addItem(string $input): void
    {
        if (count($this->data) === $this->markerLength) {
            array_shift($this->data);
        }

        $this->data[] = $input;
        $this->counter++;
    }

    public function getMarkerStart(): int
    {
        if (count($this->data) !== $this->markerLength) {
            return 0;
        }

        if (array_unique($this->data) !== $this->data) {
            return 0;
        }

        return $this->counter;
    }

    public function getMarker(): string
    {
        if ($this->getMarkerStart()) {
            return implode($this->data);
        }

        return '';
    }
}

$startOfPacket = new ElvenSignalSystem(4);
$markerStart = 0;

foreach (str_split($rawInput) as $value) {
    $startOfPacket->addItem($value);

    if ($markerStart = $startOfPacket->getMarkerStart()) {
        break;
    }
}

echo sprintf('Part1: marker after %s character: %s', $markerStart, $startOfPacket->getMarker()) . PHP_EOL;

$startOfMessage = new ElvenSignalSystem(14);
$markerStart = 0;

foreach (str_split($rawInput) as $value) {
    $startOfMessage->addItem($value);

    if ($markerStart = $startOfMessage->getMarkerStart()) {
        break;
    }
}

echo sprintf('Part2: marker after %s character: %s', $markerStart, $startOfMessage->getMarker()) . PHP_EOL;