<?php

declare(strict_types=1);

namespace KiH;

use KiH\Entity\Folder;
use KiH\Entity\Media;

interface Client
{
    public function getFolder() : Folder;

    public function getMedia(string $id) : Media;
}
