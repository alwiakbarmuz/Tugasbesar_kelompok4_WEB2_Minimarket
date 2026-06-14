<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Select extends Component
{
    public $name;
    public $id;
    public $label;
    public $options;
    public $selected;
    public $required;

    public function __construct($name, $id = null, $label = null, $options = [], $selected = null, $required = false)
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->label = $label;
        $this->options = $options;
        $this->selected = $selected;
        $this->required = $required;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.select');
    }
}
