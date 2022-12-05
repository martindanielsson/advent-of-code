<?php

$input = new SplFileObject('input.txt');

$containers = [];

function move9001(int $amount, int $from, int $to, array &$containers): void {
    $fromContainer = $containers[$from];
    $movedItems = array_splice($fromContainer, count($fromContainer) - $amount, $amount);
    $toContainer = array_merge($containers[$to], $movedItems);
    $containers[$from] = $fromContainer;
    $containers[$to] = $toContainer;
}

while (! $input->eof()) {
    $line = $input->fgets();
    $key = $input->key();

    if ($key < 9) {
        // Setup
        $items = str_split($line, 4);

        foreach ($items as $key => $item) {
            if ($sanitized = trim($item, PHP_EOL . ' []')) {
                $containers[$key + 1][] = $sanitized;
            }
        }
    } else if ($key === 9) {
        // Finalize containers
        ksort($containers);
        $containers = array_map(fn ($items) => array_reverse($items), $containers);
    } else if ($key > 10) {
        // Command
        preg_match('/move (\d+) from (\d+) to (\d+)/', $line, $match);

        $amount = $match[1];
        $from = $match[2];
        $to = $match[3];

        move9001($amount, $from, $to, $containers);
    }
}

$containers = array_map(fn ($items) => array_reverse($items), $containers);

$output = array_reduce($containers, function ($output, $container) {
    $output .= $container[0];

    return $output;
}, '');

echo sprintf('Part2: %s', $output) . PHP_EOL;