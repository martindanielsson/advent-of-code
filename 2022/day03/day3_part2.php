<?php

$position = range('a', 'z');
$rawInput = file_get_contents('input.txt');

$priorities = array_map(function ($lines) use ($position) {
    $shared = array_unique(array_intersect(str_split($lines[0]), str_split($lines[1]), str_split($lines[2])));

    $priority = 0;

    foreach ($shared as $letter) {
        $offset = ctype_upper($letter) ? 27 : 1;
        $priority += (array_search(strtolower($letter), $position) + $offset);
    }

    return $priority;
}, array_chunk(explode(PHP_EOL, $rawInput), 3));

echo 'Part 2: ' . array_sum($priorities) . PHP_EOL;