<?php

namespace Differ\ReportGenerator\PlainReporter;

use function Differ\ReportGenerator\ReporterUtils\boolToString;
use function Funct\Collection\flatten;

function plainReport($ast, $pathToNode = '')
{
    $preparedText = array_map(function ($node) use ($pathToNode) {
        return renderNode($node, $pathToNode);
    }, $ast);
    $plainText = flatten($preparedText);
    return implode("\n", $plainText);
}

function renderNode($node, $pathToNode)
{
    $plainTypeMap = [
        'nested' => function ($node) {
            return plainReport($node['children'], "{$node['node']}.");
        },
        'added' => function ($node) use ($pathToNode) {
            return "Property '{$pathToNode}{$node['node']}' was added with value: " . plainValueToString($node['to']);
        },
        'removed' => function ($node) use ($pathToNode) {
            return "Property '{$pathToNode}{$node['node']}' was removed";
        },
        'changed' => function ($node) use ($pathToNode) {
            return "Property '{$pathToNode}{$node['node']}' was changed. From " .
                plainValueToString($node['from']) . ' to ' . plainValueToString($node['to']);
        },
        'unchanged' => function ($node) {
            return [];
        }
    ];
    return $plainTypeMap[$node['type']]($node);
}

function plainValueToString($value)
{
    return is_array($value) ? "'complex value'" : "'" . boolToString($value) . "'";
}
