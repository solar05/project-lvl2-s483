<?php

namespace Gendiff\Tests;

use function Gendiff\Main\runGendiff;
use PHPUnit\Framework\TestCase;

class ReportGeneratorTest extends TestCase
{
    private $expectedPlain;
    private $expectedPretty;
    private $expectedJson;
    private $expectedPlainNested;
    private $expectedPrettyNested;
    private $expectedJsonNested;
    private $fixturesPath;

    public function setUp(): void
    {
        $this->fixturesPath = './tests/fixtures/';
        $this->expectedPlain = file_get_contents("{$this->fixturesPath}expected-plain.txt");
        $this->expectedPretty = file_get_contents("{$this->fixturesPath}expected-pretty.txt");
        $this->expectedJson = file_get_contents("{$this->fixturesPath}expected-json.txt");
        $this->expectedPlainNested = file_get_contents("{$this->fixturesPath}expected-plain-nested.txt");
        $this->expectedPrettyNested = file_get_contents("{$this->fixturesPath}expected-pretty-nested.txt");
        $this->expectedJsonNested = file_get_contents("{$this->fixturesPath}expected-json-nested.txt");
    }

    public function testPlainJsonDiff()
    {
        $this->assertEquals(
            $this->expectedPlain,
            runGendiff('plain', "{$this->fixturesPath}plain-a.json", "{$this->fixturesPath}plain-b.json")
        );
    }

    public function testPlainNestedJsonDiff()
    {
        $this->assertEquals(
            $this->expectedPlainNested,
            runGendiff('plain', "{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-b.json")
        );
    }


    public function testPlainYamlDiff()
    {
        $this->assertEquals(
            $this->expectedPlain,
            runGendiff('plain', "{$this->fixturesPath}plain-a.yaml", "{$this->fixturesPath}plain-b.yaml")
        );
    }

    public function testPlainNestedYamlDiff()
    {
        $this->assertEquals(
            $this->expectedPlainNested,
            runGendiff('plain', "{$this->fixturesPath}nested-a.yaml", "{$this->fixturesPath}nested-b.yaml")
        );
    }

    public function testPrettyJsonDiff()
    {
        $this->assertEquals(
            $this->expectedPretty,
            runGendiff('pretty', "{$this->fixturesPath}plain-a.json", "{$this->fixturesPath}plain-b.json")
        );
    }

    public function testPrettyNestedJsonDiff()
    {
        $this->assertEquals(
            $this->expectedPrettyNested,
            runGendiff('pretty', "{$this->fixturesPath}nested-a.json", "{$this->fixturesPath}nested-b.json")
        );
    }


    public function testPrettyYamlDiff()
    {
        $this->assertEquals(
            $this->expectedPretty,
            runGendiff('pretty', "{$this->fixturesPath}plain-a.yaml", "{$this->fixturesPath}plain-b.yaml")
        );
    }

    public function testPrettyNestedYamlDiff()
    {
        $this->assertEquals(
            $this->expectedPrettyNested,
            runGendiff('pretty', "{$this->fixturesPath}nested-a.yaml", "{$this->fixturesPath}nested-b.yaml")
        );
    }

    public function testPlainJsonDiffReport()
    {
        $this->assertEquals(
            $this->expectedJson,
            runGendiff('json', "{$this->fixturesPath}plain-a.yaml", "{$this->fixturesPath}plain-b.yaml")
        );
    }

    public function testNestedJsonDiffReport()
    {
        $this->assertEquals(
            $this->expectedJsonNested,
            runGendiff('json', "{$this->fixturesPath}nested-a.yaml", "{$this->fixturesPath}nested-b.yaml")
        );
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
