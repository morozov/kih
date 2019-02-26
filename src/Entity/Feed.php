<?php declare(strict_types=1);

namespace KiH\Entity;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use function array_map;

final class Feed implements IteratorAggregate
{
    /**
     * @var Item[]
     */
    private $files;

    /**
     * @param Item[] $files
     */
    public function __construct(array $files)
    {
        $this->files = array_map(static function (Item $file) : Item {
            return $file;
        }, $files);
    }

    /**
     * @return Iterator|Item[]
     */
    public function getIterator() : Iterator
    {
        return new ArrayIterator($this->files);
    }
}
