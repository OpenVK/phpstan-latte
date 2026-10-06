<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Resolver\LayoutResolver;

use function dirname;
use function file_get_contents;
use function in_array;
use function preg_match;
use function realpath;
use const DIRECTORY_SEPARATOR;

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
        preg_match(
            '/\{(?:layout|extend|extends)\s+(?:[\'"](?<quoted>[^\'"]+)[\'"]|(?<bare>[^\s,}]+))/',
            $templateContent,
            $match
        );

        $layoutName = $match['quoted'] ?? $match['bare'] ?? null;
        if ($layoutName !== null && !in_array($layoutName, ['none', 'auto'], true)) {
            $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . $layoutName) ?: null;
            if ($layoutFilePath !== null) {
                return $layoutFilePath;
            }
        }

        $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . '@layout.latte') ?: null;
        if ($layoutFilePath !== null) {
            return $layoutFilePath;
        }

        $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '@layout.latte') ?: null;
        return $layoutFilePath;
    }
}
