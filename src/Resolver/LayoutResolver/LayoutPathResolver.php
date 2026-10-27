<?php

declare(strict_types=1);

namespace Efabrica\PHPStanLatte\Resolver\LayoutResolver;

use function dirname;
use function file_get_contents;
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
        $matches = [];
        if (preg_match(
            '/\{(?:layout|extend|extends)\s+(?:[\'"](?<quoted>[^\'"]+)[\'"]|(?<bare>[^\s,}]+))/',
            $templateContent,
            $matches
        ) === 1) {
            // "bare" is the only key PHP can leave unset (see preg_match named-group behavior)
            $layoutName = isset($matches['bare']) ? $matches['bare'] : $matches['quoted'];
        } else {
            $layoutName = null;
        }
        $isQuoted = !isset($matches['bare']);
        if ($layoutName === 'none') {
            return null; // {layout none} disables the layout completely
        }
        if ($layoutName !== null && $layoutName !== 'auto') {
            $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . $layoutName);
            if ($layoutFilePath !== false) {
                return $layoutFilePath;
            }
            // keep explicit literal paths so a missing layout is reported instead of
            // silently replaced by @layout.latte; dynamic expressions have no path to check
            if ($isQuoted || preg_match('/^\$|[(\[]|->|::/', $layoutName) !== 1) {
                return dirname($templatePath) . DIRECTORY_SEPARATOR . $layoutName;
            }
        }

        // no layout tag, {layout auto} or a dynamic layout name
        $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . '@layout.latte') ?: null;
        if ($layoutFilePath !== null) {
            return $layoutFilePath;
        }

        $layoutFilePath = realpath(dirname($templatePath) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '@layout.latte') ?: null;
        return $layoutFilePath;
    }
}
