<?php

namespace Differ\ReportGenerator\JsonReporter;

function jsonReport(array $ast)
{
    return json_encode($ast);
}
