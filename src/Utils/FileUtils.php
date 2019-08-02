<?php

namespace Gendiff\Utils\FileUtils;

function isFilesExists($firstFilePath, $secondFilePath)
{
    return file_exists($firstFilePath) && file_exists($secondFilePath);
}
