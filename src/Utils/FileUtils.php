<?php

namespace Gendiff\Utils\FileUtils;

function isFilesExists($firstFilePath, $secondFilePath)
{
    return file_exists($firstFilePath) && file_exists($secondFilePath);
}

function isFilesExtensionSame($firstFileName, $secondFileName)
{
    $firstFileExtension = explode('.', $firstFileName);
    $secondFileExtension = explode('.', $secondFileName);
    return $firstFileExtension[1] === $secondFileExtension[1];
}
