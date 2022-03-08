#!/usr/bin/env php
<?php

$baseline = __DIR__ . '/baseline_clover.xml';
$local = __DIR__ . '/clover.xml';

if (! file_exists($local)) {
    echo 'Can only generate report if coverage was created';
    exit(1);
}

if (! file_exists($baseline)) {
    echo 'Missing baseline clover file.';
    exit(1);
}

// Get the new data.
$clover = new SimpleXMLElement(file_get_contents($local));
$currentMetrics = $clover->xPath('project/metrics')[0] ?? null;

$attributes = $currentMetrics->attributes();

$loc = $attributes->ncloc;
$files = $attributes->files;
$classes = $attributes->classes;
$methods = [$attributes->methods, $attributes->coveredmethods];
$statements = [$attributes->statements, $attributes->coveredstatements];

// Get the original data.
$baseClover = new SimpleXMLElement(file_get_contents($baseline));
$baseReportMetrics = $baseClover->xPath('project/metrics')[0] ?? null;

$baseAttributes = $baseReportMetrics->attributes();

$baseLoc = $baseAttributes->ncloc;
$baseFiles = $baseAttributes->files;
$baseClasses = $baseAttributes->classes;
$baseMethods = [$baseAttributes->methods, $baseAttributes->coveredmethods];
$baseSatements = [$baseAttributes->statements, $baseAttributes->coveredstatements];

//  Build the rows.
$rows = [];

$rows[] = [
    'Lines of code',
    $baseLoc,
    $loc,
    $loc - $baseLoc,
];

$rows[] = [
    'Files',
    $baseFiles,
    $files,
    $files - $baseFiles,
];

$rows[] = [
    'Classes',
    $baseClasses,
    $classes,
    $classes - $baseClasses,
];

$rows[] = [
    'Methods/Covered',
    $baseMethods[1] . ' / ' . $baseMethods[0],
    $methods[1] . ' / ' . $methods[0],
    $methods[1] - $baseMethods[1],
];

$rows[] = [
    'Statements/Covered',
    $baseSatements[1] . ' / ' . $baseSatements[0],
    $statements[1] . ' / ' . $statements[0],
    $statements[1] - $baseSatements[1],
];

$headers = ['', 'old', 'new', 'diff'];

$lines = ['', ''];

// Just to make the table pretty;
$padAmount = 20;

foreach (['', 'old', 'new', 'diff'] as $index => $header) {
    $lines[0] .= '|' . str_pad($header, $padAmount);
    $lines[1] .= '|' . str_pad('', $padAmount, '-');
    if ($index === 3) {
        $lines[0] .= '|' . PHP_EOL;
        $lines[1] .= '|' . PHP_EOL;
    }
}

foreach ($rows as $index => $row) {
    $lineIndex = 1 + $index;
    $lines[$lineIndex] = '';
    foreach ($row as $index => $data) {
        $lines[$lineIndex] .= '|' . str_pad($data, $padAmount);
        if ($index === 3) {
            $lines[$lineIndex] .= '|' . PHP_EOL;
        }
    }
}

foreach ($lines as $line) {
    echo $line;
}
