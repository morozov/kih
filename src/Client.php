<?php

declare(strict_types=1);

namespace KiH;

use KiH\Entity\Feed;
use KiH\Entity\Media;

interface Client
{
    /**
     * @throws Exception
     */
    public function getFeed(): Feed;

    /**
     * @throws Exception
     */
    public function getMedia(string $id): Media;
}
