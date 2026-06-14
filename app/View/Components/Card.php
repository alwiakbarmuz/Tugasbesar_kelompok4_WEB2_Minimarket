<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Card extends Component
{
    public $title;
    public $subtitle;
    public $padding;
    public $shadow;

    public function __construct($title = null, $subtitle = null, $padding = '6', $shadow = 'sm')
    {
        $this->title = $title;
        $this->subtitle = $subtitle;
        $this->padding = $padding;
        $this->shadow = $shadow;
    }

    public function render(): View|Closure|string
    {
        return view('components.card');
    }
}
