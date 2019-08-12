<?php

namespace Differ\ReportGenerator\PlainReporter;

use function Differ\ReportGenerator\ReporterUtils\boolToString;

function plainReport($ast)
{
    return implode("\n", prepareReport($ast));
}

function prepareReport(array $ast, $parents = '')
{
    $sortedNodes = array_filter($ast, function ($key) {
        return $key['type'] !== 'unchanged';
    });
    $plainTypeMap = [
        'nested' => function ($node) use ($parents) {
            $nextParents = $parents . $node['node'] . '.';
            return prepareReport($node['children'], $nextParents);
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
