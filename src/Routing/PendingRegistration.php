<?php

namespace A17\Twill\Routing;

use Illuminate\Routing\RouteCollection;

class PendingRegistration
{
    protected bool $registered = false;

    public function __construct(
        protected string $type,
        protected string $name,
        protected array $options,
        protected TwillRegistrar $registrar,
    ) {}

    /**
     * Set the methods the controller should apply to.
     *
     * @param array|string|dynamic  $methods
     */
    public function only($methods): static
    {
        $this->options['only'] = is_array($methods) ? $methods : func_get_args();

        return $this;
    }

    /**
     * Set the methods the controller should exclude.
     *
     * @param array|string|dynamic  $methods
     */
    public function except($methods): static
    {
        $this->options['except'] = is_array($methods) ? $methods : func_get_args();

        return $this;
    }

    /**
     * Register twill controller routes.
     */
    public function register(): RouteCollection
    {
        $this->registered = true;

        return $this->registrar->{$this->type}($this->name, $this->options);
    }

    /**
     * Handle the object's destruction.
     */
    public function __destruct()
    {
        if (!$this->registered) {
            $this->register();
        }
    }
}
