<?php

namespace Gendiff\ReportGenerator;


class ReportGenerator
{

    public function generateReport(string $format, array $ast)
    {
        switch ($format) {
            case 'plain':
                return $this->plainReport($ast);
            default:
                throw new \Exception("{$format} format is unsupported.");
        }
    }

    private function plainReport(array $ast)
    {
        $iterator = function ($ast, $parents) use (&$iterator) {
            return array_reduce($ast, function ($acc, $node) use ($iterator, $parents) {
                $parents[] = $node['node'];
                $pathToNode = implode('.', $parents);
                switch ($node['type']) {
                    case 'nested':
                        $acc = array_merge($acc, $iterator($node['children'], $parents));
                        break;
                    case 'added':
                        if (is_array($node['to'])) {
                            $acc[] = "Property '{$pathToNode}' was added with value: 'complex value'";
                        } else {
                            $acc[] = "Property '{$pathToNode}' was added with value: '{$node['to']}'";
                        }
                        break;
                    case 'changed':
                        $acc[] = "Property '{$pathToNode}' was changed. From '{$node['from']}' to '{$node['to']}'";
                        break;
                    case 'removed':
                        $acc[] = "Property '{$pathToNode}' was removed";
                        break;
                }
                return $acc;
            }, []);
        };
        return implode("\n", $iterator($ast, []));
    }
}
