<?php

declare(strict_types=1);

namespace Differ\Formatters\Stylish;

const INDENT_SIZE = 4;

// Depth is primary; the indent is derived from it (depth * 4 spaces, minus the
// 2-character marker '+ ' / '- ' / '  ' that precedes the closing brace text).
function buildIndent(int $depth): string
{
    return str_repeat(' ', ($depth * INDENT_SIZE) - 2);
}

function stringify(mixed $value, int $depth): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_array($value)) {
        return implode(' ', $value);
    }

    if (!is_object($value)) {
        return (string) $value;
    }

    return stringifyObject($value, $depth);
}

function stringifyObject(object $value, int $depth): string
{
    $keys = array_keys(get_object_vars($value));
    $lines = array_map(function (string $key) use ($value, $depth): string {
        $formattedValue = stringify($value->$key, $depth + 1);
        return buildIndent($depth + 1) . "  {$key}: {$formattedValue}";
    }, $keys);
    $body = implode("\n", $lines);
    return "{\n{$body}\n" . buildIndent($depth) . '  }';
}

function renderRoot(array $children, int $depth): string
{
    $mapped = array_map(fn(array $child): string => renderNode($child, $depth), $children);
    $body = implode("\n", $mapped);
    return "{\n{$body}\n}";
}

function renderChanged(array $node, int $depth): string
{
    $indent = buildIndent($depth);
    $lines = [
        "{$indent}- {$node['key']}: " . stringify($node['value1'], $depth),
        "{$indent}+ {$node['key']}: " . stringify($node['value2'], $depth),
    ];
    return implode("\n", $lines);
}

function renderNested(array $node, int $depth): string
{
    $indent = buildIndent($depth);
    $mapped = array_map(fn(array $child): string => renderNode($child, $depth + 1), $node['children']);
    $body = implode("\n", $mapped);
    return "{$indent}  {$node['key']}: {\n{$body}\n{$indent}  }";
}

// Dispatch is entirely by node type. Nothing is handled outside the match.
function renderNode(array $node, int $depth): string
{
    $indent = buildIndent($depth);

    return match ($node['type']) {
        'root' => renderRoot($node['children'], $depth),
        'unchanged' => "{$indent}  {$node['key']}: " . stringify($node['value'], $depth),
        'added' => "{$indent}+ {$node['key']}: " . stringify($node['value'], $depth),
        'deleted' => "{$indent}- {$node['key']}: " . stringify($node['value'], $depth),
        'changed' => renderChanged($node, $depth),
        'nested' => renderNested($node, $depth),
        default => throw new \Exception("Unknown type: {$node['type']}"),
    };
}

function render(array $tree): string
{
    return renderNode($tree, depth: 1);
}
