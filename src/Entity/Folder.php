<?php

declare(strict_types=1);

namespace KiH\Entity;

use ArrayIterator;
use IteratorAggregate;
use KiH\Exception;

final class Folder implements IteratorAggregate
{
    private $files;

    public function __construct(array $files)
    {
        $this->files = $files;
    }

    public static function fromApiResponse(array $data): self
    {
        if (!isset($data['value'])) {
            throw new Exception('The folder representation does not contain the "value" element');
        }

        return new self(array_map(function (array $file) {
            return File::fromApiResponse($file);
        }, $data['value']));
    }

    public function getIterator()
    {
        return new ArrayIterator($this->files);
    }
}
