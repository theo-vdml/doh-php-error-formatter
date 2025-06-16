<?php

namespace DohFormatting\Doh\Formatters;

use DohFormatting\Doh\Contracts\FormatterInterface;
use DohFormatting\Doh\Doh;
use DohFormatting\Doh\Exception\Frame;
use Highlight\Highlighter;
use function HighlightUtilities\splitCodeIntoArray;


/**
 * Outputs a Doh instance as an HTML formatted error page with syntax-highlighted code excerpts.
 */
class HtmlFormatter implements FormatterInterface
{
    /**
     * Highlights the code excerpt from a Frame using syntax highlighting.
     *
     * Retrieves a code snippet from the frame's file around the frame's line number,
     * then highlights it with PHP syntax coloring, returning the result as an HTML string.
     *
     * @param Frame $frame The stack trace frame containing file and line info.
     * @param int   $padding Number of lines before and after the target line to include.
     * @return string|null The HTML formatted and syntax-highlighted code excerpt, or null if unavailable.
     */
    private function highlightCodeExcerpt(Frame $frame, int $padding = 7): ?string
    {
        $excerpt = $frame->getExcerpt($padding);
        if (!$excerpt) {
            return null;
        }

        $hl = new Highlighter();
        $highlighted = $hl->highlight('php', $excerpt, false);
        $highlightedLines = splitCodeIntoArray($highlighted->value);

        $start = max(0, $frame->line - $padding - 1);
        $line = $frame->line;

        $html = "<pre><code class=\"block text-sm font-mono text-zinc-100 hljs language-{$highlighted->language}\">";

        foreach ($highlightedLines as $i => $htmlLine) {
            $lineNumber = $start + $i + 1;
            $isHighlighted = $lineNumber === $line;

            $html .= '<div class="flex ' . ($isHighlighted ? 'bg-red-900/30' : '') . '">';
            $html .= '<span class="w-12 text-right pr-4 text-zinc-500 select-none">' . $lineNumber . '</span>';
            $html .= '<span class="flex-1">' . $htmlLine . '</span>';
            $html .= '</div>';
        }

        $html .= '</code></pre>';
        return $html;
    }

    /**
     * Returns an array of syntax-highlighted code excerpts for an array of Frames.
     *
     * Maps each frame to its highlighted excerpt or null if unavailable.
     *
     * @param Frame[] $frames Array of Frame instances to generate highlights for.
     * @return array<int, string|null> Array of HTML strings or nulls corresponding to each frame.
     */
    private function getHighlights(array $frames)
    {
        return array_map(function (Frame $frame): string|null {
            return $this->highlightCodeExcerpt($frame);
        }, $frames);
    }

    /**
     * Escapes a string for safe HTML output.
     *
     * Converts special characters to HTML entities to prevent XSS vulnerabilities.
     *
     * @param string $str The input string to escape.
     * @return string The escaped string safe for HTML output.
     */
    private function escape(string $str): string
    {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generates an HTML representation of the given Doh instance.
     *
     * Builds the full HTML error page including the exception class, message,
     * syntax-highlighted code excerpts for each frame, and uses a template to render.
     *
     * @param Doh $doh The Doh instance wrapping the throwable to render.
     * @return string The fully rendered HTML error page.
     */
    public function output(Doh $doh): string
    {
        // Variables for the template
        $trace = $doh->buildTrace();
        $class = $this->escape($doh->getClass());
        $message = $this->escape($doh->getMessage());
        $highlights = $this->getHighlights($trace->all());
        $relevant = $trace->mostRelevantIndex();

        ob_start();
        include __DIR__ . '/../templates/exception-details.php';
        return ob_get_clean();
    }
}