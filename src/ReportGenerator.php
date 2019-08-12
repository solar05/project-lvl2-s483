<?php

namespace Gendiff\ReportGenerator;

use function Funct\Collection\flattenAll;

function generateReport(string $format, array $ast)
{
    $formatMap = [
        'plain' => function ($ast) {
            return implode("\n", plainReport($ast));
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

function plainReport(array $ast, $parents = '')
{
    $sortedNodes = array_filter($ast, function ($key) {
        return $key['type'] !== 'unchanged';
    });
    $plainTypeMap = [
        'nested' => function ($node) use ($parents) {
            $nextParents = $parents . $node['node'] . '.';
            return plainReport($node['children'], $nextParents);
        },
        'added' => function ($node) use ($parents) {
            $pathToNode = $parents . $node['node'];
            $value = plainValueToString($node['to']);
            return ["Property '{$pathToNode}' was added with value: '{$value}'"];
        },
        'removed' => function ($node) use ($parents) {
            $pathToNode = $parents . $node['node'];
            return ["Property '{$pathToNode}' was removed"];
        },
        'changed' => function ($node) use ($parents) {
            $pathToNode = $parents . $node['node'];
            $oldValue = plainValueToString($node['from']);
            $newValue = plainValueToString($node['to']);
            return ["Property '{$pathToNode}' was changed. From '{$oldValue}' to '{$newValue}'"];
        }
    ];
    return array_reduce($sortedNodes, function ($acc, $node) use ($plainTypeMap) {
        $newAcc = array_merge($acc, $plainTypeMap[$node['type']]($node));
        return $newAcc;
    }, []);
}

function plainValueToString($value)
{
    return is_array($value) ? 'complex value' : boolToString($value);
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

function boolToString($value)
{
    if (is_array($value)) {
        return array_map(function ($value) {
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            }
            return $value;
        }, $value);
    } elseif (is_bool($value)) {
        return $value ? "true" : "false";
    }
    return $value;
}
