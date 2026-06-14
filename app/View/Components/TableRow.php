<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableRow extends Component
{
    public $hover;
    public $border;

    public function __construct($hover = true, $border = true)
    {
        $this->hover = $hover;
        $this->border = $border;
    }

    public function render(): View|Closure|string
    {
        return view('components.table-row');
    }
}
