<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use function Gendiff\ReportGenerator\prettyReport;
use function Gendiff\ReportGenerator\plainReport;
use function Gendiff\ReportGenerator\jsonReport;
use function Gendiff\FileParser\parseFiles;
use function Gendiff\AST\makeAst;

class ReportGeneratorTest extends TestCase
{
    private $expectedPlain;
    private $expectedPretty;
    private $expectedJson;
    private $expectedPlainNested;
    private $expectedPrettyNested;
    private $expectedJsonNested;

    public function setUp() : void
    {
        $this->expectedPlain = file_get_contents('./tests/fixtures/expected-plain.txt');
        $this->expectedPretty = file_get_contents('./tests/fixtures/expected-pretty.txt');
        $this->expectedJson = file_get_contents('./tests/fixtures/expected-json.txt');
        $this->expectedPlainNested = file_get_contents('./tests/fixtures/expected-plain-nested.txt');
        $this->expectedPrettyNested = file_get_contents('./tests/fixtures/expected-pretty-nested.txt');
        $this->expectedJsonNested = file_get_contents('./tests/fixtures/expected-json-nested.txt');
    }

    public function setUpAstForReport($firstPathToFile, $secondPathToFile, $filesExtension)
    {
        $parsedFile = parseFiles($firstPathToFile, $secondPathToFile, $filesExtension);
        $ast = makeAst(...$parsedFile);
        return $ast;
    }

    public function testPlainReport()
    {
        $report = plainReport($this->setUpAstForReport('./tests/fixtures/nested-a.json', './tests/fixtures/nested-b.json', 'json'));
        $this->assertEquals($this->expectedPlainNested, $report);
        $this->assertNotEquals("Property 'common.setting2' was removed", $report);
        $report1 = plainReport($this->setUpAstForReport('./tests/fixtures/nested-a.yaml', './tests/fixtures/nested-b.yaml', 'yaml'));
        $this->assertEquals($this->expectedPlainNested, $report1);
        $this->assertNotEquals("Property 'common.setting2' was removed", $report1);
        $report2 = plainReport($this->setUpAstForReport('./tests/fixtures/plain-a.json', './tests/fixtures/plain-b.json', 'json'));
        $this->assertEquals($this->expectedPlain, $report2);
        $this->assertNotEquals($this->expectedPlainNested, $report2);
        $report3 = plainReport($this->setUpAstForReport('./tests/fixtures/plain-a.yaml', './tests/fixtures/plain-b.yaml', 'yaml'));
        $this->assertEquals($this->expectedPlain, $report3);
        $this->assertNotEquals($this->expectedPlainNested, $report3);
    }

    public function testPrettyReport()
    {
        $report = prettyReport($this->setUpAstForReport('./tests/fixtures/nested-a.json', './tests/fixtures/nested-b.json', 'json'));
        $this->assertEquals($this->expectedPrettyNested, $report);
        $this->assertNotEquals("{common: {setting1: Value 1", $report);
        $report1 = prettyReport($this->setUpAstForReport('./tests/fixtures/nested-a.yaml', './tests/fixtures/nested-b.yaml', 'yaml'));
        $this->assertEquals($this->expectedPrettyNested, $report1);
        $this->assertNotEquals("{common: {setting1: Value 1", $report1);
        $report2 = prettyReport($this->setUpAstForReport('./tests/fixtures/plain-a.yaml', './tests/fixtures/plain-b.yaml', 'yaml'));
        $this->assertEquals($this->expectedPretty, $report2);
        $this->assertNotEquals($this->expectedPrettyNested, $report2);
        $report3 = prettyReport($this->setUpAstForReport('./tests/fixtures/plain-a.json', './tests/fixtures/plain-b.json', 'json'));
        $this->assertEquals($this->expectedPretty, $report3);
        $this->assertNotEquals($this->expectedPrettyNested, $report3);
    }

    public function testJson()
    {
        $report = jsonReport($this->setUpAstForReport('./tests/fixtures/plain-a.json', './tests/fixtures/plain-b.json', 'json'));
        $this->assertEquals($this->expectedJson, $report);
        $this->assertNotEquals('["blah":"blah","test:"true"]', $report);
        $report1 = jsonReport($this->setUpAstForReport('./tests/fixtures/plain-a.yaml', './tests/fixtures/plain-b.yaml', 'yaml'));
        $this->assertEquals($this->expectedJson, $report1);
        $this->assertNotEquals('["blah":"blah","test:"true"]', $report1);
        $report2 = jsonReport($this->setUpAstForReport('./tests/fixtures/nested-a.json', './tests/fixtures/nested-b.json', 'json'));
        $this->assertEquals($this->expectedJsonNested, $report2);
        $this->assertNotEquals($this->expectedJson, $report2);
        $report3 = jsonReport($this->setUpAstForReport('./tests/fixtures/nested-a.yaml', './tests/fixtures/nested-b.yaml', 'yaml'));
        $this->assertEquals($this->expectedJsonNested, $report3);
        $this->assertNotEquals($this->expectedJson, $report3);
    }
}
