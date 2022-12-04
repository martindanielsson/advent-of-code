<?php

$rawInput = file_get_contents('input.txt');

$overlaps = array_map(function ($line) {
    $groups = explode(',', $line);
    $group1 = explode('-', $groups[0]);
    $group2 = explode('-', $groups[1]);

    return ($group1[0] <= $group2[0] && $group1[1] >= $group2[1])
        || ($group1[0] >= $group2[0] && $group1[1] <= $group2[1]);
}, explode(PHP_EOL, $rawInput));

echo 'Part 1: ' . count(array_filter($overlaps)) . PHP_EOL;