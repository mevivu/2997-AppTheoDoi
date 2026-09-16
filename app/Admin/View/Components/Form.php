<?php

namespace App\Admin\View\Components;

use Illuminate\View\Component;

class Form extends Component
{
    /**
     * The form type.
     *
     * @var array
     */
    public $type;
    /**
     * The form position.
     *
     * @var string
     */
    public $action;
    /**
     * The form form.
     *
     * @var boolean
     */
    public $validate;
    /**
     * Whether form has file upload.
     *
     * @var boolean
     */
    public $hasFile;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($action = '', $type = 'GET', $validate = false, $hasFile = false)
    {
        //
        $this->type = strtoupper($type);
        $this->action = $action;
        $this->validate = $validate;
        $this->hasFile = filter_var($hasFile, FILTER_VALIDATE_BOOLEAN);
    }
    public function isValidate(){
        return $this->validate === true ? 'data-parsley-validate' : '';
    }
    public function marcoMethod(){
        return ($this->type != 'POST' && $this->type != 'GET') ? 'POST' : $this->type;
    }
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.form');
    }
}