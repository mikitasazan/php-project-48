<?php

declare(strict_types=1);

// The threshold check lives in its own script rather than inline in the
// Makefile: make breaks a long recipe line across continuations, and php
// receives it in pieces.

$reportPath = $argv[1] ?? 'build/logs/clover.xml';
$minimum = (float) ($argv[2] ?? 0);

if (!file_exists($reportPath)) {
    fwrite(STDERR, "Coverage report not found: {$reportPath}\n");
    exit(1);
}

$report = simplexml_load_file($reportPath);

if ($report === false) {
    fwrite(STDERR, "Coverage report is not readable: {$reportPath}\n");
    exit(1);
}

$metrics = $report->project->metrics;
$statements = (int) $metrics['statements'];
$covered = (int) $metrics['coveredstatements'];
$percent = $statements > 0 ? $covered / $statements * 100 : 100.0;

printf("Coverage: %.2f%%, minimum %.2f%%\n", $percent, $minimum);

exit($percent + 1e-9 < $minimum ? 1 : 0);
