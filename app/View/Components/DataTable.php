<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class DataTable extends Component
{
    public $headers;
    public $actions;

    public function __construct($headers = [], $actions = false)
    {
        $this->headers = $headers;
        $this->actions = $actions;
    }

    public function render(): View|Closure|string
    {
        return view('components.data-table');
    }
}
