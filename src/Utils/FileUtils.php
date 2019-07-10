<?php

namespace Gendiff\Utils\FileUtils;

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
