<?php

namespace Differ\ReportGenerator;

use function Differ\ReportGenerator\PlainReporter\plainReport;
use function Differ\ReportGenerator\PrettyReporter\prettyReport;
use function Differ\ReportGenerator\JsonReporter\jsonReport;

function generateReport(array $ast, string $format)
{
    $formatMap = [
        'plain' => function ($ast) {
            return plainReport($ast);
        },
        'pretty' => function ($ast) {
            return prettyReport($ast);
        },
        'json' => function ($ast) {
            return jsonReport($ast);
        }

        ];
    if (!array_key_exists($format, $formatMap)) {
            throw new \Exception("{$format} format is unsupported.");
    }
    return $formatMap[$format]($ast);
}
