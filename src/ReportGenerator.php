<?php

namespace Gendiff\ReportGenerator;

use function Funct\Collection\flattenAll;

function generateReport(string $format, array $ast)
{
    switch ($format) {
        case 'plain':
            return plainReport($ast);
        case 'pretty':
            return prettyReport($ast);
        case 'json':
            return jsonReport($ast);
        default:
            throw new \Exception("{$format} format is unsupported.");
    }
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
    $lines = array_map(function ($currentNode) use ($level) {
        $prepNode = boolToString($currentNode);
        switch ($prepNode['type']) {
            case 'nested':
                ['node' => $key, 'children' => $children] = $prepNode;
                $str = getIndents($level) . '    ' . $key . ': ' . prettyReport($children, $level + 1);
                break;
            case 'added':
                $str = getPreparedPrettyLine($level, '+', $prepNode['node'], $prepNode['to']);
                break;
            case 'removed':
                $str = getPreparedPrettyLine($level, '-', $prepNode['node'], $prepNode['from']);
                break;
            case 'unchanged':
                $str = getPreparedPrettyLine($level, ' ', $prepNode['node'], $prepNode['from']);
                break;
            case 'changed':
                $str = [getPreparedPrettyLine($level, '-', $prepNode['node'], $prepNode['from']),
                    getPreparedPrettyLine($level, '+', $prepNode['node'], $prepNode['to'])];
                break;
        }
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
