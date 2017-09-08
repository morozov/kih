<?php

return [
    'settings' => [
        'displayErrorDetails' => (bool) getenv('DEBUG'),
        'share' => [
            'cid' => '0b6c46ff0a72f8db',
            'resid' => 'B6C46FF0A72F8DB!196510',
            'parId' => 'B6C46FF0A72F8DB!116',
            'authkey' => '!AB17lqCz5De3HEE',
        ],
        'yandex_disk' => [
            'public_key' => 'https://yadi.sk/d/yNLm0qGR3LfRjD',
        ],
        'vk' => [
            'group' => 'kremhrust',
            'access_token' => getenv('VK_ACCESS_TOKEN'),
        ],
        'feed' => [
            'title' => 'Кремов и Хрусталёв',
            'logo' => 'http://www.radiorecord.ru/i/img/rr-logo-podcast.png',
        ],
        'baseUri' => getenv('BASE_URI') ?: null,
    ],
];
