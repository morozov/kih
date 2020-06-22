<?php

declare(strict_types=1);

namespace KiH;

use DOMDocument;
use KiH\Entity\Feed;
use Psr\Http\Message\UriInterface;

interface Generator
{
    public function generate(Feed $feed, UriInterface $requestUri): DOMDocument;
}
