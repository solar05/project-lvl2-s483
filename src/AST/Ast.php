<?php

namespace Gendiff\AST;

function makeAst($parsedData)
{
    echo 'a';
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
