<?php

$rawInput = file_get_contents('input.txt');

$array = array_map(function ($items) {
    return array_sum(explode(PHP_EOL, $items));
}, explode(PHP_EOL.PHP_EOL, $rawInput));

echo 'part1: ' . max($array) . PHP_EOL;
arsort($array);
echo 'part2: ' . array_sum(array_splice($array, 0, 3)) . PHP_EOL;