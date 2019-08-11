<?php

namespace Gendiff\Tests;

use function Gendiff\Main\runGendiff;
use PHPUnit\Framework\TestCase;

class ReportGeneratorTest extends TestCase
{
    protected $fixturesPath = './tests/fixtures/';

    /**
     * @dataProvider additionProvider
     */
    public function testRunGendiff($fileWithExpectedData, $firstFileName, $secondFileName, $format = 'pretty')
    {
        $expected = file_get_contents($this->fixturesPath . $fileWithExpectedData);
        $firstFilePath = $this->fixturesPath . $firstFileName;
        $secondFilePath = $this->fixturesPath . $secondFileName;
        $this->assertEquals($expected, runGendiff($format, $firstFilePath, $secondFilePath));
    }

    public function additionProvider()
    {
        return [
            ["expected-pretty.txt", "plain-a.json", "plain-b.json"],
            ["expected-pretty.txt", "plain-a.yaml", "plain-b.yaml"],
            ["expected-pretty-nested.txt", "nested-a.json", "nested-b.json"],
            ["expected-pretty-nested.txt", "nested-a.yaml", "nested-b.yaml"],
            ["expected-plain.txt", "plain-a.json", "plain-b.json", "plain"],
            ["expected-plain.txt", "plain-a.yaml", "plain-b.yaml", "plain"],
            ["expected-plain-nested.txt", "nested-a.json", "nested-b.json", "plain"],
            ["expected-plain-nested.txt", "nested-a.yaml", "nested-b.yaml", "plain"],
            ["expected-json.txt", "plain-a.json", "plain-b.json", "json"],
            ["expected-json.txt", "plain-a.yaml", "plain-b.yaml", "json"],
            ["expected-json-nested.txt", "nested-a.json", "nested-b.json", "json"],
            ["expected-json-nested.txt", "nested-a.yaml", "nested-b.yaml", "json"]
        ];
    }

    public function testFilesExistsException()
    {
        $error = runGendiff('json', 'non-existent.json', "{$this->fixturesPath}nested-a.json");
        $this->assertEquals('Error: one of files does not exists.', $error);
    }

    public function testFilesExtensionNotSameException()
    {
        $error = runGendiff('json', "{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-a.yaml");
        $this->assertEquals('Error: files extensions are not the same.', $error);
    }

    public function testUnsupportedExtensionException()
    {
        $error = runGendiff('json', "{$this->fixturesPath}expected-json.txt", "{$this->fixturesPath}expected-json.txt");
        $this->assertEquals("Error: txt extension is unsupported.", $error);
    }

    public function testUnsupportedFormatException()
    {
        $error = runGendiff('good', "{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-b.json");
        $this->assertEquals("good format is unsupported.", $error);
    }
}
