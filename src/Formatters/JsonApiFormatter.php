<?php

namespace DohFormatting\Doh\Formatters;

use DohFormatting\Doh\Contracts\FormatterInterface;
use DohFormatting\Doh\Doh;

/**
 * Outputs a Doh instance as a JSON:API compliant error document.
 */
class JsonApiFormatter implements FormatterInterface
{
    /**
     * Converts the Doh throwable to a JSON:API error response string.
     *
     * @param Doh $doh
     * @return string JSON:API compliant error object.
     */
    public function output(Doh $doh): string
    {
        $t = $doh->getThrowable();

        $error = [
            'errors' => [
                [
                    'status' => '500',
                    'code' => get_class($t),
                    'title' => $t->getMessage(),
                    'detail' => sprintf('%s:%d', $t->getFile(), $t->getLine()),
                    'meta' => [
                        'trace' => explode("\n", $t->getTraceAsString()),
                    ],
                ]
            ]
        ];

        return json_encode($error, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
