<?php

declare(strict_types=1);

$autoload = __DIR__ . '/../vendor/autoload.php';

if (!file_exists($autoload)) {
    echo 'You must set up the project dependencies, run the following commands:'
        . PHP_EOL . 'curl -sS https://getcomposer.org/installer | php'
        . PHP_EOL . 'php composer.phar install'
        . PHP_EOL;
    exit(1);
}

require $autoload;

$controller = new \KiH\Controller(new \KiH\Client(
    new \GuzzleHttp\Client(),
    'https://onedrive.live.com/redir.aspx?cid=0b6c46ff0a72f8db&resid=B6C46FF0A72F8DB!196510&parId=B6C46FF0A72F8DB!116&authkey=!AB17lqCz5De3HEE'
), new \KiH\Parser(), new \KiH\Generator(
    'https://s11v.tk/kih'
));

try {
    if ($_SERVER['REQUEST_URI'] === '/rss.xml') {
        $controller->rss();
    } elseif (preg_match('/\/media\/([^\/]+)\.mp3$/', $_SERVER['REQUEST_URI'], $matches)) {
        $controller->download($matches[1]);
    } else {
        return false;
    }
} catch (\KiH\Exception $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    return false;
}
