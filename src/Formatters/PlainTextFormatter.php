<?php

namespace DohFormatting\Doh\Formatters;

use DohFormatting\Doh\Contracts\FormatterInterface;
use DohFormatting\Doh\Doh;

/**
 * Outputs a Doh instance as a plain text error message with stack trace.
 */
class PlainTextFormatter implements FormatterInterface
{
    /**
     * Converts the Doh throwable to a plain text string.
     *
     * @param Doh $doh
     * @return string Plain text representation of the throwable and trace.
     */
    public function output(Doh $doh): string
    {
        $t = $doh->getThrowable();

        return sprintf(
            "[%s] %s in %s:%d\n\n%s",
            get_class($t),
            $t->getMessage(),
            $t->getFile(),
            $t->getLine(),
            $t->getTraceAsString()
        );
    }
}
