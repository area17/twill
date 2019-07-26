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
        $this->redirector = $redirector;
        $this->urlGenerator = $urlGenerator;
        $this->viewFactory = $viewFactory;
    }

    /**
     * @param string $section
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($section)
    {
        return $this->viewFactory->exists('admin.settings.' . $section)
        ? $this->viewFactory->make('admin.settings.' . $section, [
            'customForm' => true,
            'editableTitle' => false,
            'customTitle' => ucfirst($section) . ' settings',
            'section' => $section,
            'form_fields' => $this->settings->getFormFields($section),
            'saveUrl' => $this->urlGenerator->route('admin.settings.update', $section),
            'translate' => true,
        ])
        : $this->redirector->back();
    }

    /**
     * @param mixed $section
     * @param Request $request
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
}
