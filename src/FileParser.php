<?php

namespace  Differ\FileParser;

use Symfony\Component\Yaml\Yaml;

function parseFileContent($fileContent, $fileExtension)
{
    $typeMap = [
        'json' => function ($fileContent) {
            return parseJsonContent($fileContent);
        },
        'yaml' => function ($fileContent) {
            return parseYamlContent($fileContent);
        }];
    $result = $typeMap[$fileExtension]($fileContent);
    return $result;
}

function parseJsonContent($fileContent)
{
    $fileJsonContent = json_decode($fileContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new \Exception(json_last_error_msg());
    }
    return $fileJsonContent;
}

function parseYamlContent($fileContent)
{
    $fileYamlContent = Yaml::parse($fileContent, true);
    return $fileYamlContent;
}
