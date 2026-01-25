<?php

namespace JanWennrich\BoardGameGeekApi;

final class Xml
{
    public static function attrString(\SimpleXMLElement $element, string $name): ?string
    {
        $value = (string) $element[$name];
        return $value !== '' ? $value : null;
    }

    public static function attrInt(\SimpleXMLElement $element, string $name): ?int
    {
        $value = self::attrString($element, $name);
        return $value === null ? null : (int) $value;
    }

    public static function attrFloat(\SimpleXMLElement $element, string $name): ?float
    {
        $value = self::attrString($element, $name);
        return $value === null ? null : (float) $value;
    }

    public static function attrBool(\SimpleXMLElement $element, string $name): ?bool
    {
        $value = self::attrString($element, $name);
        if ($value === null) {
            return null;
        }

        $normalized = strtolower($value);
        if ($normalized === 'true' || $normalized === '1') {
            return true;
        }

        if ($normalized === 'false' || $normalized === '0') {
            return false;
        }

        return null;
    }

    public static function childText(?\SimpleXMLElement $element): ?string
    {
        if (!$element instanceof \SimpleXMLElement) {
            return null;
        }

        $value = trim((string) $element);
        return $value !== '' ? $value : null;
    }

    public static function childStringValue(\SimpleXMLElement $element, string $childName): ?string
    {
        $child = $element->{$childName} ?? null;
        if (!($child instanceof \SimpleXMLElement)) {
            return null;
        }

        return self::attrString($child, 'value') ?? self::childText($child);
    }

    public static function childIntValue(\SimpleXMLElement $element, string $childName): ?int
    {
        $value = self::childStringValue($element, $childName);
        return $value === null ? null : (int) $value;
    }

    public static function childFloatValue(\SimpleXMLElement $element, string $childName): ?float
    {
        $value = self::childStringValue($element, $childName);
        return $value === null ? null : (float) $value;
    }

    /**
     * @return \SimpleXMLElement[]
     */
    public static function xpath(\SimpleXMLElement $element, string $path): array
    {
        return $element->xpath($path) ?: [];
    }
}
