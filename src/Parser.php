<?php

namespace  Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseContent($content, $extension)
{
    $typeMap = [
        'json' => function ($content) {
            $jsonContent = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(json_last_error_msg());
            }
            return $jsonContent;
        },
        'yaml' => function ($content) {
            $fileYamlContent = Yaml::parse($content, true);
            return $fileYamlContent;
        }];
    $result = $typeMap[$extension]($content);
    return $result;
}
