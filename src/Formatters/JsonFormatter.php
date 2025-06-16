<?php

namespace DohFormatting\Doh\Formatters;

use DohFormatting\Doh\Contracts\FormatterInterface;
use DohFormatting\Doh\Doh;

/**
 * Outputs a Doh instance as a JSON string.
 */
class JsonFormatter implements FormatterInterface
{
    /**
     * Converts the Doh throwable to a pretty-printed JSON string.
     *
     * @param Doh $doh
     * @return string JSON representation of the throwable.
     */
    public function output(Doh $doh): string
    {
        $t = $doh->getThrowable();

        $data = [
            'type' => get_class($t),
            'message' => $t->getMessage(),
            'file' => $t->getFile(),
            'line' => $t->getLine(),
            'trace' => explode("\n", $t->getTraceAsString()),
        ];

        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
