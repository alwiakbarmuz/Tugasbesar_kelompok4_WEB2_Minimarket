<?php

namespace App\View\Components\Form;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class NumberInput extends Component
{
    public $name;
    public $id;
    public $label;
    public $min;
    public $max;
    public $step;
    public $required;

    public function __construct($name, $id = null, $label = null, $min = 0, $max = null, $step = 1, $required = false)
    {
        $this->name = $name;
        $this->id = $id ?? $name;
        $this->label = $label;
        $this->min = $min;
        $this->max = $max;
        $this->step = $step;
        $this->required = $required;
    }

    public function render(): View|Closure|string
    {
        return view('components.form.number-input');
    }
}
