<?php

namespace DohFormatting\Doh\Formatters;

use DohFormatting\Doh\Contracts\FormatterInterface;
use DohFormatting\Doh\Doh;

/**
 * Outputs a Doh instance as a Markdown formatted error report.
 */
class MarkdownFormatter implements FormatterInterface
{
    /**
     * Converts the Doh throwable to a markdown formatted string.
     *
     * @param Doh $doh
     * @return string Markdown representation of the error and stack trace.
     */
    public function output(Doh $doh): string
    {
        $t = $doh->getThrowable();

        $markdown = "## Exception: " . get_class($t) . "\n\n";
        $markdown .= "**Message:** " . $t->getMessage() . "\n\n";
        $markdown .= "**Location:** " . $t->getFile() . ":" . $t->getLine() . "\n\n";
        $markdown .= "```\n" . $t->getTraceAsString() . "\n```\n";

        return $markdown;
    }
}
