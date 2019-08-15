<?php

namespace Differ\ReportGenerator\PlainReporter;

use function Differ\ReportGenerator\ReporterUtils\boolToString;

function plainReport($ast)
{
    return substr_replace(prepareReport($ast), "", -1);
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
            return "Property '{$pathToNode}' was added with value: '{$value}'\n";
        },
        'removed' => function ($node) use ($parents) {
            $pathToNode = $parents . $node['node'];
            return "Property '{$pathToNode}' was removed\n";
        },
        'changed' => function ($node) use ($parents) {
            $pathToNode = $parents . $node['node'];
            $oldValue = plainValueToString($node['from']);
            $newValue = plainValueToString($node['to']);
            return "Property '{$pathToNode}' was changed. From '{$oldValue}' to '{$newValue}'\n";
        }
    ];
    return array_reduce($sortedNodes, function ($acc, $node) use ($plainTypeMap) {
        $newAcc = $acc . $plainTypeMap[$node['type']]($node);
        return $newAcc;
    }, '');
}

function plainValueToString($value)
{
    return is_array($value) ? 'complex value' : boolToString($value);
}
