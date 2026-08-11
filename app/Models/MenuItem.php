<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = [
        'menu_id',
        'label',
        'link_type',
        'link_url',
        'category_id',
        'page_id',
        'tour_id',
        'parent_id',
        'order',
        'is_active',
        'icon',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function page()
    {
        return $this->belongsTo(Page::class);
    }

    public function tour()
    {
        return $this->belongsTo(Tour::class);
    }

    public static function entityLinkTypes(): array
    {
        return ['category', 'page', 'tour'];
    }

    public static function customLinkTypes(): array
    {
        return ['custom', 'internal', 'external'];
    }

    public function usesEntityLabel(): bool
    {
        return in_array($this->link_type, self::entityLinkTypes(), true);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('order');
    }

    public function activeChildren()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function translations()
    {
        return $this->hasMany(MenuItemTranslation::class);
    }

    public function translate($locale = null)
    {
        $locale = $locale ?: app()->getLocale();
        
        $translation = $this->translations()->where('locale', $locale)->first();
        
        if ($translation) {
            return $translation;
        }
        
        // Fallback to the default locale
        return $this->translations()->where('locale', config('app.fallback_locale'))->first();
    }

    /**
     * Get the URL for this menu item
     */
    public function getUrl()
    {
        switch ($this->link_type) {
            case 'category':
                if ($this->category) {
                    return route('category.show', $this->category->url_key);
                }
                return '#';
            case 'page':
                if ($this->page) {
                    return route('pages.show', $this->page->url_key);
                }
                return '#';
            case 'tour':
                if ($this->tour) {
                    return route('tours.show', $this->tour->url_key);
                }
                return '#';
            case 'external':
                return $this->link_url ?? '#';
            case 'custom':
            case 'internal':
            default:
                // Si c'est un lien interne, vérifier s'il commence par http
                if ($this->link_url && (str_starts_with($this->link_url, 'http://') || str_starts_with($this->link_url, 'https://'))) {
                    return $this->link_url;
                }
                // Sinon, traiter comme une route relative
                return $this->link_url ? url($this->link_url) : '#';
        }
    }

    /**
     * Get the display label (multilingual)
     */
    public function getDisplayLabel($locale = null)
    {
        $locale = $locale ?: app()->getLocale();

        if ($this->link_type === 'category' && $this->category) {
            return translate_model($this->category, 'name', $this->label);
        }

        if ($this->link_type === 'page' && $this->page) {
            $translation = $this->page->translate($locale);
            if ($translation && $translation->title) {
                return $translation->title;
            }
            return translate_model($this->page, 'title', $this->label);
        }

        if ($this->link_type === 'tour' && $this->tour) {
            return translate_model($this->tour, 'title', $this->label);
        }

        $translation = $this->translations()->where('locale', $locale)->first();

        if ($translation && $translation->label) {
            return $translation->label;
        }

        $defaultLocale = config('app.fallback_locale', 'fr');
        if ($locale !== $defaultLocale) {
            $defaultTranslation = $this->translations()->where('locale', $defaultLocale)->first();
            if ($defaultTranslation && $defaultTranslation->label) {
                return $defaultTranslation->label;
            }
        }

        return $this->label;
    }
}
