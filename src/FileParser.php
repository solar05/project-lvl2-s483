<?php

namespace  Gendiff\FileParser;

use Symfony\Component\Yaml\Yaml;

function parseFiles($firstPathToFile, $secondPathToFile, $filesExtension)
{
    $result = [];
    switch ($filesExtension) {
        case 'json':
            $result = parseJson($firstPathToFile, $secondPathToFile);
            break;
        case 'yaml':
            $result = parseYaml($firstPathToFile, $secondPathToFile);
            break;
    }
    return $result;
}

function parseJson($firstPathToFile, $secondPathToFile)
{
    $firstFileRawContent = file_get_contents($firstPathToFile);
    $secondFileRawContent = file_get_contents($secondPathToFile);
    $fileJsonContent = json_decode($firstFileRawContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception(json_last_error_msg());
    }
    $secondJsonContent = json_decode($secondFileRawContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception(json_last_error_msg());
    }
    return [$fileJsonContent, $secondJsonContent];
}

function parseYaml($firstPathToFile, $secondPathToFile)
{
    $firstFileContent = Yaml::parseFile($firstPathToFile);
    $secondFileContent = Yaml::parseFile($secondPathToFile);
    return [$firstFileContent, $secondFileContent];
}
