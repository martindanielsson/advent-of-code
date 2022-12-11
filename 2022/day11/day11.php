<?php

class KeepAway {
    protected array $monkeys;
    protected int $round = 1;
    protected ?int $worryReduction = null;
    protected bool $reportProgress = false;

    function withMonkeys(array $monkeys): self
    {
        $this->monkeys = $monkeys;

        return $this;
    }

    public function parseInput(): array
    {
        $rawInput = file_get_contents('input.txt');
        $input = explode(PHP_EOL, $rawInput);
        $monkeys = [];
        $monkey = null;

        foreach ($input as $line) {
            if (str_contains($line, 'Monkey')) {
                $monkey = [
                    'inspections' => 0,
                ];
            } else if (str_contains($line, 'Starting items:')) {
                $monkey['items'] = explode(', ', trim(substr($line, 18), PHP_EOL));
            } else if (str_contains($line, 'Operation:')) {
                $monkey['operation'] = trim(substr($line, 23), PHP_EOL);
            } else if (str_contains($line, 'Test:')) {
                $monkey['divide_by'] = trim(substr($line, 21), PHP_EOL);
            } else if (str_contains($line, 'If true:')) {
                $monkey['if_true'] = trim(substr($line, 29), PHP_EOL);
            } else if (str_contains($line, 'If false:')) {
                $monkey['if_false'] = trim(substr($line, 30), PHP_EOL);
                $monkeys[] = $monkey;
                $monkey = [];
            }
        }

        return $monkeys;
    }

    public function playRounds(int $rounds): self {
        $this->round = 1;

        for ($i = 0; $i < $rounds; $i++) {
            $this->playRound();
        }

        return $this;
    }

    public function withWorryReduction(int $worryReduction): self {
        $this->worryReduction = $worryReduction;

        return $this;
    }

    public function reportProgress(bool $reportProgress = true): self {
        $this->reportProgress = $reportProgress;

        return $this;
    }

    public function report(string $part): void {
        echo $part . PHP_EOL;

        foreach ($this->monkeys as $key => $monkey) {
            echo sprintf('Monkey %s inspected items %s times.', $key, $monkey['inspections']) . PHP_EOL;
        }

        usort($this->monkeys, function ($monkey1, $monkey2) {
            return $monkey2['inspections'] <=> $monkey1['inspections'];
        });

        echo sprintf(
            'Top two monkeys: %s and %s, have a total monkey business of: %s',
            $this->monkeys[0]['inspections'],
            $this->monkeys[1]['inspections'],
            $this->monkeys[0]['inspections'] * $this->monkeys[1]['inspections'],
        ) . PHP_EOL;
    }

    private function log(string $output): void
    {
        if ($this->reportProgress) {
            echo $output;
        }
    }

    private function playRound(): void
    {
        foreach ($this->monkeys as $key => &$monkey) {
            $this->log('Monkey ' . $key . ':' . PHP_EOL);

            if (! count($monkey['items'])) {
                $this->log('  Monkey has no items.' . PHP_EOL);
                continue;
            }

            foreach ($monkey['items'] as $worryLevel) {
                $monkey['inspections']++;
                $this->log(sprintf(
                    '  Monkey inspects an item with a worry level of %s.',
                    $worryLevel
                ) . PHP_EOL);

                $worryLevel = $this->translateOperation($worryLevel, $monkey['operation']);
                $worryLevel = $this->controlWorryLevel($worryLevel);

                $this->testAndThrowItem($worryLevel, $monkey);
            }

            $monkey['items'] = [];
        }

        $this->reportRound();
        $this->round++;
    }

    private function controlWorryLevel(int $worryLevel): int{
        if (! $this->worryReduction) {
            $worryLevel = floor($worryLevel / 3);

            $this->log(sprintf(
                '    Monkey gets bored with item. Worry level is divided by 3 to %s.',
                $worryLevel,
            ) . PHP_EOL);
        } else {
            $worryLevel = $worryLevel % $this->worryReduction;
            $this->log(sprintf(
                '    Monkey gets bored with item. Worry level is set to modulo of %s to %s.',
                $this->worryReduction,
                $worryLevel,
            ) . PHP_EOL);
        }

        return $worryLevel;
    }

    private function reportRound(): void
    {
        $this->log(sprintf(
            'After round %s, the monkeys are holding items with these worry levels:',
            $this->round,
        ) . PHP_EOL);

        foreach ($this->monkeys as $key => $monkey) {
            $this->log(sprintf('Monkey %s: %s', $key, implode(', ', $monkey['items'])) . PHP_EOL);
        }
    }

    private function testAndThrowItem(int $worryLevel, array $monkey): void
    {
        if ($worryLevel % $monkey['divide_by'] === 0) {
            $this->log(sprintf(
                '    Current worry level is divisible by %s.',
                $monkey['divide_by'],
            ) . PHP_EOL);

            $targetMonkey = $monkey['if_true'];
        } else {
            $this->log(sprintf(
                '    Current worry level is not divisible by %s.',
                $monkey['divide_by'],
            ) . PHP_EOL);

            $targetMonkey = $monkey['if_false'];
        }

        $this->log(sprintf(
            '    Item with worry level %s is thrown to monkey %s.',
            $worryLevel,
            $targetMonkey,
        ) . PHP_EOL);

        $this->monkeys[$targetMonkey]['items'][] = $worryLevel;
    }

    private function translateOperation(int $worryLevel, string $operation): int
    {
        $operations = explode(' ', $operation);

        if ($operations[1] === 'old') {
            $operations[1] = $worryLevel;
            $operations[2] = 'itself';
        }

        switch ($operations[0]) {
            case '+':
                $worryLevel += $operations[1];
                $this->log(sprintf(
                    '    Worry level increases by %s to %s.',
                    $operations[2] ?? $operations[1],
                    $worryLevel
                ) . PHP_EOL);
                break;
            case '*':
                $worryLevel *= $operations[1];
                $this->log(sprintf(
                    '    Worry level is multiplied by %s to %s.',
                    $operations[2] ?? $operations[1],
                    $worryLevel
                ) . PHP_EOL);
                break;
            default:
                break;
        }

        return $worryLevel;
    }
}

$keepAway = new KeepAway();
$monkeys = $keepAway->parseInput();

$keepAway
    ->withMonkeys($monkeys)
    ->playRounds(20)
    ->report('Part1:');

$keepAway
    ->withMonkeys($monkeys)
    ->withWorryReduction(array_product(array_column($monkeys, 'divide_by')))
    ->playRounds(10000)
    ->report('Part2:');