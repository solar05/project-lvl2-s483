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
        try {
             genDiff('non-existent.json', "{$this->fixturesPath}nested-a.json", 'json');
        } catch (\Exception $error) {
            $this->assertEquals('Error: one of files does not exists.', $error->getMessage());
            return;
        }
        $this->fail('File nonexistent exception wasn`t thrown');
    }

    public function testFilesExtensionNotSameException()
    {
        try {
            genDiff("{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-a.yaml", 'json');
        } catch (\Exception $error) {
            $this->assertEquals('Error: files extensions are not the same.', $error->getMessage());
            return;
        }
        $this->fail('Files extension dissimilarity exception wasn`t thrown');
    }

    public function testUnsupportedExtensionException()
    {
        try {
            genDiff("{$this->fixturesPath}expected-json.txt", "{$this->fixturesPath}expected-json.txt", 'json');
        } catch (\Exception $error) {
            $this->assertEquals("Error: txt extension is unsupported.", $error->getMessage());
            return;
        }
        $this->fail('Files extension not supported exception wasn`t thrown');
    }

    public function testUnsupportedFormatException()
    {
        try {
            genDiff("{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-b.json", 'good');
        } catch (\Exception $error) {
            $this->assertEquals("good format is unsupported.", $error->getMessage());
            return;
        }
        $this->fail('Unsupported format exception wasn`t thrown');
    }
}
