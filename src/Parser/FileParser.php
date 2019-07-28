<?php

namespace  Gendiff\FileParser;

use Symfony\Component\Yaml\Yaml;

class FileParser
{
    private $filesExtension;
    private $pathToFiles;
    private $availableExtensions = ["json", "yaml"];

    public function __construct($firstPathToFile, $secondPathToFile, $extension)
    {
        if (in_array($extension, $this->availableExtensions)) {
            $this->filesExtension = $extension;
            $this->pathToFiles = ['firstPath' => $firstPathToFile, 'secondPath' => $secondPathToFile];
        } else {
            throw new \Exception("Files with {$extension} is not supported!");
        }
    }

    public function parseFiles()
    {
        $result = [];
        switch ($this->filesExtension) {
            case 'json':
                $result = $this->parseJson();
                break;
            case 'yaml':
                $result = $this->parseYaml();
                break;
        }
        return $result;
    }

    private function parseJson()
    {
        $firstFileRawContent = file_get_contents($this->pathToFiles['firstPath']);
        $secondFileRawContent = file_get_contents($this->pathToFiles['secondPath']);
        $fileJsonContent = json_decode($firstFileRawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(json_last_error_msg());
        }
        $secondJsonContent = json_decode($secondFileRawContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception(json_last_error_msg());
        }
        return ['firstFileContent' => $fileJsonContent, 'secondFileContent' => $secondJsonContent];
    }

    private function parseYaml()
    {
        $firstFileContent = Yaml::parseFile($this->pathToFiles['firstPath']);
        $secondFileContent = Yaml::parseFile($this->pathToFiles['secondPath']);
        /*$firstCorrectedFile = $this->correctYamlKeys($firstFileContent);
        $secondCorrectedFile = $this->correctYamlKeys($secondFileContent);
        return ['firstFileContent' => $firstCorrectedFile, 'secondFileContent' => $secondCorrectedFile];
        */
        return ['a' => $firstFileContent, 'b' => $secondFileContent];
    }

    private function correctYamlKeys($parsedYaml)
    {
        $result = $parsedYaml;
        foreach ($result as $key => $value) {
            $result[$key] = $result[$key][0];
        }
        return $result;
    }
}
