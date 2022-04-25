<?php

namespace A17\Twill\Models\Behaviors;

trait HasPresenter
{
    protected $presenterInstance;

    /**
     * @return object
     */
    public function present(string $presenter = 'presenter')
    {
        if (!$this->$presenter || !class_exists($this->$presenter)) {
            throw new \Exception('Please set the Presenter path to your Presenter :' . $presenter . ' FQN');
        }

        if (!$this->presenterInstance) {
            $this->presenterInstance = new $this->$presenter($this);
        }

        return $this->presenterInstance;
    }

    /**
     * @return object
     */
    public function presentAdmin()
    {
        return $this->present('presenterAdmin');
    }

    /**
     * @return $this
     */
    public function setPresenter(string $presenter, string $presenterProperty = 'presenter')
    {
        if (!$this->$presenterProperty) {
            $this->$presenterProperty = $presenter;
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function setPresenterAdmin(string $presenter)
    {
        return $this->setPresenter($presenter, 'presenterAdmin');
    }
}
