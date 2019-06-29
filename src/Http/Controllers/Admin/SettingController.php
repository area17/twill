<?php

namespace A17\Twill\Http\Controllers\Admin;

use A17\Twill\Repositories\SettingRepository;
use Illuminate\Routing\Redirector;

class SettingController extends Controller
{
    /**
     * @var SettingRepository
     */
    protected $settings;

    /**
     * @var Redirector
     */
    protected $redirector;

    /**
     * @param SettingRepository $settings
     * @param Redirector $redirector
     */
    public function __construct(SettingRepository $settings, Redirector $redirector)
    {
        parent::__construct();

        $this->settings = $settings;
        $this->redirector = $redirector;
    }

    /**
     * @param string $section
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index($section)
    {
        return view()->exists('admin.settings.' . $section) ? view('admin.settings.' . $section, [
            'customForm' => true,
            'editableTitle' => false,
            'customTitle' => ucfirst($section) . ' settings',
            'section' => $section,
            'form_fields' => $this->settings->getFormFields($section),
            'saveUrl' => route('admin.settings.update', $section),
            'translate' => true,
        ]) : $this->redirector->back();
    }

    /**
     * @param string $section
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($section)
    {
        if (array_key_exists('cancel', request()->all())) {
            return $this->redirector->back();
        }

        $this->settings->saveAll(request()->except('_token'), $section);

        fireCmsEvent('cms-settings.saved');

        return $this->redirector->back();
    }
}
