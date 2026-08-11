<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Tour;
use App\Models\FeatureBlock;
use App\Models\FeatureBlocksSectionTranslation;
use App\Models\FeatureBlocksSectionSetting;
use App\Services\GooglePlaceReviewsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $tours = Tour::query()
            ->where('status', 'published')
            ->whereHas('images')
            ->with(['categories', 'category', 'images', 'primaryImage', 'translations', 'pricings', 'promotions'])
            ->orderByDesc('is_featured')
            ->orderByDesc('is_bestseller')
            ->orderByDesc('avg_rating')
            ->orderByDesc('views_count')
            ->take(6)
            ->get();

        if ($tours->count() < 6) {
            $existingIds = $tours->pluck('id');
            $extra = Tour::query()
                ->where('status', 'published')
                ->whereHas('images')
                ->whereNotIn('id', $existingIds)
                ->with(['categories', 'category', 'images', 'primaryImage', 'translations', 'pricings', 'promotions'])
                ->latest()
                ->take(6 - $tours->count())
                ->get();

            $tours = $tours->concat($extra);
        }
        
        $categories = Category::withCount('tours')
            ->with(['translations', 'images'])
            ->orderBy('name')
            ->get();
        
        // Popular cities based on number of published tours
        $popularCities = Tour::where('status', 'published')
            ->select('location', DB::raw('COUNT(*) as tours_count'))
            ->groupBy('location')
            ->orderByDesc('tours_count')
            ->take(8)
            ->get();
        
        // Feature blocks
        $featureBlocks = FeatureBlock::active()->with('translations')->take(4)->get();
        
        // Section translations
        $currentLocale = app()->getLocale();
        $sectionTranslation = FeatureBlocksSectionTranslation::getForLocale($currentLocale);
        $sectionTitle = $sectionTranslation?->title ?? '';
        $sectionDescription = $sectionTranslation?->description ?? '';
        
        // Section settings (container background color)
        $sectionSettings = FeatureBlocksSectionSetting::getSettings();
        
        // Generate popular keywords/tags from tours
        $popularKeywords = $this->getPopularKeywords();

        $googlePlaceData = null;
        $placeId = site_setting('reviews_home_place_id', '');
        if (is_string($placeId) && trim($placeId) !== '') {
            $googlePlaceData = app(GooglePlaceReviewsService::class)->fetch($placeId, $currentLocale);
        }

        return view('frontend.home', compact(
            'tours',
            'categories',
            'popularCities',
            'featureBlocks',
            'sectionTitle',
            'sectionDescription',
            'sectionSettings',
            'popularKeywords',
            'googlePlaceData'
        ));
    }

    public function category($slugOrId)
    {
        if (is_numeric($slugOrId)) {
            $category = Category::with('translations')->findOrFail($slugOrId);
        } else {
            $category = Category::with('translations')->where('slug', $slugOrId)->firstOrFail();
        }
        
        $tours = Tour::where('status', 'published')
            ->where(function ($query) use ($category) {
                $query->where('category_id', $category->id)
                    ->orWhereHas('categories', function ($q) use ($category) {
                        $q->where('categories.id', $category->id);
                    });
            })
            ->with(['images', 'primaryImage', 'translations', 'pricings', 'promotions'])
            ->latest()
            ->paginate(12);
        
        return view('frontend.category', compact('category', 'tours'));
    }

    /**
     * Get popular keywords/tags from tours
     * Combines locations, categories, and meta_keywords
     */
    private function getPopularKeywords($limit = 30)
    {
        $keywords = collect();
        
        // Get keywords from locations (with count)
        $locationKeywords = Tour::where('status', 'published')
            ->select('location', DB::raw('COUNT(*) as count'))
            ->whereNotNull('location')
            ->where('location', '!=', '')
            ->groupBy('location')
            ->orderByDesc('count')
            ->take(15)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->location,
                    'type' => 'location',
                    'count' => $item->count,
                    'url' => route('tours.location', urlencode($item->location))
                ];
            });
        
        $keywords = $keywords->merge($locationKeywords);
        
        // Get keywords from categories (with count)
        $categoryKeywords = Category::withCount('tours')
            ->whereHas('tours', function ($query) {
                $query->where('status', 'published');
            })
            ->orderByDesc('tours_count')
            ->take(10)
            ->get()
            ->map(function ($category) {
                return [
                    'name' => translate_model($category, 'name'),
                    'type' => 'category',
                    'count' => $category->tours_count,
                    'url' => route('category.show', $category->url_key)
                ];
            });
        
        $keywords = $keywords->merge($categoryKeywords);
        
        // Get keywords from meta_keywords field (if exists)
        $metaKeywords = Tour::where('status', 'published')
            ->whereNotNull('meta_keywords')
            ->where('meta_keywords', '!=', '')
            ->pluck('meta_keywords')
            ->flatMap(function ($keywordsString) {
                return explode(',', $keywordsString);
            })
            ->map(function ($keyword) {
                return trim($keyword);
            })
            ->filter(function ($keyword) {
                return !empty($keyword) && strlen($keyword) > 2;
            })
            ->countBy()
            ->sortDesc()
            ->take(10)
            ->map(function ($count, $keyword) {
                return [
                    'name' => $keyword,
                    'type' => 'keyword',
                    'count' => $count,
                    'url' => route('tours.location', urlencode($keyword))
                ];
            })
            ->values();
        
        $keywords = $keywords->merge($metaKeywords);
        
        // Remove duplicates, sort by count, and limit
        $uniqueKeywords = $keywords->unique('name')
            ->sortByDesc('count')
            ->take($limit)
            ->values();
        
        return $uniqueKeywords;
    }
}
