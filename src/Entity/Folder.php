<?php

declare(strict_types=1);

namespace KiH\Entity;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use KiH\Exception;

final class Folder implements IteratorAggregate
{
    /**
     * @var File[]
     */
    private $files;

    public function __construct(array $files)
    {
        $this->files = array_map(function (File $file) : File {
            return $file;
        }, $files);
    }

    public static function fromApiResponse(array $data): self
    {
        if (!isset($data['value'])) {
            throw new Exception('The folder representation does not contain the "value" element');
        }

        return new self(array_map(function (array $file) : File {
            return File::fromApiResponse($file);
        }, $data['value']));
    }

    public function getIterator() : Iterator
    {
        return new ArrayIterator($this->files);
    }
}
