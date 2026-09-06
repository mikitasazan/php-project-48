<?php

declare(strict_types=1);

namespace Differ\Differ;

use function Differ\Formatters\format;
use function Differ\Parsers\parse;

function genDiff(string $path1, string $path2, string $formatName = 'stylish'): string
{
    $data1 = parse(getFormatName($path1), readFile($path1));
    $data2 = parse(getFormatName($path2), readFile($path2));
    $diffTree = buildDiffTree($data1, $data2);
    return format($formatName, $diffTree);
}

function readFile(string $filepath): string
{
    $absolutePath = realpath($filepath);
    if ($absolutePath === false) {
        throw new \Exception("File '{$filepath}' does not exist");
    }
    return (string) file_get_contents($absolutePath);
}

function getFormatName(string $filepath): string
{
    return pathinfo($filepath, PATHINFO_EXTENSION);
}

function buildDiffTree(object $data1, object $data2): array
{
    return [
        'type' => 'root',
        'children' => buildDiffNodes($data1, $data2),
    ];
}

// A single pass over the union of keys from both objects, keys sorted.
// Node types are exactly five: added, deleted, changed, unchanged, nested.
function buildDiffNodes(object $data1, object $data2): array
{
    $keys1 = array_keys(get_object_vars($data1));
    $keys2 = array_keys(get_object_vars($data2));
    $sortedKeys = array_unique(array_merge($keys1, $keys2));
    sort($sortedKeys);

    return array_map(fn(string $key): array => buildDiffNode($key, $data1, $data2), $sortedKeys);
}

function buildDiffNode(string $key, object $data1, object $data2): array
{
    if (!property_exists($data2, $key)) {
        return ['key' => $key, 'type' => 'deleted', 'value' => $data1->$key];
    }

    if (!property_exists($data1, $key)) {
        return ['key' => $key, 'type' => 'added', 'value' => $data2->$key];
    }

    $value1 = $data1->$key;
    $value2 = $data2->$key;

    if (is_object($value1) && is_object($value2)) {
        return ['key' => $key, 'type' => 'nested', 'children' => buildDiffNodes($value1, $value2)];
    }

    if ($value1 === $value2) {
        return ['key' => $key, 'type' => 'unchanged', 'value' => $value1];
    }

    return ['key' => $key, 'type' => 'changed', 'value1' => $value1, 'value2' => $value2];
}
