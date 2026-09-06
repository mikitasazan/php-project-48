<?php

declare(strict_types=1);

namespace Differ\Formatters\Plain;

function stringify(mixed $value): string
{
    if (is_bool($value)) {
        return $value ? 'true' : 'false';
    }

    if (is_null($value)) {
        return 'null';
    }

    if (is_string($value)) {
        return "'{$value}'";
    }

    if (is_object($value) || is_array($value)) {
        return '[complex value]';
    }

    return (string) $value;
}

function buildPropertyName(string $ancestry, string $key): string
{
    return "{$ancestry}{$key}";
}

function renderChildren(array $children, string $ancestry): array
{
    $mapped = array_map(fn(array $child): array => renderNode($child, $ancestry), $children);
    return array_merge([], ...$mapped);
}

// Dispatch is by node type; there is no exception node handled outside the switch.
function renderNode(array $node, string $ancestry): array
{
    $propertyName = buildPropertyName($ancestry, (string) ($node['key'] ?? ''));

    return match ($node['type']) {
        'root' => renderChildren($node['children'], $ancestry),
        'nested' => renderChildren($node['children'], "{$propertyName}."),
        'added' => [sprintf("Property '%s' was added with value: %s", $propertyName, stringify($node['value']))],
        'deleted' => [sprintf("Property '%s' was removed", $propertyName)],
        'changed' => [sprintf(
            "Property '%s' was updated. From %s to %s",
            $propertyName,
            stringify($node['value1']),
            stringify($node['value2']),
        )],
        'unchanged' => [],
        default => throw new \Exception("Unknown type: {$node['type']}"),
    };
}

function render(array $tree): string
{
    $lines = renderNode($tree, ancestry: '');
    return implode("\n", $lines);
}
