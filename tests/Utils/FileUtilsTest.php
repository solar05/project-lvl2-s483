<?php

namespace Gendiff\Tests\Utils;

use PHPUnit\Framework\TestCase;
use function Gendiff\Utils\FileUtils\isFilesExists;
use function Gendiff\Utils\FileUtils\isFilesExtensionSame;

class FileUtilsTest extends TestCase
{
    public function testIsFileExist()
    {
        $path = './tests/fixtures/';
        $this->assertFalse(isFilesExists("{$path}fds", "{$path}b.json"));
        $this->assertTrue(isFilesExists("{$path}plain-b.json", "{$path}plain-a.json"));
    }
}
