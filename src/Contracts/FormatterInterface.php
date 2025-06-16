<?php

namespace DohFormatting\Doh\Contracts;

use DohFormatting\Doh\Doh;

/**
 * Defines the contract for classes that format and output
 * a Doh instance (an error wrapper) into a specific representation.
 */
interface FormatterInterface
{
    /**
     * Generates a formatted string representation of the given Doh.
     *
     * This method takes a Doh instance (which encapsulates a Throwable and
     * its trace) and returns a string output, such as HTML, JSON, or plain text.
     *
     * @param Doh $doh The error wrapper instance to format and output.
     * @return string The formatted output string.
     */
    public function output(Doh $doh): string;
}
