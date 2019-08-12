<?php

namespace Differ\ReportGenerator\ReporterUtils;

function boolToString($value)
{
    if (is_array($value)) {
        return array_map(function ($value) {
            if (is_bool($value)) {
                $value = $value ? "true" : "false";
            }
            return $value;
        }, $value);
    } elseif (is_bool($value)) {
        return $value ? "true" : "false";
    }
    return $value;
}
