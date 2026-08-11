<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Tour;
use App\Models\Category;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;

class SeoService
{
    protected array $locales = ['fr', 'en', 'es', 'ar'];
    protected string $defaultLocale = 'fr';
    protected string $siteName = 'Marrakech Tours';

    /**
     * Generate meta tags for a page (returns array for flexibility)
     */
    public function generateMetaTags(array $data): array
    {
        return [
            'title' => $data['title'] ?? $this->siteName,
            'description' => $this->truncateDescription($data['description'] ?? '', 155),
            'canonical' => $data['canonical'] ?? URL::current(),
            'image' => $data['image'] ?? asset('images/og-default.jpg'),
            'type' => $data['type'] ?? 'website',
            'noindex' => $data['noindex'] ?? false,
            'robots' => $data['robots'] ?? 'index, follow',
            'site_name' => $this->siteName,
            'locale' => app()->getLocale(),
        ];
    }

    /**
     * Render meta tags as HTML
     */
    public function renderMetaTags(array $data): HtmlString
    {
        $meta = $this->generateMetaTags($data);
        $tags = [];

        $tags[] = "<title>{$this->escape($meta['title'])}</title>";
        $tags[] = "<meta name=\"description\" content=\"{$this->escape($meta['description'])}\">";
        $tags[] = "<link rel=\"canonical\" href=\"{$meta['canonical']}\">";

        if ($meta['noindex']) {
            $tags[] = '<meta name="robots" content="noindex, follow">';
        }

        $tags[] = "<meta property=\"og:title\" content=\"{$this->escape($meta['title'])}\">";
        $tags[] = "<meta property=\"og:description\" content=\"{$this->escape($meta['description'])}\">";
        $tags[] = "<meta property=\"og:url\" content=\"{$meta['canonical']}\">";
        $tags[] = "<meta property=\"og:image\" content=\"{$meta['image']}\">";
        $tags[] = "<meta property=\"og:type\" content=\"{$meta['type']}\">";
        $tags[] = "<meta property=\"og:site_name\" content=\"{$meta['site_name']}\">";
        $tags[] = '<meta property="og:locale" content="' . $meta['locale'] . '">';

        $tags[] = '<meta name="twitter:card" content="summary_large_image">';
        $tags[] = "<meta name=\"twitter:title\" content=\"{$this->escape($meta['title'])}\">";
        $tags[] = "<meta name=\"twitter:description\" content=\"{$this->escape($meta['description'])}\">";
        $tags[] = "<meta name=\"twitter:image\" content=\"{$meta['image']}\">";

        return new HtmlString(implode("\n    ", $tags));
    }

    /**
     * Generate hreflang tags for multilingual pages (returns array)
     */
    public function generateHreflangTags(string $routeName, array $routeParams = []): array
    {
        $tags = [];

        foreach ($this->locales as $locale) {
            $params = array_merge($routeParams, ['locale' => $locale]);

            try {
                $url = route($routeName, $params);
                $tags[] = ['hreflang' => $locale, 'href' => $url];
            } catch (\Exception $e) {
                continue;
            }
        }

        $defaultParams = array_merge($routeParams, ['locale' => $this->defaultLocale]);
        try {
            $defaultUrl = route($routeName, $defaultParams);
            $tags[] = ['hreflang' => 'x-default', 'href' => $defaultUrl];
        } catch (\Exception $e) {
        }

        return $tags;
    }

    /**
     * Render hreflang tags as HTML
     */
    public function renderHreflangTags(string $routeName, array $routeParams = []): HtmlString
    {
        $tags = $this->generateHreflangTags($routeName, $routeParams);
        $html = [];

        foreach ($tags as $tag) {
            $html[] = "<link rel=\"alternate\" hreflang=\"{$tag['hreflang']}\" href=\"{$tag['href']}\">";
        }

        return new HtmlString(implode("\n    ", $html));
    }

    /**
     * Generate JSON-LD for a Tour (TouristTrip schema)
     */
    public function generateTourJsonLd(Tour $tour): HtmlString
    {
        $translation = $tour->translate(app()->getLocale()) ?? $tour->translate('fr');

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'TouristTrip',
            'name' => $translation?->title ?? $tour->title,
            'description' => $translation?->description ?? $tour->description,
            'url' => route('tours.show', ['locale' => app()->getLocale(), 'slug' => $translation?->slug ?? $tour->slug]),
            'touristType' => 'Adventure tourism',
            'provider' => $this->getOrganizationSchema(),
        ];

        if ($tour->duration) {
            $schema['duration'] = "PT{$tour->duration}H";
        }

        if ($tour->departure_point) {
            $schema['itinerary'] = [
                '@type' => 'Place',
                'name' => $tour->departure_point,
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Marrakech',
                    'addressCountry' => 'MA',
                ],
            ];

            if ($tour->departure_lat && $tour->departure_lng) {
                $schema['itinerary']['geo'] = [
                    '@type' => 'GeoCoordinates',
                    'latitude' => $tour->departure_lat,
                    'longitude' => $tour->departure_lng,
                ];
            }
        }

        $minPrice = $tour->getMinPrice();
        if ($minPrice) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'priceCurrency' => 'EUR',
                'price' => $minPrice,
                'availability' => 'https://schema.org/InStock',
                'validFrom' => now()->format('Y-m-d'),
            ];
        }

        if ($tour->avg_rating && $tour->reviews_count > 0) {
            $schema['aggregateRating'] = [
                '@type' => 'AggregateRating',
                'ratingValue' => $tour->avg_rating,
                'reviewCount' => $tour->reviews_count,
                'bestRating' => 5,
                'worstRating' => 1,
            ];
        }

        if ($tour->images && count($tour->images) > 0) {
            $schema['image'] = $tour->images->take(5)->pluck('url')->toArray();
        }

        return new HtmlString(
            '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'
        );
    }

    /**
     * Generate JSON-LD for TravelAgency / LocalBusiness (homepage)
     */
    public function generateOrganizationJsonLd(): HtmlString
    {
        $schema = array_merge(
            $this->getOrganizationSchema(),
            [
                '@context' => 'https://schema.org',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => 'Médina de Marrakech',
                    'addressLocality' => 'Marrakech',
                    'postalCode' => '40000',
                    'addressCountry' => 'MA',
                ],
                'geo' => [
                    '@type' => 'GeoCoordinates',
                    'latitude' => 31.6295,
                    'longitude' => -7.9811,
                ],
                'openingHoursSpecification' => [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                    'opens' => '08:00',
                    'closes' => '20:00',
                ],
                'priceRange' => '€€',
                'areaServed' => [
                    '@type' => 'Place',
                    'name' => 'Marrakech, Morocco',
                ],
            ]
        );

        return new HtmlString(
            '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'
        );
    }

    /**
     * Generate JSON-LD schema for FAQ page (returns array)
     */
    public function generateFaqJsonLd(array $faqs): array
    {
        $mainEntity = [];

        foreach ($faqs as $faq) {
            $mainEntity[] = [
                '@type' => 'Question',
                'name' => $faq['question'],
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text' => $faq['answer'],
                ],
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $mainEntity,
        ];
    }

    /**
     * Generate JSON-LD schema for breadcrumbs (returns array)
     */
    public function generateBreadcrumbJsonLd(array $items): array
    {
        $itemListElement = [];

        foreach ($items as $index => $item) {
            $element = [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => $item['title'] ?? $item['name'] ?? '',
            ];
            
            if (!empty($item['url'])) {
                $element['item'] = $item['url'];
            }

            $itemListElement[] = $element;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $itemListElement,
        ];
    }

    /**
     * Render FAQ JSON-LD as HTML script tag
     */
    public function renderFaqJsonLd(array $faqs): HtmlString
    {
        $schema = $this->generateFaqJsonLd($faqs);
        return new HtmlString(
            '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'
        );
    }

    /**
     * Render Breadcrumb JSON-LD as HTML script tag
     */
    public function renderBreadcrumbJsonLd(array $items): HtmlString
    {
        $schema = $this->generateBreadcrumbJsonLd($items);
        return new HtmlString(
            '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'
        );
    }

    /**
     * Generate JSON-LD for a single review
     */
    public function generateReviewJsonLd(array $review, Tour $tour): HtmlString
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Review',
            'itemReviewed' => [
                '@type' => 'TouristTrip',
                'name' => $tour->title,
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $review['rating'],
                'bestRating' => 5,
            ],
            'author' => [
                '@type' => 'Person',
                'name' => $review['author'],
            ],
            'reviewBody' => $review['content'],
            'datePublished' => $review['date'],
        ];

        return new HtmlString(
            '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>'
        );
    }

    /**
     * Get organization schema (reusable)
     */
    protected function getOrganizationSchema(): array
    {
        return [
            '@type' => ['TravelAgency', 'LocalBusiness'],
            'name' => $this->siteName,
            'url' => config('app.url'),
            'logo' => asset('images/logo.png'),
            'telephone' => '+212-XXX-XXXXXX',
            'email' => 'contact@marrakechtours.net',
            'sameAs' => [
                'https://www.facebook.com/marrakechtours',
                'https://www.instagram.com/marrakechtours',
                'https://www.tripadvisor.com/marrakechtours',
            ],
        ];
    }

    /**
     * Escape HTML entities for meta content
     */
    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Truncate text for meta description (140-155 chars)
     */
    public function truncateDescription(string $text, int $maxLength = 155): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        if (strlen($text) <= $maxLength) {
            return $text;
        }

        $truncated = substr($text, 0, $maxLength - 3);
        $lastSpace = strrpos($truncated, ' ');

        if ($lastSpace !== false) {
            $truncated = substr($truncated, 0, $lastSpace);
        }

        return $truncated . '...';
    }
}
