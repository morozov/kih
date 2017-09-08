<?php

declare(strict_types=1);

namespace KiH;

use KiH\Entity\Feed;
use KiH\Entity\Media;

interface Client
{
    /**
     * @return Feed
     * @throws Exception
     */
    public function getFeed() : Feed;

    /**
     * @param string $id
     * @return Media
     * @throws Exception
     */
    public function getMedia(string $id) : Media;
}
