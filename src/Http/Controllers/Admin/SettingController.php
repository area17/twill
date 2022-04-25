<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\SettingRepository;
use Illuminate\Config\Repository as Config;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Routing\UrlGenerator;
use Illuminate\View\Factory as ViewFactory;

class SettingController extends Controller
{
    public function __construct(
        protected Config $config,
        protected SettingRepository $settings,
        protected Redirector $redirector,
        protected UrlGenerator $urlGenerator,
        protected ViewFactory $viewFactory
    ) {
        parent::__construct();
        $this->middleware('can:edit-settings');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(string $section): \Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
    {
        return $this->viewFactory->exists('twill.settings.' . $section)
        ? $this->viewFactory->make('twill.settings.' . $section, [
            'customForm' => true,
            'editableTitle' => false,
            'customTitle' => ucfirst($section) . ' settings',
            'section' => $section,
            'form_fields' => $this->settings->getFormFields($section),
            'saveUrl' => $this->urlGenerator->route('twill.settings.update', $section),
            'translate' => true,
        ])
        : $this->redirector->back();
    }

    /**
     * @param mixed $section
     */
    public function update($section, Request $request): \Illuminate\Http\RedirectResponse
    {
        if (array_key_exists('cancel', $request->all())) {
            return $this->redirector->back();
        }

        $this->settings->saveAll($request->except('_token'), $section);

        fireCmsEvent('cms-settings.saved');

        return $this->redirector->back();
    }
}
