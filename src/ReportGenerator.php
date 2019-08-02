<?php

namespace Gendiff\ReportGenerator;

use function Funct\Collection\flattenAll;
use function Funct\Collection\get;

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
            $parents[] = $node['node'];
            $pathToNode = implode('.', $parents);
            switch ($node['type']) {
                case 'nested':
                    $acc = array_merge($acc, $iterator($node['children'], $parents));
                    break;
                case 'added':
                    if (is_array($node['to'])) {
                        $acc[] = "Property '{$pathToNode}' was added with value: 'complex value'";
                    } else {
                        $acc[] = "Property '{$pathToNode}' was added with value: '{$node['to']}'";
                    }
                    break;
                case 'changed':
                    $acc[] = "Property '{$pathToNode}' was changed. From '{$node['from']}' to '{$node['to']}'";
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
        ['type' => $type,
        'node' => $key,
        'from' => $oldValue,
        'to' => $newValue,
        'children' => $children] = $currentNode;
        switch ($type) {
            case 'nested':
                $str = getIndents($level) . '    ' . $key . ': ' . prettyReport($children, $level + 1);
                break;
            case 'added':
                $str = getIndents($level) . '  + ' . $key . ': ' . processToString($newValue, $level + 1);
                break;
            case 'removed':
                $str = getIndents($level) . '  - ' . $key . ': ' . processToString($oldValue, $level + 1);
                break;
            case 'unchanged':
                $str = getIndents($level) . '    ' . $key . ': ' . processToString($oldValue, $level + 1);
                break;
            case 'changed':
                $str = [getIndents($level) . '  - ' . $key . ': ' . processToString($oldValue, $level + 1),
                    getIndents($level) . '  + ' . $key . ': ' . processToString($newValue, $level + 1)];
                break;
        }
        return $str;
    }, $ast);
    $text = implode("\n", flattenAll($lines));
    return getFinalString($text, $level);
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
    return getFinalString($text, $level);
}

function getFinalString($text, $level)
{
    $indents = getIndents($level);
    return "{\n{$text}{$indents}\n}";
}

function getIndents($level)
{
    return str_repeat('', $level * 4);
}
