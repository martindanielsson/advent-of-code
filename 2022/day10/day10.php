<?php
class CPU {
    protected int $x = 1;
    protected int $cycle = 0;
    protected array $reports = [
        20 => null,
        60 => null,
        100 => null,
        140 => null,
        180 => null,
        220 => null,
    ];
    protected array $commands;
    protected mixed $beforeCycleHook = null;
    protected mixed $afterCycleHook = null;

    public function setInput(array $commands): void
    {
        $this->commands = $commands;
    }

    public function setBeforeCycleHook(callable $hook): void
    {
        $this->beforeCycleHook = $hook;
    }

    public function setAfterCycleHook(callable $hook): void
    {
        $this->afterCycleHook = $hook;
    }

    public function run(): void
    {
        $this->x = 1;
        $this->cycle = 0;

        foreach ($this->commands as $command) {
            if ($command[0] === 'noop') {
                $this->runCycles(1);
            } else {
                $this->runCycles(2);

                $this->x += $command[1];

                if ($this->afterCycleHook) {
                    call_user_func($this->afterCycleHook, $this->x);
                }
            }
        }
    }

    public function report(): void
    {
        foreach ($this->reports as $cycle => $report) {
            echo sprintf('At cycle %s the signal strength was %s', $cycle, $report) . PHP_EOL;
        }

        echo 'Part 1: Total signal strength: ' . array_sum($this->reports) . PHP_EOL;
    }

    protected function runCycles(int $cycles): void {
        for ($i = 0; $i < $cycles; $i++) {
            if ($this->beforeCycleHook) {
                call_user_func($this->beforeCycleHook);
            }
            $this->cycle++;

            $this->checkCycle();
        }
    }

    protected function checkCycle(): void {
        if (array_key_exists($this->cycle, $this->reports)) {
            $this->reports[$this->cycle] = $this->x * $this->cycle;
        }
    }
}

class CrtRenderer
{
    const PIXEL_WIDTH = 40;
    const SPRITE_LENGTH = 3;

    protected int $spritePosition = 0;
    protected int $drawPosition = 0;
    protected string $output = '';
    protected CPU $cpu;

    public function __construct(CPU $cpu)
    {
        $this->cpu = $cpu;
        $this->cpu->setBeforeCycleHook(function () {
            $this->drawPixel();
        });
        $this->cpu->setAfterCycleHook(function (int $position) {
            $this->moveSpritePosition($position);
        });
    }

    public function draw(): void
    {
        echo 'Part2' . PHP_EOL;
        $this->cpu->run();
    }

    public function drawPixel(): void
    {
        if (
            $this->drawPosition >= $this->spritePosition &&
            $this->drawPosition < $this->spritePosition + self::SPRITE_LENGTH
        ) {
            $this->output .= '#';
        } else {
            $this->output .= '.';
        }

        $this->drawPosition++;

        if ($this->drawPosition === self::PIXEL_WIDTH) {
            echo $this->output . PHP_EOL;

            $this->output = '';
            $this->drawPosition = 0;
        }
    }

    public function moveSpritePosition(int $position): void
    {
        $this->spritePosition = $position - 1;
    }
}


$rawInput = file_get_contents('input.txt');
$input = array_map(fn ($line) => explode(' ', $line), explode(PHP_EOL, $rawInput));
$cpu = new CPU();
$cpu->setInput($input);
$cpu->run();
$cpu->report();

$crt = new CrtRenderer($cpu);
$crt->draw();