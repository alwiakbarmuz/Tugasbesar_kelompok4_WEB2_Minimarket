<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Textarea extends Component
{
    public $name;
    public $id;
    public $label;
    public $rows;
    public $placeholder;
    public $required;

    public function __construct($name, $id = null, $label = null, $rows = 3, $placeholder = null, $required = false)
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->label = $label;
        $this->rows = $rows;
        $this->placeholder = $placeholder;
        $this->required = $required;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.textarea');
    }
}
