<?php

namespace A17\Twill\Http\Livewire;

use A17\Twill\Models\Model;
use A17\Twill\Repositories\ModuleRepository;
use App\Repositories\ProjectRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use Livewire\Component;

class Form extends Component
{
    public Model $model;
    public array $form;
    // @todo: Make dynamic.
    public string $currentLang = 'en';

    private function getRepo(): ModuleRepository
    {
        return app(ProjectRepository::class);
    }

    public function mount(int $itemId = null): void
    {
        if ($itemId) {
            $this->model = $this->getRepo()->getById($itemId);
        } else {
            $this->model = $this->getRepo()->newInstance();
        }

        $this->form = $this->form($this->model);
    }

    public function getRules(): array
    {
        $dummyRules = [];

        $translatedFields = $this->model->getTranslatedAttributes();

        foreach ($this->model->getFillable() as $field) {
            if (in_array($field, $translatedFields, true)) {
                foreach (config('translatable.locales') as $locale) {
                    $dummyRules['cmsFields.' . $field . '.' . $locale] = ['nullable'];
                }
            } else {
                $dummyRules['cmsFields.' . $field] = ['nullable'];
            }
        }

        return $dummyRules;
    }

    private function getForm(): string
    {
        $view = "twill.projects.form";

        \Illuminate\Support\Facades\View::share('twillFormLocales', config('translatable.locales'));
        \Illuminate\Support\Facades\View::share('twillFormCurrentLocale', $this->currentLang);
        \Illuminate\Support\Facades\View::share('livewire', true);
        return \Illuminate\Support\Facades\View::make($view, ['langCodes' => config('translatable.locales')])->render();
    }

    public function save(): void
    {
        $this->getRepo()->update($this->model->id, $this->form);
    }

    public function addRepeater(string $type): void
    {
        $this->form['repeaters'][$type][] = [
            'id' => time(),
        ];
    }

    public function render(): View
    {
        // Get the form.
        \Illuminate\Support\Facades\View::share('repeaters', $this->form['repeaters']);
        return view('twill::livewire.form', [
            'langCodes' => config('translatable.locales'),
            'formView' => $this->getForm(),
        ]);
    }

    /**
     * This method constructs the data model as the saving expects it in the end (repo).
     *
     * Small adjustments were done in HandleRepeaters to optimize how saving is done.
     */
    public function form(Model $item): array
    {
        $data = $this->getRepo()->getFormFields($item);

        foreach ($data['translations'] as $field => $fieldData) {
            $data[$field] = $fieldData;
        }

        $data['languages'] = [];

        foreach (config('translatable.locales') as $locale) {
            $data['languages'][$locale] = [
                'shortlabel' => Str::upper($locale),
                'label' => getLanguageLabelFromLocaleCode($locale),
                'value' => $locale,
                'published' => $data['active'][$locale] === 1 || $data['active'][$locale] === true,
            ];
        }

        $data['parent_id'] = null;
        $data['public'] = false;

        unset($data['translations']);

        // Todo: Repeaters, browsers etc.

        return array_replace_recursive($data, $this->formData());
    }

    private function formData(): array
    {
        // @todo: Request
        return [];
    }

    private function moduleHas(string $what)
    {
        return true;
    }

    private function getRepeaterList(): \Illuminate\Support\Collection
    {
        return collect([]);
    }

}
