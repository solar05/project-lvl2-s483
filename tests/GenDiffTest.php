<?php
/**
 * Created by PhpStorm.
 * User: artems
 * Date: 12/07/2019
 * Time: 02:32
 */

namespace Gendiff\Tests;

use function Gendiff\Utils\FileUtils\getJsonFileContent;
use function Gendiff\Main\generateDiff;
use PHPUnit\Framework\TestCase;

class GenDiffTest extends TestCase
{
    public function testGenerateDiff()
    {
        $testDir = './bin/';
        $firstFile = getJsonFileContent('a.json', $testDir);
        $secondFile = getJsonFileContent('b.json', $testDir);
        $report = generateDiff($firstFile, $secondFile);
        $this->assertEquals("{
    host: hexlet.io
    + timeout: 20
    - timeout: 50
    - proxy: 123.234.53.22
    + verbose: true
}", $report);
        $this->assertEquals("{
    host: hexlet.io
    timeout: 50
    proxy: 123.234.53.22
}", generateDiff($firstFile, $firstFile));
        $this->assertEquals("{
    - host: hexlet.io
    - timeout: 50
    - proxy: 123.234.53.22
}", generateDiff($firstFile, []));

        $this->assertEquals("{
    + host: hexlet.io
    + timeout: 50
    + proxy: 123.234.53.22
}", generateDiff([], $firstFile));
    }
}
