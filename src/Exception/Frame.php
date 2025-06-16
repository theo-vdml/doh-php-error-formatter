<?php

namespace DohFormatting\Doh\Exception;

/**
 * Represents a single frame in a stack trace.
 */
class Frame
{

    /**
     * @param string|null $file     The file where the frame occurred.
     * @param int|null    $line     The line number in the file.
     * @param string      $function The function name.
     * @param string      $class    The class name (if applicable).
     * @param string      $type     The type of call (e.g., '::' or '->').
     * @param array       $args     The arguments passed to the function.
     */
    public function __construct(
        public ?string $file = null,
        public ?int $line = null,
        public string $function = '',
        public string $class = '',
        public string $type = '',
        public array $args = [],
    ) {
    }

    /**
     * Checks if the frame is internal (i.e., has no file info).
     *
     * @return bool
     */
    public function isInternal(): bool
    {
        return !$this->file;
    }

    /**
     * Checks if the frame originates from the application (not vendor).
     *
     * @return bool
     */
    public function isApp(): bool
    {
        if ($this->isInternal()) {
            return false;
        }
        return strpos($this->file, '/vendor/') === false;
    }

    /**
     * Checks if the frame originates from a vendor package.
     *
     * @return bool
     */
    public function isVendor(): bool
    {
        if ($this->isInternal()) {
            return false;
        }
        return strpos($this->file, '/vendor/') !== false;
    }

    /**
     * Returns the base name of the file, or a placeholder if not set.
     *
     * @return string
     */
    public function getFileLabel(): string
    {
        return $this->file ? basename($this->file) : 'Core PHP/Internal Function';
    }

    /**
     * Returns the full path (or empty string) — useful for tooltips, logs, etc.
     *
     * @return string
     */
    public function getFilePath(): string
    {
        return $this->file ?? '';
    }

    /**
     * Returns a plain-text description of the line info.
     *
     * @return string e.g. "at line 9"
     */
    public function getLineText(): string
    {
        return $this->line !== null ? "at line {$this->line}" : "";
    }


    /**
     * Returns a human-readable description of the frame's context.
     *
     * @return string
     */
    public function getContext(): string
    {
        if ($this->function === '{main}') {
            return '{main}';
        } elseif ($this->function === '{closure}') {
            return '{closure}';
        } elseif ($this->class && $this->function) {
            return "{$this->class}{$this->type}{$this->function}()";
        } elseif ($this->function) {
            return "{$this->function}()";
        } elseif ($this->class) {
            return $this->class;
        }

        return '{unknown}';
    }

    /**
     * Returns a plain-text code excerpt around the frame's line.
     *
     * @param int $padding Number of lines before and after the target line.
     * @return string|null
     */

    public function getExcerpt(int $padding = 7): ?string
    {
        if (!$this->file || !is_readable($this->file)) {
            return null;
        }

        $lines = @file($this->file, FILE_IGNORE_NEW_LINES);
        if (!$lines) {
            return '';
        }

        $start = max(0, $this->line - $padding - 1);
        $end = min(count($lines), $this->line + $padding);
        $excerpt = array_slice($lines, $start, $end - $start, true);

        return implode(PHP_EOL, $excerpt);
    }

    /**
     * Creates a Frame instance from a backtrace array frame.
     *
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['file'] ?? null,
            $data['line'] ?? null,
            $data['function'] ?? '',
            $data['class'] ?? '',
            $data['type'] ?? '',
            $data['args'] ?? []
        );
    }


}