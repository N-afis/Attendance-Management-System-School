<?php

function loadEnv($path)
{
    if (!file_exists($path)) {
        throw new Exception(".env file not found at $path");
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        if (!strpos($line, '=')) continue;

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remove optional quotes
        $value = trim($value, "\"'");

        $_ENV[$name] = $value;
    }
}
