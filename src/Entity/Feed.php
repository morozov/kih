<?php

declare(strict_types=1);

namespace KiH\Entity;

use ArrayIterator;
use Iterator;
use IteratorAggregate;

final class Feed implements IteratorAggregate
{
    /**
     * @var Item[]
     */
    private $files;

    public function __construct(array $files)
    {
        $this->files = array_map(function (Item $file) : Item {
            return $file;
        }, $files);
    }

    public function getIterator() : Iterator
    {
        return new ArrayIterator($this->files);
    }
}
