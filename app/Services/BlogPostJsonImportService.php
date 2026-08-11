<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Models\Language;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BlogPostJsonImportService
{
    public function import(array $payload): array
    {
        if (isset($payload['posts']) && is_array($payload['posts'])) {
            $posts = $payload['posts'];
        } elseif (array_is_list($payload)) {
            $posts = $payload;
        } elseif (isset($payload['translations']) || isset($payload['title'])) {
            $posts = [$payload];
        } else {
            throw new \InvalidArgumentException('Le JSON doit contenir une clé "posts" (tableau) ou être un tableau d\'articles.');
        }

        if (empty($posts)) {
            throw new \InvalidArgumentException('Aucun article trouvé dans le fichier JSON.');
        }

        $activeLocales = Language::active()->pluck('code')->toArray();

        $stats = [
            'created' => 0,
            'skipped' => 0,
            'errors' => [],
        ];

        DB::transaction(function () use ($posts, $activeLocales, &$stats) {
            foreach ($posts as $index => $postData) {
                if (!is_array($postData)) {
                    $stats['errors'][] = "Article #{$index} : format invalide.";
                    continue;
                }

                try {
                    $result = $this->importPost($postData, $activeLocales, $index);
                    if ($result === 'created') {
                        $stats['created']++;
                    } else {
                        $stats['skipped']++;
                    }
                } catch (\Throwable $e) {
                    $stats['errors'][] = "Article #{$index} : {$e->getMessage()}";
                }
            }
        });

        return $stats;
    }

    private function importPost(array $postData, array $activeLocales, int $index): string
    {
        $translations = $this->normalizeTranslations($postData, $activeLocales);

        if (empty($translations)) {
            throw new \InvalidArgumentException('aucune traduction valide (slug + title + content requis).');
        }

        foreach ($translations as $translation) {
            if ($this->slugExists($translation['locale'], $translation['slug'])) {
                throw new \InvalidArgumentException(
                    "slug « {$translation['slug']} » déjà utilisé pour la locale {$translation['locale']}."
                );
            }
        }

        $publishedAt = null;
        if (!empty($postData['published_at'])) {
            $publishedAt = Carbon::parse($postData['published_at']);
        }

        $post = BlogPost::create([
            'is_active' => (bool) ($postData['is_active'] ?? true),
            'published_at' => $publishedAt ?? now(),
            'author' => $postData['author'] ?? null,
            'featured_image' => $postData['featured_image'] ?? null,
        ]);

        foreach ($translations as $translation) {
            BlogPostTranslation::create([
                'blog_post_id' => $post->id,
                'locale' => $translation['locale'],
                'slug' => Str::slug($translation['slug']),
                'title' => $translation['title'],
                'excerpt' => $translation['excerpt'] ?? null,
                'content' => $translation['content'],
                'meta_title' => $translation['meta_title'] ?? null,
                'meta_description' => $translation['meta_description'] ?? null,
            ]);
        }

        return 'created';
    }

    private function normalizeTranslations(array $postData, array $activeLocales): array
    {
        $normalized = [];

        if (isset($postData['translations']) && is_array($postData['translations'])) {
            $isAssoc = !array_is_list($postData['translations']);

            if ($isAssoc) {
                foreach ($postData['translations'] as $locale => $data) {
                    if (!in_array($locale, $activeLocales, true) || !is_array($data)) {
                        continue;
                    }
                    $item = $this->buildTranslationRow($locale, $data);
                    if ($item) {
                        $normalized[] = $item;
                    }
                }
            } else {
                foreach ($postData['translations'] as $data) {
                    if (!is_array($data)) {
                        continue;
                    }
                    $locale = $data['locale'] ?? null;
                    if (!$locale || !in_array($locale, $activeLocales, true)) {
                        continue;
                    }
                    $item = $this->buildTranslationRow($locale, $data);
                    if ($item) {
                        $normalized[] = $item;
                    }
                }
            }
        }

        // Fallback: champs à la racine pour une seule locale
        if (empty($normalized) && !empty($postData['title']) && !empty($postData['content'])) {
            $locale = $postData['locale'] ?? config('app.fallback_locale', 'fr');
            if (in_array($locale, $activeLocales, true)) {
                $item = $this->buildTranslationRow($locale, $postData);
                if ($item) {
                    $normalized[] = $item;
                }
            }
        }

        return $normalized;
    }

    private function buildTranslationRow(string $locale, array $data): ?array
    {
        $title = trim($data['title'] ?? '');
        $content = trim($data['content'] ?? '');
        $slug = trim($data['slug'] ?? '');

        if ($title === '' || $content === '') {
            return null;
        }

        if ($slug === '') {
            $slug = Str::slug($title);
        }

        return [
            'locale' => $locale,
            'slug' => $slug,
            'title' => $title,
            'excerpt' => $data['excerpt'] ?? null,
            'content' => $content,
            'meta_title' => $data['meta_title'] ?? null,
            'meta_description' => $data['meta_description'] ?? null,
        ];
    }

    private function slugExists(string $locale, string $slug): bool
    {
        return BlogPostTranslation::where('locale', $locale)
            ->where('slug', Str::slug($slug))
            ->exists();
    }
}
