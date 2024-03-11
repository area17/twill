<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Facades\TwillBlocks;
use A17\Twill\Repositories\SettingRepository;
use A17\Twill\Services\Forms\Form;
use Illuminate\Config\Repository as Config;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\View;
use Illuminate\View\Factory as ViewFactory;

class SettingController extends Controller
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var SettingRepository
     */
    protected $settings;

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @var UrlGenerator
     */
    protected $urlGenerator;

    /**
     * @var ViewFactory
     */
    protected $viewFactory;

    public function __construct(
        Config $config,
        SettingRepository $settings,
        Redirector $redirector,
        UrlGenerator $urlGenerator,
        ViewFactory $viewFactory
    ) {
        parent::__construct();

        $this->config = $config;
        $this->settings = $settings;
        $this->middleware('can:edit-settings');
        $this->redirector = $redirector;
        $this->urlGenerator = $urlGenerator;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param string $section
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(string $section)
    {
        if (! $this->viewFactory->exists('twill.settings.' . $section)) {
            return $this->redirector->back();
        }

        $formFields = $this->settings->getFormFieldsForSection($section);

        View::share('form', [
            'form_fields' => $formFields,
        ]);

        return $this->viewFactory->make('twill.settings.' . $section, [
            'customForm' => true,
            'editableTitle' => false,
            'customTitle' => ucfirst($section) . ' settings',
            'section' => $section,
            'form_fields' => $formFields,
            'formBuilder' => Form::make(),
            'saveUrl' => $this->urlGenerator->route(config('twill.admin_route_name_prefix') . 'settings.update', $section),
            'translate' => true,
        ]);
    }

    /**
     * @param mixed $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($section, Request $request)
    {
        if (array_key_exists('cancel', $request->all())) {
            return $this->redirector->back();
        }

        $this->settings->saveAll($request->except('_token'), $section);

        fireCmsEvent('cms-settings.saved');

        return $this->redirector->back();
    }

    public function getSubmitOptions(Model $item): ?array
    {
        // Use options from form template
        return null;
    }
}
