<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatCard extends Component
{
    public $title;
    public $value;
    public $icon;
    public $color;
    public $trend;
    public $trendValue;

    public function __construct($title, $value, $icon, $color = 'blue', $trend = null, $trendValue = null)
    {
        $this->title = $title;
        $this->value = $value;
        $this->icon = $icon;
        $this->color = $color;
        $this->trend = $trend;
        $this->trendValue = $trendValue;
    }

    public function render(): View|Closure|string
    {
        return view('components.stat-card');
    }
}
