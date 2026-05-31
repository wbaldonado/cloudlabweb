<?php

namespace App\Traits;

use DOMDocument;
use DOMElement;
use DOMNode;

trait SanitizesInput
{
    protected array $disallowedTags = [
        'script', 'iframe', 'object', 'embed', 'form', 'input', 'button',
        'textarea', 'select', 'style', 'link', 'meta', 'base', 'svg', 'math',
        'frame', 'frameset', 'applet', 'audio', 'video', 'source', 'track',
    ];

    protected array $allowedTags = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's', 'strike', 'del', 'ins',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'ul', 'ol', 'li', 'blockquote',
        'pre', 'code', 'a', 'img', 'table', 'thead', 'tbody', 'tr', 'th', 'td',
        'hr', 'span', 'div', 'figure', 'figcaption', 'sup', 'sub',
    ];

    /**
     * Sanitize HTML content by removing dangerous tags and attributes.
     */
    public function sanitizeHtml($html)
    {
        if (empty($html)) {
            return $html;
        }

        $previousState = libxml_use_internal_errors(true);

        $dom = new DOMDocument();
        $dom->loadHTML(
            '<?xml encoding="UTF-8">' . mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'),
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );

        $this->removeDisallowedTags($dom);
        $this->sanitizeAttributes($dom);

        libxml_clear_errors();
        libxml_use_internal_errors($previousState);

        $output = $dom->saveHTML();
        return $output !== false ? $output : strip_tags($html);
    }

    protected function removeDisallowedTags(DOMDocument $dom): void
    {
        foreach ($this->disallowedTags as $tag) {
            while ($nodes = $dom->getElementsByTagName($tag)) {
                if ($nodes->length === 0) {
                    break;
                }
                $node = $nodes->item(0);
                $this->unwrapOrRemoveNode($node);
            }
        }

        $all = $dom->getElementsByTagName('*');
        for ($i = $all->length - 1; $i >= 0; $i--) {
            $node = $all->item($i);
            if (!$node instanceof DOMElement) {
                continue;
            }
            if (!in_array(strtolower($node->tagName), $this->allowedTags, true)) {
                $this->unwrapOrRemoveNode($node);
            }
        }
    }

    protected function unwrapOrRemoveNode(DOMNode $node): void
    {
        $parent = $node->parentNode;
        if (!$parent) {
            return;
        }
        while ($node->firstChild) {
            $parent->insertBefore($node->firstChild, $node);
        }
        $parent->removeChild($node);
    }

    protected function sanitizeAttributes(DOMDocument $dom): void
    {
        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//*[@*]');

        foreach ($nodes as $node) {
            if (!$node instanceof DOMElement) {
                continue;
            }

            $attrs = [];
            foreach ($node->attributes as $attr) {
                $attrs[] = $attr->nodeName;
            }

            foreach ($attrs as $name) {
                $lower = strtolower($name);
                if (str_starts_with($lower, 'on')) {
                    $node->removeAttribute($name);
                    continue;
                }

                $value = trim($node->getAttribute($name));
                if (in_array($lower, ['href', 'src', 'xlink:href'], true)) {
                    if ($this->isUnsafeUrl($value)) {
                        $node->removeAttribute($name);
                    }
                    continue;
                }

                if ($lower === 'style') {
                    $node->removeAttribute($name);
                    continue;
                }

                if (!in_array($lower, ['href', 'src', 'alt', 'title', 'class', 'id', 'colspan', 'rowspan', 'width', 'height'], true)) {
                    $node->removeAttribute($name);
                }
            }
        }
    }

    protected function isUnsafeUrl(string $value): bool
    {
        $lower = strtolower(trim($value));
        if ($lower === '' || str_starts_with($lower, '#') || str_starts_with($lower, '/')) {
            return false;
        }
        if (preg_match('#^(https?|mailto):#i', $lower)) {
            return false;
        }
        return true;
    }
}
