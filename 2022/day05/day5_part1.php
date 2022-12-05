<?php

$input = new SplFileObject('input.txt');

$containers = [];

function move9000(int $amount, int $from, int $to, array &$containers): void {
    for ($i = 0; $i < $amount; $i++) {
        $item = array_shift($containers[$from]);
        array_unshift($containers[$to], $item);
    }
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
    } else if ($key > 10) {
        // Command
        preg_match('/move (\d+) from (\d+) to (\d+)/', $line, $match);

        $amount = $match[1];
        $from = $match[2];
        $to = $match[3];

        move9000($amount, $from, $to, $containers);
    }
}

$output = array_reduce($containers, function ($output, $container) {
    $output .= $container[0];

    return $output;
}, '');

echo sprintf('Part1: %s', $output) . PHP_EOL;