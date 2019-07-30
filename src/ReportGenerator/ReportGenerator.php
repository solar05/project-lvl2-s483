<?php

namespace Gendiff\ReportGen;


class ReportGenerator
{
    private $ast;

    public function __construct(array $ast)
    {
        $this->ast = $ast;
    }
}
