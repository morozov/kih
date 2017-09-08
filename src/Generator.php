<?php

declare(strict_types=1);

namespace KiH;

use DOMDocument;
use KiH\Entity\Feed;

interface Generator
{
    public function generate(Feed $feed) : DOMDocument;
}
