<?php

interface Item {
    public function getSize(): int;
}

class File implements Item {
    protected string $name;
    protected int $size;

    public function __construct(string $name, int $size)
    {
        $this->name = $name;
        $this->size = $size;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}

class Dir implements Item {
    protected ?Dir $parent;
    protected string $path;
    /** @var \Item[]  */
    protected array $contents;
    protected int $totalSize;

    public function __construct(string $path, ?Dir $parent = null)
    {
        if (! $parent) {
            $prefix = '';
        } else if ($parent->getPath() === '/') {
            $prefix = '/';
        } else {
            $prefix = $parent->getPath() . '/';
        }

        $this->path = $prefix . $path;
        $this->parent = $parent;
        $this->contents = [];
        $this->totalSize = 0;
    }

    public function addDir(Dir $dir): void
    {
        $this->contents[] = $dir;
    }

    public function addFile(File $file): void
    {
        $this->contents[] = $file;
        $this->totalSize += $file->getSize();
    }

    public function getParent(): ?Dir
    {
        return $this->parent;
    }

    public function getSize(): int
    {
        return $this->totalSize;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function calculateTotalSize(): void
    {
        $size = 0;

        foreach ($this->contents as $item) {
            $size += $item->getSize();
        }

        $this->totalSize = $size;
    }

    public function __toString(): string
    {
        return $this->getSize() . ' ' . $this->getPath() . PHP_EOL;
    }
}
class FileSystem {
    protected Dir $currentWorkingDir;
    protected Dir $root;
    /** @var Dir[] */
    protected array $structure;

    public function __construct(string $path)
    {
        $this->root = new Dir($path);
        $this->currentWorkingDir = $this->root;
        $this->structure[] = $this->currentWorkingDir;
    }

    public function newDir(string $path): void
    {
        $dir = new Dir($path, $this->currentWorkingDir);
        $this->currentWorkingDir->addDir($dir);
        $this->setCurrentWorkingDir($dir);

        $this->structure[] = $dir;
    }

    public function leaveDir(): void
    {
        $dir = $this->currentWorkingDir->getParent();
        $this->setCurrentWorkingDir($dir);
    }

    public function addFileToCurrentWorkingDir(string $fileName, int $size): void
    {
        $file = new File($fileName, $size);
        $this->currentWorkingDir->addFile($file);

        $this->updateParentTotalSize();
    }

    /**
     * @param int $size
     * @return Dir[]
     */
    public function findDirs(int $size): array
    {
        return array_filter($this->structure, function (Dir $dir) use ($size) {
            return $dir->getSize() <= $size;
        });
    }

    public function totalSize(): int
    {
        return $this->root->getSize();
    }

    public function findFolderToFreeUpSpace(int $freeSpace): Dir
    {
        $spaceRequired = ($freeSpace - (70000000 - $this->totalSize()));

        $directories = array_filter($this->structure, function (Dir $dir) use ($spaceRequired) {
            return $dir->getSize() >= $spaceRequired;
        });

        usort($directories, function (Dir $dirA, Dir $dirB) {
            return $dirA->getSize() <=> $dirB->getSize();
        });

        return $directories[0];
    }

    protected function setCurrentWorkingDir(Dir $dir): void
    {
        $this->currentWorkingDir = $dir;
    }

    protected function updateParentTotalSize(): void
    {
        $dir = $this->currentWorkingDir;

        do {
            $dir->calculateTotalSize();
        } while ($dir = $dir->getParent());
    }
}

$rawInput = file_get_contents('input.txt');

$fileSystem = null;

foreach (explode(PHP_EOL, $rawInput) as $line) {
    $command = explode(' ', $line);

    if ($command[0] === '$' && $command[1] === 'cd') {
        if (is_null($fileSystem)) {
            $fileSystem = new FileSystem($command[2]);
        } else if ($command[2] === '..') {
            $fileSystem->leaveDir();
        } else {
            $fileSystem->newDir($command[2]);
        }
    } else if (is_numeric($command[0])) {
        $fileSystem->addFileToCurrentWorkingDir($command[1], $command[0]);
    }
}

$dirs = $fileSystem->findDirs(100000);

$size = 0;

foreach ($dirs as $dir) {
    $size += $dir->getSize();
}

echo 'Part1: Total size of folders 100000 or smaller: ' . $size . PHP_EOL;
echo 'Part2: Folder to remove: ' . $fileSystem->findFolderToFreeUpSpace(30000000);