<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Allow-list HTML sanitiser for rich text coming from the block editor.
 */
class HtmlSanitizer
{
    private const TAGS = [
        'p' => [], 'br' => [], 'h2' => [], 'h3' => [], 'h4' => [],
        'strong' => [], 'b' => [], 'em' => [], 'i' => [], 'u' => [], 's' => [], 'mark' => [],
        'a' => ['href', 'target', 'rel'], 'ul' => [], 'ol' => [], 'li' => [], 'blockquote' => [], 'span' => [],
    ];

    public static function clean(?string $html): string
    {
        $html = trim((string) $html);

        if ($html === '' || $html === '<p></p>') {
            return '';
        }

        $doc = new DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $doc->loadHTML('<?xml encoding="UTF-8"><div id="root">'.$html.'</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $root = $doc->getElementById('root');
        if (! $root) {
            return e(strip_tags($html));
        }

        static::walk($root);

        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $doc->saveHTML($child);
        }

        return trim($out);
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (! array_key_exists($tag, self::TAGS)) {
                    if (in_array($tag, ['script', 'style', 'iframe', 'object', 'embed', 'template'], true)) {
                        $node->removeChild($child);
                        continue;
                    }
                    // Unwrap unknown elements but keep their text.
                    static::walk($child);
                    while ($child->firstChild) {
                        $node->insertBefore($child->firstChild, $child);
                    }
                    $node->removeChild($child);
                    continue;
                }

                static::cleanAttributes($child, self::TAGS[$tag]);
                static::walk($child);
            } elseif ($child->nodeType === XML_COMMENT_NODE) {
                $node->removeChild($child);
            }
        }
    }

    private static function cleanAttributes(DOMElement $el, array $allowed): void
    {
        foreach (iterator_to_array($el->attributes) as $attr) {
            $name = strtolower($attr->name);

            if ($name === 'style') {
                // Only text alignment survives.
                if (preg_match('/text-align:\s*(left|right|center|justify|start|end)/i', $attr->value, $m)) {
                    $el->setAttribute('style', 'text-align: '.strtolower($m[1]));
                } else {
                    $el->removeAttribute('style');
                }
                continue;
            }

            if (! in_array($name, $allowed, true)) {
                $el->removeAttribute($attr->name);
            }
        }

        if ($el->tagName === 'a') {
            $href = trim($el->getAttribute('href'));
            if (! preg_match('#^(https?://|mailto:|tel:|/|\#)#i', $href)) {
                $el->removeAttribute('href');
            }
            if ($el->getAttribute('target') === '_blank') {
                $el->setAttribute('rel', 'noopener noreferrer');
            } else {
                $el->removeAttribute('target');
            }
        }
    }
}
