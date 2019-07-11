<?php

namespace Gendiff\Tests\Utils;

use PHPUnit\Framework\TestCase;
use function Gendiff\Utils\FileUtils\isFileExist;
use function Gendiff\Utils\FileUtils\getJsonFileContent;

class FileUtilsTest extends TestCase
{
    public function testIsFileExist()
    {
        $this->assertFalse(isFileExist('fds', './tests/Utils/'));
        $this->assertTrue(isFileExist('testfile.json', './tests/Utils/'));
    }

    public function testGetJsonFileContent()
    {
        $this->assertEquals([], getJsonFileContent('fsdfs', './tests/Utils/'));
        $firstFileContent = getJsonFileContent('testfile.json', './tests/Utils/');
        $secondFileContent = getJsonFileContent('testfile.json', './tests/Utils/');
        $this->assertEquals($firstFileContent, $secondFileContent);
        $this->assertNotEquals($firstFileContent['verbose'] = false, $secondFileContent);
    }
}
