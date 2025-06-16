<?php

namespace DohFormatting\Doh\Formatters;

use DohFormatting\Doh\Contracts\FormatterInterface;
use DohFormatting\Doh\Doh;

/**
 * Outputs a Doh instance as an XML formatted error message.
 */
class XmlFormatter implements FormatterInterface
{
    /**
     * Converts the Doh throwable to an XML string representation.
     *
     * @param Doh $doh
     * @return string XML representation of the throwable.
     */
    public function output(Doh $doh): string
    {
        $t = $doh->getThrowable();

        $xml = new \SimpleXMLElement('<error/>');
        $xml->addChild('type', get_class($t));
        $xml->addChild('message', htmlspecialchars($t->getMessage(), ENT_XML1 | ENT_COMPAT, 'UTF-8'));
        $xml->addChild('file', $t->getFile());
        $xml->addChild('line', (string) $t->getLine());

        $traceNode = $xml->addChild('trace');
        $traceLines = explode("\n", $t->getTraceAsString());
        foreach ($traceLines as $line) {
            $traceNode->addChild('frame', htmlspecialchars($line, ENT_XML1 | ENT_COMPAT, 'UTF-8'));
        }

        // Format XML output with indentation for readability
        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;

        return $dom->saveXML();
    }
}
