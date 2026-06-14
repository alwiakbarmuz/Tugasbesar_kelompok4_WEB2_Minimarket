<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Alert extends Component
{
    public $type;
    public $message;
    public $dismissible;

    public function __construct($type = 'info', $message = null, $dismissible = false)
    {
        $this->type = $type;
        $this->message = $message;
        $this->dismissible = $dismissible;
    }

    public function render(): View|Closure|string
    {
        return view('components.alert');
    }
}
