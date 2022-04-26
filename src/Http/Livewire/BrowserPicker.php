<?php

namespace A17\Twill\Http\Livewire;

use A17\Twill\Facades\TwillCapsules;
use Illuminate\Contracts\View\View;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use LivewireUI\Modal\ModalComponent;

class BrowserPicker extends ModalComponent
{
    use WithPagination;

    public string $title;
    public string $field;
    public string $module;
    public array $endpoints;
    public int $max;
    public Collection $selected;

    // Browser manager.
    public string $search = '';
    public array $except = [];

    protected ?LengthAwarePaginator $options = null;

    public function mount(string $title, string $field, array $endpoints, string $module, int $max, array $currentSelection): void
    {
        $this->title = $title;
        $this->field = $field;
        $this->endpoints = $endpoints;
        $this->module = $module;
        $this->max = $max;
        $this->selected = collect($currentSelection);
    }

    public function submitBrowserData(): void
    {
        $this->emit('newBrowserData', $this->field, $this->selected);
        $this->closeModal();
    }

    public function toggle(array $item): void
    {
        if ($this->isSelected($item)) {
            $this->selected = $this->selected->reject(function (array $element) use($item) {
               return $element['id'] === $item['id'];
            });
        }
        else {
            $this->selected->push($item);
        }
    }

    public function isSelected(array $item): bool {
        return $this->selected->firstWhere('id', $item['id']) !== null;
    }

    /**
     * @return \A17\Twill\Models\Model[]
     */
    private function getOptions(): LengthAwarePaginator
    {
        if (null === $this->options) {
            // @todo: Migrate all from the controller.
            /** @var \A17\Twill\Repositories\ModuleRepository $repository */
            $repository = app($this->getRepositoryClass($this->module));
            // @todo: Filtering
            // @todo: TitleField
            $this->options = $repository->get(forcePagination: true);
        }
        return $this->options;
    }

    /**
     * Gets a simplified array of options to use and pass around.
     */
    private function simpleOptions(): array
    {
        $data = [];
        foreach ($this->getOptions() as $option) {
            $data[$option->id] = [
                'id' => $option->id,
                'name' => $option->title,
                'edit' => 'todo',
                'endpointType' => $option::class,
            ];
        }

        return $data;
    }

    public function render(): View
    {
        return view('twill::livewire.browser-picker', [
            'options' => $this->simpleOptions(),
            'paginator' => $this->getOptions()->links(),
        ]);
    }

    private function getRepositoryClass($model): string
    {
        $namespace = Config::get('twill.namespace');
        $model = Str::ucfirst(Str::singular($model));
        if (@class_exists($class = "$namespace\Repositories\\{$model}Repository")) {
            return $class;
        }

        return TwillCapsules::getCapsuleForModel($model)->getRepositoryClass();
    }
}
