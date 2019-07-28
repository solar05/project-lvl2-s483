<?php

namespace Gendiff\Tests\Utils;

use PHPUnit\Framework\TestCase;
use function Gendiff\Utils\FileUtils\isFilesExists;
use function Gendiff\Utils\FileUtils\isFilesExtensionSame;

class FileUtilsTest extends TestCase
{
    public function testIsFileExist()
    {
        $path = './fixtures/';
        $this->assertFalse(isFilesExists("{$path}fds", "{$path}b.json"));
        $this->assertTrue(isFilesExists("{$path}testfile.json", "{$path}a.json"));
    }

    public function testIsFilesExtensionSame()
    {
        $this->assertTrue(isFilesExtensionSame('a.json', 'b.json'));
        $this->assertFalse(isFilesExtensionSame('a.json', 'b.yaml'));
    }
}
