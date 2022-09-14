<?php

namespace A17\Twill\Services\Forms\Fields;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use ReflectionClass;

abstract class BaseFormField
{
    /**
     * @var \A17\Twill\View\Components\Fields\TwillFormComponent
     */
    protected function __construct(
        protected string $component,
        protected ?string $name = null,
        protected ?string $label = null,
        protected ?string $note = null,
        protected ?bool $required = false,
        protected ?bool $disabled = false,
        /**
         * A list of mandatory properties in order of their component
         * constructor.
         */
        protected array $mandatoryProperties = []
    ) {
    }

    abstract public static function make(): static;

    /**
     * Set the name of the field, if no label is set yet, this method will also update that.
     */
    public function name(string $name): static
    {
        $this->name = $name;

        if (! $this->label) {
            $this->label(Str::headline($name));
        }

        return $this;
    }

    /**
     * Set the label of the field, you can use twillTrans('') Laravel translatable strings here.
     */
    public function label(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Add a note to the field to display on the form.
     */
    public function note(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Marks the field as mandatory, however you still need to add validation rules.
     */
    public function required(bool $required = true): self
    {
        $this->required = $required;

        return $this;
    }

    /**
     * Marks the field as disabled.
     *
     * There might be some fields not supporting this.
     */
    public function disabled(bool $disabled = true): self
    {
        $this->disabled = $disabled;

        return $this;
    }

    public function render(): View
    {
        $vars = collect(get_object_vars($this))->except(['component']);

        if ($this->mandatoryProperties !== []) {
            // If the view component has mandatory parameters we construct it
            // slightly different from regular ones.
            // This allows more control.
            $class = new ReflectionClass($this->component);
            $args = [];

            foreach ($this->mandatoryProperties as $property) {
                if (! $this->{$property}) {
                    throw new \InvalidArgumentException(
                        "Missing required field property '$property' on " . $this::class
                    );
                }

                $args[$property] = $this->getValue($property);
            }

            $args += $this->getAdditionalConstructorArguments();

            $component = $class->newInstance(...$args);
        } else {
            /** @var \A17\Twill\View\Components\Fields\TwillFormComponent $component */
            $component = new $this->component();
        }

        foreach ($vars->keys() as $name) {
            $component->{$name} = $this->getValue($name);
        }

        return $component->render();
    }

    /**
     * Gets the property but also allows the class to overwrite it
     * if needed.
     *
     * This can be done by adding `get{}`
     */
    private function getValue(string $property): mixed
    {
        $method = 'get' . Str::studly($property);

        if (method_exists($this, $method)) {
            return $this->$method();
        }

        return $this->$property;
    }

    /**
     * In render we dynamically build the constructor arguments.
     *
     * In exceptional cases such as browser we have more conditionals and
     * we can use this method to set those.
     */
    protected function getAdditionalConstructorArguments(): array
    {
        return [];
    }
}
