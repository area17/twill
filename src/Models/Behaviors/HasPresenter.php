<?php

namespace A17\Twill\Models\Behaviors;

trait HasPresenter
{
    protected $presenterInstance;

    /**
     * @param string $presenter
     * @return object
     */
    public function present($presenter = 'presenter')
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
     * @param string $presenter
     * @param string $presenterProperty
     * @return $this
     */
    public function setPresenter($presenter, $presenterProperty = 'presenter')
    {
        if (!$this->$presenterProperty) {
            $this->$presenterProperty = $presenter;
        }

        return $this;
    }

    /**
     * @param string $presenter
     * @return $this
     */
    public function setPresenterAdmin($presenter)
    {
        return $this->setPresenter($presenter, 'presenterAdmin');
    }
}
