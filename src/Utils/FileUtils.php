<?php

namespace Gendiff\Utils\FileUtils;

use function Funct\Collection\flatten;
use Symfony\Component\Yaml\Yaml;

function isFileExist($fileName, $fileLocation)
{
    return file_exists("{$fileLocation}{$fileName}");
}

function getJsonFileContent($fileName, $fileLocation)
{
    if (!isFileExist($fileName, $fileLocation)) {
        return [];
    }
    $fileRawContent = file_get_contents("{$fileLocation}{$fileName}");
    $fileJsonContent = json_decode($fileRawContent, true);
    return $fileJsonContent;
}

function getYamlFileContent($fileName, $fileLocation)
{
    if (!isFileExist($fileName, $fileLocation)) {
        return [];
    }
    $fileContent = Yaml::parseFile("{$fileLocation}{$fileName}");
    foreach ($fileContent as $key => $value) {
        $fileContent[$key] = $fileContent[$key][0];
    }
    return $fileContent;
}
