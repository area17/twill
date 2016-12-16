<?php

namespace A17\CmsToolkit\Models\Behaviors;

trait HasPresenter
{
    protected $presenterInstance;

    public function present($presenter = 'presenter')
    {
        if (!$this->$presenter or !class_exists($this->$presenter)) {
            throw new \Exception('Please set the Presenter path to your Presenter :' . $presenter . ' FQN');
        }

        if (!$this->presenterInstance) {
            $this->presenterInstance = new $this->$presenter($this);
        }

        return $this->presenterInstance;
    }

    public function presentAdmin()
    {
        return $this->present('presenterAdmin');
    }
}
