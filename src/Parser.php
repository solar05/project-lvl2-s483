<?php

namespace  Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseContent($fileContent, $fileExtension)
{
    $typeMap = [
        'json' => function ($fileContent) {
            $fileJsonContent = json_decode($fileContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(json_last_error_msg());
            }
            return $fileJsonContent;
        },
        'yaml' => function ($fileContent) {
            $fileYamlContent = Yaml::parse($fileContent, true);
            return $fileYamlContent;
        }];
    $result = $typeMap[$fileExtension]($fileContent);
    return $result;
}
