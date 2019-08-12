<?php

namespace Differ\ReportGenerator\PrettyReporter;

use function Funct\Collection\flattenAll;
use function Differ\ReportGenerator\ReporterUtils\boolToString;

function prettyReport(array $ast, $level = 0)
{
    $prettyTypeMap = [
        'nested' => function ($node, $level) {
            return getIndents($level) . '    ' . $node['node'] . ': ' . prettyReport($node['children'], $level + 1);
        },
        'added' => function ($node, $level) {
            return getPreparedPrettyLine($level, '+', $node['node'], $node['to']);
        },
        'removed' => function ($node, $level) {
            return getPreparedPrettyLine($level, '-', $node['node'], $node['from']);
        },
        'unchanged' => function ($node, $level) {
            return getPreparedPrettyLine($level, ' ', $node['node'], $node['from']);
        },
        'changed' => function ($node, $level) {
            return [getPreparedPrettyLine($level, '-', $node['node'], $node['from']),
                getPreparedPrettyLine($level, '+', $node['node'], $node['to'])];
        }
    ];

    $lines = array_map(function ($currentNode) use ($level, $prettyTypeMap) {
        $preparedNode = boolToString($currentNode);
        $str = $prettyTypeMap[$preparedNode['type']]($preparedNode, $level);
        return $str;
    }, $ast);
    $text = implode("\n", flattenAll($lines));
    $finalIndents = getIndents($level);
    return "{\n{$text}\n{$finalIndents}}";
}

function processToString($data, $level = 0)
{
    if (empty($data) || !is_array($data)) {
        return $data;
    }
    $keys = array_keys($data);
    $lines = array_reduce($keys, function ($acc, $key) use ($data, $level) {
        $acc[] = getIndents($level + 1) . $key . ': ' . $data[$key];
        return $acc;
    }, []);
    $text = implode("\n", $lines) . "\n";
    $indents = getIndents($level);
    return "{\n{$text}{$indents}}";
}

function getIndents($level)
{
    return str_repeat(' ', $level * 4);
}

function getPreparedPrettyLine($level, $sign, $key, $value)
{
    return getIndents($level) . "  {$sign} " . $key . ": " . processToString($value, $level + 1);
}
