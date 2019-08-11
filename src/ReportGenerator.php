<?php

namespace Gendiff\ReportGenerator;

use function Funct\Collection\flattenAll;

function generateReport(string $format, array $ast)
{
    $formatMap = [
        'plain' => function ($ast) {
            return plainReport($ast);
        },
        'pretty' => function ($ast) {
            return prettyReport($ast);
        },
        'json' => function ($ast) {
            return jsonReport($ast);
        }

        ];
    if (!array_key_exists($format, $formatMap)) {
            throw new \Exception("{$format} format is unsupported.");
    }
    return $formatMap[$format]($ast);
}

function plainReport(array $ast)
{
    $iterator = function ($ast, $parents) use (&$iterator) {
        return array_reduce($ast, function ($acc, $node) use ($iterator, $parents) {
            $prepNode = boolToString($node);
            $parents[] = $prepNode['node'];
            $pathToNode = implode('.', $parents);
            switch ($prepNode['type']) {
                case 'nested':
                    $acc = array_merge($acc, $iterator($prepNode['children'], $parents));
                    break;
                case 'added':
                    if (is_array($prepNode['to'])) {
                        $acc[] = "Property '{$pathToNode}' was added with value: 'complex value'";
                    } else {
                        $acc[] = "Property '{$pathToNode}' was added with value: '{$prepNode['to']}'";
                    }
                    break;
                case 'changed':
                    $acc[] = "Property '{$pathToNode}' was changed. From '{$prepNode['from']}' to '{$prepNode['to']}'";
                    break;
                case 'removed':
                    $acc[] = "Property '{$pathToNode}' was removed";
                    break;
            }
            return $acc;
        }, []);
    };
    return implode("\n", $iterator($ast, []));
}


function jsonReport(array $ast)
{
    return json_encode($ast);
}


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

function boolToString(array $array)
{
    return array_map(function ($value) {
        if (is_bool($value)) {
            $value = $value ? "true" : "false";
        }
        return $value;
    }, $array);
}
