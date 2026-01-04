<?php

namespace Haida\PageBuilder\Services;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class HtmlSanitizer
{
    /**
     * @param array<int, string> $allowedTags
     * @param array<string, array<int, string>> $allowedAttributes
     */
    public function __construct(
        private array $allowedTags = [],
        private array $allowedAttributes = [],
    ) {
    }

    public function sanitize(?string $html): string
    {
        if (! $html) {
            return '';
        }

        $document = new DOMDocument();
        libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="utf-8" ?>'.$html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new DOMXPath($document);
        /** @var \DOMNodeList $nodes */
        $nodes = $xpath->query('//*');

        if (! $nodes) {
            return $html;
        }

        foreach ($nodes as $node) {
            if (! $node instanceof DOMElement) {
                continue;
            }

            $tag = strtolower($node->tagName);
            if (! in_array($tag, $this->allowedTags, true)) {
                $this->replaceWithText($node);
                continue;
            }

            $this->sanitizeAttributes($node, $tag);
        }

        return trim($document->saveHTML() ?? '');
    }

    private function sanitizeAttributes(DOMElement $node, string $tag): void
    {
        $allowed = $this->allowedAttributes[$tag] ?? [];

        if ($node->hasAttributes()) {
            $remove = [];
            foreach ($node->attributes as $attribute) {
                $name = strtolower($attribute->name);
                if (str_starts_with($name, 'on') || $name === 'style') {
                    $remove[] = $attribute->name;
                    continue;
                }

                if (! in_array($name, $allowed, true)) {
                    $remove[] = $attribute->name;
                    continue;
                }

                if ($tag === 'a' && $name === 'href' && ! $this->isSafeLink($attribute->value)) {
                    $remove[] = $attribute->name;
                }
            }

            foreach ($remove as $attrName) {
                $node->removeAttribute($attrName);
            }
        }

        if ($tag === 'a' && $node->hasAttribute('target')) {
            $node->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private function isSafeLink(string $value): bool
    {
        $value = trim($value);
        if ($value === '' || str_starts_with($value, '#') || str_starts_with($value, '/')) {
            return true;
        }

        $scheme = parse_url($value, PHP_URL_SCHEME);
        if ($scheme === null) {
            return true;
        }

        return in_array(strtolower($scheme), ['http', 'https', 'mailto', 'tel'], true);
    }

    private function replaceWithText(DOMNode $node): void
    {
        $document = $node->ownerDocument;
        $text = $document?->createTextNode($node->textContent ?? '');
        $parent = $node->parentNode;

        if (! $parent) {
            return;
        }

        if ($text) {
            $parent->insertBefore($text, $node);
        }

        $parent->removeChild($node);
    }
}
