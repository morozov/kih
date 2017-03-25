<?php

declare(strict_types=1);

namespace KiH\Tests;

use KiH\Exception;
use KiH\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @var Parser */
    private $parser;

    public function setUp()
    {
        parent::setUp();

        $this->parser = new Parser();
    }

    /**
     * @param string $fixture
     *
     * @test
     * @dataProvider folderProvider
     */
    public function parseFolder(string $fixture)
    {
        $json = file_get_contents($fixture);
        $files = $this->parser->parseFolder($json);

        $this->assertCount(1, $files);
        $file = array_shift($files);
        $this->assertArrayHasKey('createdDateTime', $file);
        $this->assertInstanceOf(\DateTime::class, $file['createdDateTime']);
    }

    public static function folderProvider()
    {
        return [
            [
                __DIR__ . '/fixtures/parser/success/folder.json',
            ],
        ];
    }
}
