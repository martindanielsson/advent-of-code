<?php

$position = range('a', 'z');
$rawInput = file_get_contents('input.txt');

$priorities = array_map(function ($line) use ($position) {
    $parts = str_split($line, strlen($line) / 2);
    $shared = array_unique(array_intersect(str_split($parts[0]), str_split($parts[1])));

    $priority = 0;

    foreach ($shared as $letter) {
        $offset = ctype_upper($letter) ? 27 : 1;
        $priority += (array_search(strtolower($letter), $position) + $offset);
    }

    return $priority;
}, explode(PHP_EOL, $rawInput));

echo 'Part 1: ' . array_sum($priorities) . PHP_EOL;