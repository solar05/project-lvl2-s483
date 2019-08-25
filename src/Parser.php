<?php

namespace  Differ\Parser;

use Symfony\Component\Yaml\Yaml;

function parseContent($content, $extension)
{
    $typeMap = [
        'json' => function ($content) {
            $parsedContent = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception(json_last_error_msg());
            }
            return $parsedContent;
        },
        'yaml' => function ($content) {
            $parsedContent = Yaml::parse($content, true);
            return $parsedContent;
        }];
    $result = $typeMap[$extension]($content);
    return $result;
}
