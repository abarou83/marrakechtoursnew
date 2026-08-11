<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TranslatedText extends Component
{
    public $model;
    public $attribute;
    public $fallback;

    /**
     * Create a new component instance.
     */
    public function __construct($model, $attribute, $fallback = '')
    {
        $this->model = $model;
        $this->attribute = $attribute;
        $this->fallback = $fallback;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $translation = $this->model->translate();
        $text = $translation ? $translation->{$this->attribute} : ($this->model->{$this->attribute} ?? $this->fallback);
        
        return $text;
    }
}
