<?php

namespace App\Support;

class ArabicSlug
{
    public static function make(string $value): string
    {
        $slug = preg_replace('/[^\p{Arabic}\p{L}\p{N}\s-]+/u', '', trim($value)) ?? '';
        $slug = preg_replace('/[\s_]+/u', '-', $slug) ?? '';
        $slug = preg_replace('/-+/u', '-', $slug) ?? '';

        return trim($slug, '-');
    }
}
