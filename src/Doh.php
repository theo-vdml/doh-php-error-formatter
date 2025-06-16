<?php

namespace DohFormatting\Doh;

use DohFormatting\Doh\Exception\Frame;
use DohFormatting\Doh\Exception\Trace;
use DohFormatting\Doh\Formatters\HtmlFormatter;
use DohFormatting\Doh\Formatters\JsonApiFormatter;
use DohFormatting\Doh\Formatters\JsonFormatter;
use DohFormatting\Doh\Formatters\PlainTextFormatter;
use DohFormatting\Doh\Formatters\XmlFormatter;

/**
 * Handles and formats exceptions into various output types (HTML, JSON, etc.)
 */
class Doh
{
    /**
     * @param \Throwable $throwable The throwable to handle
     */
    public function __construct(
        private \Throwable $throwable
    ) {
    }

    /**
     * Builds a Trace instance from the throwable's backtrace,
     * prepending a virtual frame that represents the exact location
     * where the exception was thrown.
     *
     * PHP's Exception::getTrace() does not include the frame corresponding
     * to the throw location itself; it starts from the caller.
     * This method adds a "virtual" frame to represent that missing top frame.
     *
     * @return Trace
     */
    public function buildTrace(): Trace
    {
        // Create a virtual frame representing the exact throw location.
        // This frame is not included in the original backtrace returned by getTrace().
        // It contains the file and line where the exception was thrown,
        // as well as the class name of the exception.
        $throwFrame = Frame::fromArray([
            'file' => $this->throwable->getFile(),
            'line' => $this->throwable->getLine(),
            'class' => get_class($this->throwable),
            // 'function', 'type', and 'args' are intentionally left empty
            // because the throw location itself does not have these details.
        ]);

        // Build the Trace by combining the throwable's backtrace with the virtual throw frame.
        // The virtual frame is prepended to represent the initial exception throw site.
        return Trace::fromBacktrace($this->throwable->getTrace(), [$throwFrame]);
    }

    /**
     * Returns the underlying throwable instance.
     *
     * @return \Throwable The original throwable (exception or error).
     */
    public function getThrowable(): \Throwable
    {
        return $this->throwable;
    }

    /**
     * Returns the class name of the throwable.
     *
     * @return string The fully qualified class name of the throwable.
     */
    public function getClass()
    {
        return get_class($this->throwable);
    }

    /**
     * Returns the message of the throwable.
     *
     * @return string The error or exception message.
     */
    public function getMessage()
    {
        return $this->throwable->getMessage();
    }

    /**
     * Renders the throwable as a fully formatted HTML error page.
     *
     * This includes syntax-highlighted code excerpts, stack trace frames,
     * and detailed information suitable for displaying in a browser.
     *
     * @return string The complete HTML markup representing the error.
     */
    public function toHtml(): string
    {
        $Formatter = new HtmlFormatter();
        return $Formatter->output($this);
    }

    /**
     * Produces a JSON string representing the throwable.
     *
     * The JSON includes error message, class, and a detailed trace array,
     * making it suitable for APIs or structured logging.
     *
     * @return string JSON encoded error information.
     */
    public function toJson(): string
    {
        $Formatter = new JsonFormatter();
        return $Formatter->output($this);
    }

    /**
     * Generates a JSON:API compliant error document for the throwable.
     *
     * This structured format follows the JSON:API specification,
     * providing standardized error keys such as status, code, title,
     * and trace metadata for interoperable API responses.
     *
     * @return string JSON:API formatted error response as a JSON string.
     */
    public function toJsonApi(): string
    {
        $Formatter = new JsonApiFormatter();
        return $Formatter->output($this);
    }

    /**
     * Returns a plain-text formatted representation of the throwable and its stack trace.
     *
     * Useful for CLI output, logs, or debugging in environments without rich formatting.
     *
     * @return string Human-readable plain text describing the error and stack trace.
     */
    public function toPlainText(): string
    {
        $Formatter = new PlainTextFormatter();
        return $Formatter->output($this);
    }

    /**
     * Returns an XML representation of the throwable and its trace.
     *
     * The XML includes status, error code (class name), message title,
     * file and line details, and a trace element with stack frames.
     *
     * @return string Well-formed, pretty-printed XML string describing the error.
     */
    public function toXml(): string
    {
        $Formatter = new XmlFormatter();
        return $Formatter->output($this);
    }

}