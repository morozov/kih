<?php

declare(strict_types=1);

namespace KiH;

use KiH\Entity\Feed;

interface Client
{
    /**
     * @return Feed
     * @throws Exception
     */
    public function getFeed() : Feed;
}
