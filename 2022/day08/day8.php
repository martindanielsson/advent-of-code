<?php

class Grid {
    protected int $width;
    protected int $height;
    protected array $trees;

    public function __construct(int $width, int $height, array $trees)
    {
        $this->width = $width;
        $this->height = $height;
        $this->trees = $trees;
    }

    public function countVisibleTrees(): int
    {
        $visible = 0;

        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                if ($x === 0 || $x === ($this->width - 1)) {
                    $visible++;
                } else if ($y === 0 || $y === ($this->height - 1)) {
                    $visible++;
                } else if ($this->isVisible($x, $y, $this->trees[$y][$x])) {
                    $visible++;
                }
            }
        }

        return $visible;
    }

    public function findBestViewingTree(): int
    {
        $bestScore = 0;

        for ($x = 0; $x < $this->width; $x++) {
            for ($y = 0; $y < $this->height; $y++) {
                $score = $this->countViewingScore($y, $x, $this->trees[$y][$x]);

                $bestScore = max($score, $bestScore);
            }
        }

        return $bestScore;
    }

    protected function isVisible(int $x, int $y, int $treeHeight): bool
    {
        return $this->isVisibleWest($x, $y, $treeHeight) ||
            $this->isVisibleEast($x, $y, $treeHeight) ||
            $this->isVisibleNorth($x, $y, $treeHeight) ||
            $this->isVisibleSouth($x, $y, $treeHeight);
    }

    protected function isVisibleWest(int $x, int $y, int $treeHeight): bool
    {
        $checkX = $x - 1;
        $visible = true;

        while ($checkX >= 0) {
            if ($this->trees[$y][$checkX] >= $treeHeight) {
                $visible = false;
                break;
            }

            $checkX--;
        }

        return $visible;
    }

    protected function isVisibleEast(int $x, int $y, int $treeHeight): bool
    {
        $checkX = $x + 1;
        $visible = true;

        while ($checkX < $this->width) {
            if ($this->trees[$y][$checkX] >= $treeHeight) {
                $visible = false;
                break;
            }

            $checkX++;
        }

        return $visible;
    }

    protected function isVisibleNorth(int $x, int $y, int $treeHeight): bool
    {
        $checkY = $y - 1;
        $visible = true;

        while ($checkY >= 0) {
            if ($this->trees[$checkY][$x] >= $treeHeight) {
                $visible = false;
                break;
            }

            $checkY--;
        }

        return $visible;
    }

    protected function isVisibleSouth(int $x, int $y, int $treeHeight): bool
    {
        $checkY = $y + 1;
        $visible = true;

        while ($checkY < $this->height) {
            if ($this->trees[$checkY][$x] >= $treeHeight) {
                $visible = false;
                break;
            }

            $checkY++;
        }

        return $visible;
    }

    protected function countViewingScore(int $y, int $x, int $treeHeight): int
    {
        return $this->countScoreWest($x, $y, $treeHeight) *
            $this->countScoreEast($x, $y, $treeHeight) *
            $this->countScoreNorth($x, $y, $treeHeight) *
            $this->countScoreSouth($x, $y, $treeHeight);
    }

    protected function countScoreWest(int $x, int $y, int $treeHeight): int
    {
        $checkX = $x - 1;
        $score = 0;

        while ($checkX >= 0) {
            $score++;

            if ($this->trees[$y][$checkX] >= $treeHeight) {
                break;
            }

            $checkX--;
        }

        return $score;
    }

    protected function countScoreEast(int $x, int $y, int $treeHeight): int
    {
        $checkX = $x + 1;
        $score = 0;

        while ($checkX < $this->width) {
            $score++;

            if ($this->trees[$y][$checkX] >= $treeHeight) {
                break;
            }

            $checkX++;
        }

        return $score;
    }

    protected function countScoreNorth(int $x, int $y, int $treeHeight): int
    {
        $checkY = $y - 1;
        $score = 0;

        while ($checkY >= 0) {
            $score++;

            if ($this->trees[$checkY][$x] >= $treeHeight) {
                break;
            }

            $checkY--;
        }

        return $score;
    }

    protected function countScoreSouth(int $x, int $y, int $treeHeight): int
    {
        $checkY = $y + 1;
        $score = 0;

        while ($checkY < $this->height) {
            $score++;

            if ($this->trees[$checkY][$x] >= $treeHeight) {
                break;
            }

            $checkY++;
        }

        return $score;
    }
}

$rawInput = file_get_contents('input.txt');

$input = array_map('str_split', explode(PHP_EOL, $rawInput));

$grid = new Grid(count($input[0]), count($input), $input);

$visible = $grid->countVisibleTrees();
$score = $grid->findBestViewingTree();

echo sprintf('Part1: Found %s visible trees in the grid.', $visible) . PHP_EOL;
echo sprintf('Part2: The best tree gets a score of: %s', $score) . PHP_EOL;