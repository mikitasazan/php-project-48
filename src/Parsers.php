<?php

declare(strict_types=1);

// A parser turns raw content plus a format name into data. It knows nothing
// about the filesystem or file extensions.

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse(string $formatName, string $data): object
{
    return match ($formatName) {
        'json' => json_decode($data),
        'yml', 'yaml' => Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP),
        default => throw new \Exception("Unknown format: '{$formatName}'"),
    };
}
