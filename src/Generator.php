<?php

declare(strict_types=1);

namespace KiH;

use DOMDocument;
use KiH\Entity\Folder;

interface Generator
{
    public function generate(Folder $folder) : DOMDocument;
}
