<?php

namespace Gendiff\AST;

use function Funct\Collection\union;

function makeAst(array $firstData, array $secondData)
{
    $firstArray = boolToString($firstData);
    $secondArray = boolToString($secondData);
    $union = union(array_keys($firstArray), array_keys($secondArray));
    return array_reduce($union, function ($acc, $key) use ($firstArray, $secondArray) {
        if (array_key_exists($key, $firstArray) && array_key_exists($key, $secondArray)) {
            if (is_array($firstArray[$key]) && is_array($secondArray[$key])) {
                $acc[] = ['type' => 'nested',
                    'node' => $key,
                    'from' => null,
                    'to' => null,
                    'children' => makeAst($firstArray[$key], $secondArray[$key])];
            } elseif ($firstArray[$key] === $secondArray[$key]) {
                    $acc[] = ['type' => 'unchanged',
                        'node' => $key,
                        'from' => $secondArray[$key],
                        'to' => $secondArray[$key],
                        'children' => null];
            } else {
                    $acc[] = ['type' => 'changed',
                        'node' => $key,
                        'from' => $firstArray[$key],
                        'to' => $secondArray[$key],
                        'children' => null];
            }
        } elseif (!array_key_exists($key, $firstArray)) {
            $acc[] = ['type' => 'added',
                'node' => $key,
                'from' => '',
                'to' => $secondArray[$key],
                'children' => null];
        } else {
            $acc[] = ['type' => 'removed',
                'node' => $key,
                'from' => $firstArray[$key],
                'to' => '',
                'children' => null];
        }
        return $acc;
    }, []);
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
