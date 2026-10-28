<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Resolver\LayoutResolver;

use function dirname;
use function file_get_contents;
use function preg_match;
use function realpath;
use function str_contains;
use function str_replace;
use function str_starts_with;
use function stripcslashes;
use function strlen;
use function trim;
use const DIRECTORY_SEPARATOR;
use const PREG_OFFSET_CAPTURE;

final class LayoutPathResolver
{
    private bool $featureAnalyseLayoutFiles;

    public function __construct(bool $featureAnalyseLayoutFiles)
    {
        $this->featureAnalyseLayoutFiles = $featureAnalyseLayoutFiles;
    }

    public function resolve(?string $templatePath): ?string
    {
        if (!$this->featureAnalyseLayoutFiles) {
            return null;
        }

        if ($templatePath === null) {
            return null;
        }

        $templateContent = file_get_contents($templatePath) ?: '';
        $expression = $this->extractLayoutExpression($templateContent);

        if ($expression === 'none') {
            return null; // {layout none} disables the layout completely
        }
        if ($expression !== null && $expression !== 'auto') {
            $layoutName = $this->resolveLiteral($expression);
            if ($layoutName !== null) {
                $layoutFilePath = $this->isAbsolutePath($layoutName)
                    ? $layoutName
                    : dirname($templatePath) . DIRECTORY_SEPARATOR . $layoutName;
                return realpath($layoutFilePath) ?: $layoutFilePath;
            }
        }

        // no layout tag, {layout auto} or a dynamic layout expression
        $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . '@layout.latte') ?: null;
        if ($layoutFilePath !== null) {
            return $layoutFilePath;
        }

        $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '@layout.latte') ?: null;
        return $layoutFilePath;
    }

    /**
     * Returns the raw layout tag argument, e.g. "'layout.latte'" or "$layout".
     */
    private function extractLayoutExpression(string $templateContent): ?string
    {
        if (preg_match('/\{(?:layout|extend|extends)\s+/', $templateContent, $match, PREG_OFFSET_CAPTURE) !== 1) {
            return null;
        }

        $offset = $match[0][1] + strlen($match[0][0]);
        $length = strlen($templateContent);
        $quote = null;
        $expression = '';
        for ($i = $offset; $i < $length; $i++) {
            $char = $templateContent[$i];
            if ($quote !== null) {
                $expression .= $char;
                if ($char === '\\' && $i + 1 < $length) {
                    $expression .= $templateContent[++$i];
                } elseif ($char === $quote) {
                    $quote = null;
                }
                continue;
            }
            if ($char === "'" || $char === '"') {
                $quote = $char;
                $expression .= $char;
                continue;
            }
            if ($char === '}') {
                return trim($expression);
            }
            $expression .= $char;
        }

        return null;
    }

    /**
     * Returns the value of a single, non-interpolated string literal, or null for any
     * other expression (concatenation, variable, interpolation, ...).
     */
    private function resolveLiteral(string $expression): ?string
    {
        if (preg_match('/^\'((?:[^\'\\\\]|\\\\.)*)\'$/s', $expression, $match) === 1) {
            return str_replace(["\\'", '\\\\'], ["'", '\\'], $match[1]);
        }
        if (preg_match('/^"((?:[^"\\\\$]|\\\\.)*)"$/s', $expression, $match) === 1) {
            return stripcslashes($match[1]);
        }
        // Latte also accepts bare layout file names such as ../@layout.latte
        if (preg_match('/^[^\s,()\[\]\$\'"{}]+$/', $expression) === 1
            && (str_contains($expression, '/') || str_contains($expression, '.'))) {
            return $expression;
        }

        return null;
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, DIRECTORY_SEPARATOR)
            || preg_match('/^[A-Za-z]:[\\\\\/]/', $path) === 1;
    }
}
