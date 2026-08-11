<?php

declare(strict_types=1);

namespace App\Helpers;

class ImageHelper
{
    public static function lazyLoad(string $src, string $alt = '', array $attributes = []): string
    {
        $placeholder = self::placeholder();
        
        $attrs = array_merge([
            'loading' => 'lazy',
            'decoding' => 'async',
        ], $attributes);

        $attrString = self::buildAttributeString($attrs);

        return sprintf(
            '<img src="%s" data-src="%s" alt="%s" %s>',
            $placeholder,
            htmlspecialchars($src),
            htmlspecialchars($alt),
            $attrString
        );
    }

    public static function responsive(
        string $src,
        string $alt = '',
        array $sizes = [],
        array $attributes = []
    ): string {
        $defaultSizes = [
            '320w' => 320,
            '640w' => 640,
            '768w' => 768,
            '1024w' => 1024,
            '1280w' => 1280,
        ];

        $sizes = $sizes ?: $defaultSizes;
        $srcset = [];

        foreach ($sizes as $descriptor => $width) {
            $resizedUrl = self::getResizedUrl($src, $width);
            $srcset[] = "{$resizedUrl} {$descriptor}";
        }

        $attrs = array_merge([
            'loading' => 'lazy',
            'decoding' => 'async',
            'srcset' => implode(', ', $srcset),
            'sizes' => '(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw',
        ], $attributes);

        $attrString = self::buildAttributeString($attrs);

        return sprintf(
            '<img src="%s" alt="%s" %s>',
            htmlspecialchars($src),
            htmlspecialchars($alt),
            $attrString
        );
    }

    public static function picture(
        string $src,
        string $alt = '',
        array $attributes = []
    ): string {
        $webpSrc = self::toWebP($src);
        $avifSrc = self::toAvif($src);

        $attrs = array_merge([
            'loading' => 'lazy',
            'decoding' => 'async',
        ], $attributes);

        $attrString = self::buildAttributeString($attrs);

        $html = '<picture>';

        if ($avifSrc) {
            $html .= sprintf('<source srcset="%s" type="image/avif">', htmlspecialchars($avifSrc));
        }

        if ($webpSrc) {
            $html .= sprintf('<source srcset="%s" type="image/webp">', htmlspecialchars($webpSrc));
        }

        $html .= sprintf(
            '<img src="%s" alt="%s" %s>',
            htmlspecialchars($src),
            htmlspecialchars($alt),
            $attrString
        );

        $html .= '</picture>';

        return $html;
    }

    public static function placeholder(int $width = 1, int $height = 1): string
    {
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {$width} {$height}'%3E%3C/svg%3E";
    }

    public static function blurhashPlaceholder(string $blurhash, int $width = 32, int $height = 32): string
    {
        return "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 {$width} {$height}'%3E%3Crect fill='%23f0f0f0' width='{$width}' height='{$height}'/%3E%3C/svg%3E";
    }

    protected static function getResizedUrl(string $src, int $width): string
    {
        if (str_contains($src, '/storage/')) {
            $path = parse_url($src, PHP_URL_PATH);
            $extension = pathinfo($path, PATHINFO_EXTENSION);
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $dirname = pathinfo($path, PATHINFO_DIRNAME);

            return "{$dirname}/{$filename}-{$width}w.{$extension}";
        }

        return $src;
    }

    protected static function toWebP(string $src): ?string
    {
        $extension = strtolower(pathinfo($src, PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return preg_replace('/\.(jpg|jpeg|png)$/i', '.webp', $src);
        }

        return null;
    }

    protected static function toAvif(string $src): ?string
    {
        $extension = strtolower(pathinfo($src, PATHINFO_EXTENSION));

        if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
            return preg_replace('/\.(jpg|jpeg|png)$/i', '.avif', $src);
        }

        return null;
    }

    protected static function buildAttributeString(array $attributes): string
    {
        $parts = [];

        foreach ($attributes as $key => $value) {
            if ($value === true) {
                $parts[] = $key;
            } elseif ($value !== false && $value !== null) {
                $parts[] = sprintf('%s="%s"', $key, htmlspecialchars((string) $value));
            }
        }

        return implode(' ', $parts);
    }

    public static function aspectRatio(string $ratio): string
    {
        return match ($ratio) {
            '16:9' => 'aspect-video',
            '4:3' => 'aspect-[4/3]',
            '3:2' => 'aspect-[3/2]',
            '1:1' => 'aspect-square',
            '2:3' => 'aspect-[2/3]',
            '9:16' => 'aspect-[9/16]',
            default => 'aspect-auto',
        };
    }
}
