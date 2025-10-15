<?php

namespace App\Support;

final class TextGenerator
{
    /**
     * Рандомно приписывает к $base строки из $modifiers в количестве $modifiersCount и с разделителем $separator
     */
    public static function decoratedText(string $base, array $modifiers = [], int $modifiersCount = 0, string $separator = '. '): string
    {
        if ($modifiersCount <= 0 || empty($modifiers)) {
            return $base;
        }

        $selected = array_slice(self::shuffleArray($modifiers), 0, $modifiersCount);

        return implode(
            $separator,
            array_merge([$base], $selected)
        );
    }

    /**
     * Собирает текст из частей с разделителем
     */
    public static function paragraphs(array $parts, int $count = 1, string $separator = "\n\n"): string
    {
        if ($count <= 0 || empty($parts)) {
            return '';
        }

        return implode(
            $separator,
            array_slice(self::shuffleArray($parts), 0, $count)
        );
    }

    protected static function shuffleArray(array $items): array
    {
        shuffle($items);
        return $items;
    }
}