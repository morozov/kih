<?php declare(strict_types=1);

return [
    'displayErrorDetails' => (bool) getenv('DEBUG'),
    'vk' => [
        'group' => 'kremhrust',
        'access_token' => getenv('VK_ACCESS_TOKEN') ?: '',
    ],
    'feed' => [
        'title' => 'Кремов и Хрусталёв',
        'logo' => 'http://www.radiorecord.ru/i/img/rr-logo-podcast.png',
    ],
];
