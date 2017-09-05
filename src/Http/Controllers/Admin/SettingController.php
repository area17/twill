<?php

namespace A17\CmsToolkit\Http\Controllers\Admin;

use A17\CmsToolkit\Repositories\SettingRepository;
use Illuminate\Routing\Controller;

class SettingController extends Controller
{
    public function __construct(SettingRepository $settings)
    {
        $this->settings = $settings;
    }

    public function index($section)
    {
        return view()->exists('admin.settings.' . $section) ? view('admin.settings.' . $section, [
            'section' => $section,
            'form_fields' => $this->settings->getFormFields($section),
            'form_options' => [
                'method' => 'POST',
                'url' => route('admin.settings.update', $section),
                'class' => "simple_form",
                'novalidate' => "novalidate",
                'accept-charset' => "UTF-8",
            ],
        ]) : redirect()->back();
    }

    public function update($section)
    {
        $this->settings->update(request()->except('_token'), $section);

        return redirect()->back();
    }
}
