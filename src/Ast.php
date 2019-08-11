<?php

namespace Gendiff\AST;

use function Funct\Collection\union;

function makeAst(array $firstData, array $secondData)
{
    $union = union(array_keys($firstData), array_keys($secondData));
    return array_reduce($union, function ($acc, $key) use ($firstData, $secondData) {
        if (array_key_exists($key, $firstData) && array_key_exists($key, $secondData)) {
            if (is_array($firstData[$key]) && is_array($secondData[$key])) {
                $acc[] = ['type' => 'nested',
                    'node' => $key,
                    'children' => makeAst($firstData[$key], $secondData[$key])];
            } elseif ($firstData[$key] === $secondData[$key]) {
                    $acc[] = ['type' => 'unchanged',
                        'node' => $key,
                        'from' => $secondData[$key],
                        'to' => $secondData[$key]];
            } else {
                    $acc[] = ['type' => 'changed',
                        'node' => $key,
                        'from' => $firstData[$key],
                        'to' => $secondData[$key]];
            }
        } elseif (!array_key_exists($key, $firstData)) {
            $acc[] = ['type' => 'added',
                'node' => $key,
                'from' => '',
                'to' => $secondData[$key]];
        } else {
            $acc[] = ['type' => 'removed',
                'node' => $key,
                'from' => $firstData[$key],
                'to' => ''];
        }
        return $acc;
    }, []);
}
