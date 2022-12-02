<?php
const OPPONENT_ROCK = 'A';
const OPPONENT_PAPER = 'B';
const OPPONENT_SCISSORS = 'C';
const PLAYER_ROCK = 'X';
const PLAYER_PAPER = 'Y';
const PLAYER_SCISSORS = 'Z';
const POINTS_ROCK = 1;
const POINTS_PAPER = 2;
const POINTS_SCISSORS = 3;
const POINTS_WIN = 6;
const POINTS_DRAW = 3;
const POINTS_LOSS = 0;

$points = [
    OPPONENT_ROCK . ' ' . PLAYER_PAPER => POINTS_PAPER + POINTS_WIN,
    OPPONENT_ROCK . ' ' . PLAYER_ROCK => POINTS_ROCK + POINTS_DRAW,
    OPPONENT_ROCK . ' ' . PLAYER_SCISSORS => POINTS_SCISSORS + POINTS_LOSS,
    OPPONENT_PAPER . ' ' . PLAYER_PAPER => POINTS_PAPER + POINTS_DRAW,
    OPPONENT_PAPER . ' ' . PLAYER_ROCK => POINTS_ROCK + POINTS_LOSS,
    OPPONENT_PAPER . ' ' . PLAYER_SCISSORS => POINTS_SCISSORS + POINTS_WIN,
    OPPONENT_SCISSORS . ' ' . PLAYER_PAPER => POINTS_PAPER + POINTS_LOSS,
    OPPONENT_SCISSORS . ' ' . PLAYER_ROCK => POINTS_ROCK + POINTS_WIN,
    OPPONENT_SCISSORS . ' ' . PLAYER_SCISSORS => POINTS_SCISSORS + POINTS_DRAW,
];

$rawInput = file_get_contents('input.txt');

$scores = array_map(function ($round) use ($points) {
    return $points[$round];
}, explode(PHP_EOL, $rawInput));

echo 'Part 1: Total score: ' . array_sum($scores) . PHP_EOL;