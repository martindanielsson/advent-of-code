<?php
const OPPONENT_ROCK = 'A';
const OPPONENT_PAPER = 'B';
const OPPONENT_SCISSORS = 'C';
const PLAYER_LOSE = 'X';
const PLAYER_DRAW = 'Y';
const PLAYER_WIN = 'Z';
const POINTS_ROCK = 1;
const POINTS_PAPER = 2;
const POINTS_SCISSORS = 3;
const POINTS_WIN = 6;
const POINTS_DRAW = 3;
const POINTS_LOSS = 0;

$points = [
    OPPONENT_ROCK . ' ' . PLAYER_WIN => POINTS_PAPER + POINTS_WIN,
    OPPONENT_ROCK . ' ' . PLAYER_DRAW => POINTS_ROCK + POINTS_DRAW,
    OPPONENT_ROCK . ' ' . PLAYER_LOSE => POINTS_SCISSORS + POINTS_LOSS,
    OPPONENT_PAPER . ' ' . PLAYER_WIN => POINTS_SCISSORS + POINTS_WIN,
    OPPONENT_PAPER . ' ' . PLAYER_DRAW => POINTS_PAPER + POINTS_DRAW,
    OPPONENT_PAPER . ' ' . PLAYER_LOSE => POINTS_ROCK + POINTS_LOSS,
    OPPONENT_SCISSORS . ' ' . PLAYER_WIN => POINTS_ROCK + POINTS_WIN,
    OPPONENT_SCISSORS . ' ' . PLAYER_DRAW => POINTS_SCISSORS + POINTS_DRAW,
    OPPONENT_SCISSORS . ' ' . PLAYER_LOSE => POINTS_PAPER + POINTS_LOSS,
];

$rawInput = file_get_contents('input.txt');

$scores = array_map(function ($round) use ($points) {
    return $points[$round];
}, explode(PHP_EOL, $rawInput));

echo 'Part 2: Total score: ' . array_sum($scores) . PHP_EOL;