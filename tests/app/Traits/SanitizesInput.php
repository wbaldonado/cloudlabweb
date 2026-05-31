<?php

namespace App\Traits;

use DOMDocument;

trait SanitizesInput
{
    /**
     * Sanitize HTML content by removing dangerous tags and attributes.
     *
     * @param string $html
     * @return string
     */
    public function sanitizeHtml($html)
    {
        if (empty($html)) {
            return $html;
        }

        // Suppress warnings for malformed HTML
        $previousState = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        // Load HTML with UTF-8 encoding hack
        $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $scripts = $dom->getElementsByTagName('script');
        while ($scripts->length > 0) {
            $scripts->item(0)->parentNode->removeChild($scripts->item(0));
        }

        $iframes = $dom->getElementsByTagName('iframe');
        while ($iframes->length > 0) {
            $iframes->item(0)->parentNode->removeChild($iframes->item(0));
        }
        
        $objects = $dom->getElementsByTagName('object');
        while ($objects->length > 0) {
            $objects->item(0)->parentNode->removeChild($objects->item(0));
        }

        $embeds = $dom->getElementsByTagName('embed');
        while ($embeds->length > 0) {
            $embeds->item(0)->parentNode->removeChild($embeds->item(0));
        }

        // Remove dangerous attributes
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//@*[starts-with(name(), "on")]');
        foreach ($nodes as $node) {
            $node->ownerElement->removeAttribute($node->nodeName);
        }
        
        // Remove javascript: links
        $nodes = $xpath->query('//@href[starts-with(., "javascript:")]');
        foreach ($nodes as $node) {
            $node->ownerElement->removeAttribute($node->nodeName);
        }

        // Sanitize style attributes - only allow safe CSS properties
        $nodes = $xpath->query('//@style');
        foreach ($nodes as $node) {
            $style = $node->nodeValue;
            // Allow only safe CSS properties (colors, text-align, etc.)
            $safeProperties = ['color', 'background-color', 'text-align', 'font-weight', 'font-style', 'text-decoration'];
            $sanitizedStyle = [];
            foreach ($safeProperties as $prop) {
                if (preg_match('/\b' . preg_quote($prop, '/') . '\s*:\s*[^;]+/i', $style, $matches)) {
                    $sanitizedStyle[] = $matches[0];
                }
            }
            if (!empty($sanitizedStyle)) {
                $node->ownerElement->setAttribute('style', implode('; ', $sanitizedStyle));
            } else {
                $node->ownerElement->removeAttribute('style');
            }
        }

        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        // Save HTML and strip the wrapper tags added by loadHTML
        return $dom->saveHTML();
    }
}
