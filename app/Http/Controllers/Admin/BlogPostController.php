<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogPostTranslation;
use App\Services\BlogPostJsonImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BlogPostController extends Controller
{
    public function index()
    {
        $posts = BlogPost::with('translations')
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.blog-posts.index', compact('posts'));
    }

    public function create()
    {
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();

        return view('admin.blog-posts.create', compact('availableLocales', 'locales'));
    }

    public function store(Request $request)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();

        $validated = $request->validate([
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:4096',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.excerpt' => 'nullable|string|max:500',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
        ]);

        $this->validateUniqueSlugs($request);

        $featuredImage = $request->hasFile('featured_image')
            ? $request->file('featured_image')->store('blog', 'public')
            : null;

        $post = BlogPost::create([
            'is_active' => $request->boolean('is_active', true),
            'published_at' => $validated['published_at'] ?? now(),
            'author' => $validated['author'] ?? null,
            'featured_image' => $featuredImage,
        ]);

        $this->syncTranslations($post, $validated['translations']);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Article de blog ajouté avec succès.');
    }

    public function edit(BlogPost $blogPost)
    {
        $blogPost->load('translations');
        $availableLocales = \App\Models\Language::active()->pluck('code')->toArray();
        $locales = \App\Helpers\LanguageHelper::getAvailableLocales();

        return view('admin.blog-posts.edit', compact('blogPost', 'availableLocales', 'locales'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();

        $validated = $request->validate([
            'is_active' => 'boolean',
            'published_at' => 'nullable|date',
            'author' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:4096',
            'remove_featured_image' => 'boolean',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.slug' => 'required|string|max:255',
            'translations.*.title' => 'required|string|max:255',
            'translations.*.excerpt' => 'nullable|string|max:500',
            'translations.*.content' => 'required|string',
            'translations.*.meta_title' => 'nullable|string|max:255',
            'translations.*.meta_description' => 'nullable|string|max:500',
        ]);

        $this->validateUniqueSlugs($request, $blogPost->id);

        $featuredImage = $blogPost->featured_image;

        if ($request->boolean('remove_featured_image') && $featuredImage) {
            Storage::disk('public')->delete($featuredImage);
            $featuredImage = null;
        }

        if ($request->hasFile('featured_image')) {
            if ($featuredImage) {
                Storage::disk('public')->delete($featuredImage);
            }
            $featuredImage = $request->file('featured_image')->store('blog', 'public');
        }

        $blogPost->update([
            'is_active' => $request->boolean('is_active', true),
            'published_at' => $validated['published_at'] ?? $blogPost->published_at,
            'author' => $validated['author'] ?? null,
            'featured_image' => $featuredImage,
        ]);

        $this->syncTranslations($blogPost, $validated['translations']);

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Article de blog mis à jour avec succès.');
    }

    public function destroy(BlogPost $blogPost)
    {
        if ($blogPost->featured_image) {
            Storage::disk('public')->delete($blogPost->featured_image);
        }

        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')
            ->with('success', 'Article de blog supprimé avec succès.');
    }

    public function downloadExample(): BinaryFileResponse
    {
        $path = database_path('data/example-blog-posts.multilingual.json');

        if (!is_file($path)) {
            abort(404, 'Fichier exemple introuvable.');
        }

        return response()->download($path, 'example-blog-posts.multilingual.json', [
            'Content-Type' => 'application/json',
        ]);
    }

    public function importExample(BlogPostJsonImportService $importService)
    {
        $path = database_path('data/example-blog-posts.multilingual.json');

        if (!is_file($path)) {
            return back()->withErrors(['import' => 'Fichier exemple introuvable: database/data/example-blog-posts.multilingual.json']);
        }

        try {
            $payload = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            $stats = $importService->import($payload);

            return redirect()->route('admin.blog-posts.index')
                ->with('success', $this->importSuccessMessage($stats));
        } catch (\Throwable $e) {
            Log::error('Blog import example error: ' . $e->getMessage());

            return back()->withErrors(['import' => "Erreur lors de l'import exemple : {$e->getMessage()}"]);
        }
    }

    public function importJson(Request $request, BlogPostJsonImportService $importService)
    {
        $request->validate([
            'json_file' => ['required', 'file', 'mimes:json,txt', 'max:5120'],
        ]);

        try {
            $contents = file_get_contents($request->file('json_file')->getRealPath());
            $payload = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
            $stats = $importService->import($payload);

            return redirect()->route('admin.blog-posts.index')
                ->with('success', $this->importSuccessMessage($stats));
        } catch (\JsonException $e) {
            return back()->withErrors(['import' => 'JSON invalide : ' . $e->getMessage()]);
        } catch (\Throwable $e) {
            Log::error('Blog import json error: ' . $e->getMessage());

            return back()->withErrors(['import' => "Erreur lors de l'import JSON : {$e->getMessage()}"]);
        }
    }

    private function importSuccessMessage(array $stats): string
    {
        $message = "{$stats['created']} article(s) importé(s).";

        if ($stats['skipped'] > 0) {
            $message .= " {$stats['skipped']} ignoré(s).";
        }

        if (!empty($stats['errors'])) {
            $message .= ' Avertissements : ' . implode(' | ', array_slice($stats['errors'], 0, 3));
        }

        return $message;
    }

    private function syncTranslations(BlogPost $post, array $translations): void
    {
        foreach ($translations as $translation) {
            BlogPostTranslation::updateOrCreate(
                [
                    'blog_post_id' => $post->id,
                    'locale' => $translation['locale'],
                ],
                [
                    'slug' => Str::slug($translation['slug']),
                    'title' => $translation['title'],
                    'excerpt' => $translation['excerpt'] ?? null,
                    'content' => $translation['content'],
                    'meta_title' => $translation['meta_title'] ?? null,
                    'meta_description' => $translation['meta_description'] ?? null,
                ]
            );
        }
    }

    private function validateUniqueSlugs(Request $request, ?int $excludePostId = null): void
    {
        foreach ($request->input('translations', []) as $index => $translation) {
            $locale = $translation['locale'] ?? null;
            $slug = Str::slug($translation['slug'] ?? '');

            if (!$locale || !$slug) {
                continue;
            }

            $exists = BlogPostTranslation::where('locale', $locale)
                ->where('slug', $slug)
                ->when($excludePostId, fn ($q) => $q->where('blog_post_id', '!=', $excludePostId))
                ->exists();

            if ($exists) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    "translations.{$index}.slug" => "Ce slug est déjà utilisé pour la langue {$locale}.",
                ]);
            }
        }
    }
}
