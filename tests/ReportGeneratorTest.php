<?php

namespace Differ\Tests;

use function Differ\GenDiff\genDiff;
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
        $this->assertEquals($expected, genDiff($firstFilePath, $secondFilePath, $format));
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
        $this->expectExceptionMessage("Error: one of files does not exists.");
        genDiff('non-existent.json', "{$this->fixturesPath}nested-a.json", 'json');
    }

    public function testFilesExtensionNotSameException()
    {
        $this->expectExceptionMessage("Error: files extensions are not the same.");
        genDiff("{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-a.yaml", 'json');
    }

    public function testUnsupportedExtensionException()
    {
        $this->expectExceptionMessage("Error: txt extension is unsupported.");
        genDiff("{$this->fixturesPath}expected-json.txt", "{$this->fixturesPath}expected-json.txt", 'json');
    }

    public function testUnsupportedFormatException()
    {
        $this->expectExceptionMessage("good format is unsupported.");
        genDiff("{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-b.json", 'good');
    }
}
