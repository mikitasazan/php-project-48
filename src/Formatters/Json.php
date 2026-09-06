<?php

declare(strict_types=1);

namespace Differ\Formatters\Json;

// The json format is a plain serialization of the internal diff tree,
// never a string assembled by hand.
function render(array $tree): string
{
    return json_encode($tree, JSON_THROW_ON_ERROR);
}
