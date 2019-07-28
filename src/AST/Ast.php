<?php

namespace Gendiff\AST;

use function Funct\Collection\union;

function makeAst(array $firstArray, array $secondArray)
{
    $union = union(array_keys($firstArray), array_keys($secondArray));
    return array_reduce($union, function ($acc, $key) use ($firstArray, $secondArray) {
        if (array_key_exists($key, $firstArray) && array_key_exists($key, $secondArray)) {
            if (is_array($firstArray[$key]) && is_array($secondArray[$key])) {
                $acc[] = ['type' => 'nested',
                    'node' => $key,
                    'children' => makeAst($firstArray[$key], $secondArray[$key])];
            } elseif ($firstArray[$key] === $secondArray[$key]) {
                    $acc[] = ['type' => 'unchanged',
                        'node' => $key,
                        'from' => $secondArray[$key],
                        'to' => $secondArray[$key]];
            } else {
                    $acc[] = ['type' => 'changed',
                        'node' => $key,
                        'from' => $firstArray[$key],
                        'to' => $secondArray[$key]];
            }
        } elseif (!array_key_exists($key, $firstArray)) {
            $acc[] = ['type' => 'added',
                'node' => $key,
                'from' => '',
                'to' => $secondArray[$key]];
        } else {
            $acc[] = ['type' => 'removed',
                'node' => $key,
                'from' => $firstArray[$key],
                'to' => ''];
        }
        return $acc;
    }, []);
}

function renderAst($ast)
{
    echo 'b';
}

function generateDiff($firstData, $secondData)
{
    $result = "{\n";
    foreach ($firstData as $key => $value) {
        if (array_key_exists($key, $secondData)) {
            if ($firstData[$key] === $secondData[$key]) {
                $result = "{$result}    {$key}: {$value}\n";
            } else {
                $result = "{$result}    + {$key}: {$secondData[$key]}\n    - {$key}: {$value}\n";
            }
        } else {
            $result = "{$result}    - {$key}: {$value}\n";
        }
    }
    foreach ($secondData as $key => $value) {
        if (!array_key_exists($key, $firstData)) {
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            }
            $result = "{$result}    + {$key}: {$value}\n";
        }
    }
    $result = "{$result}}";
    return $result;
}
