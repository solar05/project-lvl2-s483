<?php

namespace Gendiff\Tests;

use PHPUnit\Framework\TestCase;
use Gendiff\ReportGenerator;
use Gendiff\FileParser;
use Gendiff\AST;

class ReportGeneratorTest extends TestCase
{
    const EXPECTED_PLAIN = "Property 'timeout' was changed. From '20' to '50'
Property 'proxy' was removed
Property 'verbose' was added with value: 'true'";

    const EXPECTED_PLAIN_NESTED = "Property 'common.setting2' was removed
Property 'common.setting6' was removed
Property 'common.setting4' was added with value: 'blah blah'
Property 'common.setting5' was added with value: 'complex value'
Property 'group1.baz' was changed. From 'bas' to 'bars'
Property 'group2' was removed
Property 'group3' was added with value: 'complex value'";

    const EXPECTED_PRETTY_NESTED = "{
    common: {
        setting1: Value 1
      - setting2: 200
        setting3: true
      - setting6: {
            key: value
        }
      + setting4: blah blah
      + setting5: {
            key5: value5
        }
    }
    group1: {
      - baz: bas
      + baz: bars
        foo: bar
    }
  - group2: {
        abc: 12345
    }
  + group3: {
        fee: 100500
    }
}";

    const EXPECTED_PRETTY = "{
    host: hexlet.io
  - timeout: 20
  + timeout: 50
  - proxy: 123.234.53.22
  + verbose: true
}";

    const EXPECTED_JSON_NESTED = '[{"type":"nested","node":"common","from":null,"to":null,"children":[{"type":"unchanged","node":"setting1","from":"Value 1","to":"Value 1","children":null},{"type":"removed","node":"setting2","from":"200","to":"","children":null},{"type":"unchanged","node":"setting3","from":"true","to":"true","children":null},{"type":"removed","node":"setting6","from":{"key":"value"},"to":"","children":null},{"type":"added","node":"setting4","from":"","to":"blah blah","children":null},{"type":"added","node":"setting5","from":"","to":{"key5":"value5"},"children":null}]},{"type":"nested","node":"group1","from":null,"to":null,"children":[{"type":"changed","node":"baz","from":"bas","to":"bars","children":null},{"type":"unchanged","node":"foo","from":"bar","to":"bar","children":null}]},{"type":"removed","node":"group2","from":{"abc":"12345"},"to":"","children":null},{"type":"added","node":"group3","from":"","to":{"fee":"100500"},"children":null}]';

    const EXPECTED_JSON_PLAIN = '[{"type":"unchanged","node":"host","from":"hexlet.io","to":"hexlet.io","children":null},{"type":"changed","node":"timeout","from":20,"to":50,"children":null},{"type":"removed","node":"proxy","from":"123.234.53.22","to":"","children":null},{"type":"added","node":"verbose","from":"","to":"true","children":null}]';

    public function testPlainReport()
    {
        $parsedJson = FileParser\parseFiles('./tests/fixtures/nested-a.json', './tests/fixtures/nested-b.json', 'json');
        $ast = AST\makeAst(...$parsedJson);
        $this->assertEquals(self::EXPECTED_PLAIN_NESTED, ReportGenerator\plainReport($ast));
        $this->assertNotEquals("Property 'common.setting2' was removed", ReportGenerator\plainReport($ast));
        $parsedYaml = FileParser\parseFiles('./tests/fixtures/nested-a.yaml', './tests/fixtures/nested-b.yaml', 'yaml');
        $yamlAst = AST\makeAst(...$parsedYaml);
        $result1 = ReportGenerator\plainReport($yamlAst);
        $this->assertEquals(self::EXPECTED_PLAIN_NESTED, $result1);
        $this->assertNotEquals("Property 'common.setting2' was removed", $result1);
        $parsedJsonPlain = FileParser\parseFiles('./tests/fixtures/plain-a.json', './tests/fixtures/plain-b.json', 'json');
        $ast1 = AST\makeAst(...$parsedJsonPlain);
        $result2 = ReportGenerator\plainReport($ast1);
        $this->assertEquals(self::EXPECTED_PLAIN, $result2);
        $this->assertNotEquals(self::EXPECTED_PLAIN_NESTED, $result2);
        $parsedYamlPlain = FileParser\parseFiles('./tests/fixtures/plain-a.yaml', './tests/fixtures/plain-b.yaml', 'yaml');
        $ast2 = AST\makeAst(...$parsedYamlPlain);
        $result3 = ReportGenerator\plainReport($ast2);
        $this->assertEquals(self::EXPECTED_PLAIN, $result3);
        $this->assertNotEquals(self::EXPECTED_PLAIN_NESTED, $result3);
    }

    public function testPrettyReport()
    {
        $parsedJson = FileParser\parseFiles('./tests/fixtures/nested-a.json', './tests/fixtures/nested-b.json', 'json');
        $ast = AST\makeAst(...$parsedJson);
        $result = ReportGenerator\prettyReport($ast);
        $this->assertEquals(self::EXPECTED_PRETTY_NESTED, $result);
        $this->assertNotEquals("{common: {setting1: Value 1", $result);
        $parsedYaml = FileParser\parseFiles('./tests/fixtures/nested-a.yaml', './tests/fixtures/nested-b.yaml', 'yaml');
        $yamlAst = AST\makeAst(...$parsedYaml);
        $result1 = ReportGenerator\prettyReport($yamlAst);
        $this->assertEquals(self::EXPECTED_PRETTY_NESTED, $result1);
        $this->assertNotEquals("{common: {setting1: Value 1", $result1);
        $parsedYamlPlain = FileParser\parseFiles('./tests/fixtures/plain-a.yaml', './tests/fixtures/plain-b.yaml', 'yaml');
        $ast1 = AST\makeAst(...$parsedYamlPlain);
        $result2 = ReportGenerator\prettyReport($ast1);
        $this->assertEquals(self::EXPECTED_PRETTY, $result2);
        $this->assertNotEquals(self::EXPECTED_PRETTY_NESTED, $result2);
        $parsedJsonPlain = FileParser\parseFiles('./tests/fixtures/plain-a.json', './tests/fixtures/plain-b.json', 'json');
        $ast2 = AST\makeAst(...$parsedJsonPlain);
        $result3 = ReportGenerator\prettyReport($ast2);
        $this->assertEquals(self::EXPECTED_PRETTY, $result3);
        $this->assertNotEquals(self::EXPECTED_PRETTY_NESTED, $result3);
    }

    public function testJson()
    {
        $parsedJson = FileParser\parseFiles('./tests/fixtures/plain-a.json', './tests/fixtures/plain-b.json', 'json');
        $ast = AST\makeAst(...$parsedJson);
        $result = ReportGenerator\jsonReport($ast);
        $this->assertEquals(self::EXPECTED_JSON_PLAIN, $result);
        $this->assertNotEquals('["blah":"blah","test:"true"]', $result);
        $parsedYaml = FileParser\parseFiles('./tests/fixtures/plain-a.yaml', './tests/fixtures/plain-b.yaml', 'yaml');
        $yamlAst = AST\makeAst(...$parsedYaml);
        $result1 = ReportGenerator\jsonReport($yamlAst);
        $this->assertEquals(self::EXPECTED_JSON_PLAIN, $result1);
        $this->assertNotEquals('["blah":"blah","test:"true"]', $result1);
        $parsedJsonNested = FileParser\parseFiles('./tests/fixtures/nested-a.json', './tests/fixtures/nested-b.json', 'json');
        $ast2 = AST\makeAst(...$parsedJsonNested);
        $result2 = ReportGenerator\jsonReport($ast2);
        $this->assertEquals(self::EXPECTED_JSON_NESTED, $result2);
        $this->assertNotEquals(self::EXPECTED_JSON_PLAIN, $result2);
        $parsedYamlNested = FileParser\parseFiles('./tests/fixtures/nested-a.yaml', './tests/fixtures/nested-b.yaml', 'yaml');
        $ast3 = AST\makeAst(...$parsedYamlNested);
        $result3 = ReportGenerator\jsonReport($ast3);
        $this->assertEquals(self::EXPECTED_JSON_NESTED, $result3);
        $this->assertNotEquals(self::EXPECTED_JSON_PLAIN, $result3);
    }
}
