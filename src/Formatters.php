<?php

declare(strict_types=1);

namespace Differ\Formatters;

use function Differ\Formatters\Json\render as renderJson;
use function Differ\Formatters\Plain\render as renderPlain;
use function Differ\Formatters\Stylish\render as renderStylish;

function format(string $formatName, array $tree): string
{
    return match ($formatName) {
        'stylish' => renderStylish($tree),
        'plain' => renderPlain($tree),
        'json' => renderJson($tree),
        default => throw new \Error("Unknown format: {$formatName}"),
    };
}
