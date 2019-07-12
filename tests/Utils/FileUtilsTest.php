<?php

namespace Gendiff\Tests\Utils;

use PHPUnit\Framework\TestCase;
use function Gendiff\Utils\FileUtils\isFileExist;
use function Gendiff\Utils\FileUtils\getJsonFileContent;
use function Gendiff\Utils\FileUtils\getYamlFileContent;

class FileUtilsTest extends TestCase
{
    public function testIsFileExist()
    {
        $path = './tests/Utils/';
        $this->assertFalse(isFileExist('fds', $path));
        $this->assertTrue(isFileExist('testfile.json', $path));
    }

    public function testGetJsonFileContent()
    {
        $path = './tests/Utils/';
        $this->assertEquals([], getJsonFileContent('fsdfs', $path));
        $firstFileContent = getJsonFileContent('testfile.json', $path);
        $secondFileContent = getJsonFileContent('testfile.json', $path);
        $this->assertEquals(["timeout" => 20,
            "verbose" => true,
            "host" => "hexlet.io"], $firstFileContent);
        $this->assertEquals($firstFileContent, $secondFileContent);
        $this->assertNotEquals($firstFileContent['verbose'] = false, $secondFileContent);
    }

    public function testGetYamlFileContent()
    {
        $path = './tests/Utils/';
        $this->assertEquals([], getYamlFileContent('fsdfs', $path));
        $firstFileContent = getYamlFileContent('testfile.yaml', $path);
        $secondFileContent = getYamlFileContent('testfile.yaml', $path);
        $this->assertEquals(["timeout" => 20,
            "verbose" => true,
            "host" => "hexlet.io"], $firstFileContent);
        $this->assertEquals($firstFileContent, $secondFileContent);
        $this->assertNotEquals($firstFileContent['verbose'] = false, $secondFileContent);
    }
}
