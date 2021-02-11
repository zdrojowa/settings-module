<?php

namespace Selene\Modules\SettingsModule\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\SettingsModule\Models\Setting;
use Selene\Modules\SettingsModule\Support\SettingType;

class SettingsController extends Controller
{
    public function index() {
        return view('SettingsModule::index');
    }

    public function create() {
        return view('SettingsModule::edit', [
            'types' => SettingType::getArray()
        ]);
    }

    public function edit(Setting $setting) {
        return view('SettingsModule::edit', [
            'setting' => $setting,
            'types' => SettingType::getArray()
        ]);
    }

    public function store(Request $request) {
        $setting = $this->save($request);
        if ($setting) {
            $request->session()->flash('alert-success', 'Ustawienie zostało pomyślnie utworzone');
            return ['redirect' => route('SettingsModule::edit', ['setting' => $setting])];
        }

        $request->session()->flash('alert-error', 'Ooops. Try again.');
        return ['redirect' => route('settings.edit', ['setting' => $setting])];
    }

    public function update(Request $request, Setting $setting) {
        if ($this->save($request, $setting)) {
            $request->session()->flash('alert-success', 'Ustawienie zostało zaktualizowane');
        } else {
            $request->session()->flash('alert-error', 'Ooops. Try again.');
        }

        return ['redirect' => route('SettingsModule::edit', ['setting' => $setting])];
    }

    private function save(Request $request, Setting $setting = null) :? Setting
    {
        $type  = $request->get('type');
        $value = $request->get('value');
        if ($type === 'bool') {
            $request->merge(['value' => $value === 'true']);
        } elseif($type === 'datetime') {
            $request->merge(['value' => $value . ' ' . $request->get('time')]);
        } elseif($type === 'array') {
            $request->merge(['value' => json_decode($request->get('array'))]);
        } elseif (SettingType::isFile($type)) {
            $files = [];
            foreach(json_decode($request->get('files')) as $file) {
                $files[] = ['id' => $file->file->_id, 'type' => $file->type];
            }
            $request->merge(['value' => $files]);
        } elseif ($type === 'int') {
            $request->merge(['value' => $value >> 0]);
        } elseif ($type === 'decimal') {
            $request->merge(['value' => (float) str_replace(',', '.', $value)]);
        }

        if ($setting === null) {
            return Setting::create($request->all());
        }
        if ($setting->update($request->all())) {
            return $setting;
        }
        return null;
    }
}
