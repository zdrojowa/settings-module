<?php

namespace Selene\Modules\SettingsModule\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\DashboardModule\ZdrojowaTable;
use Selene\Modules\SettingsModule\Models\Setting;
use Selene\Modules\SettingsModule\Support\SettingType;

class SettingsController extends Controller
{

    public function index() {
        return view('SettingsModule::list');
    }

    public function ajax(Request $request) {
        return ZdrojowaTable::response(Setting::query(), $request);
    }

    public function create() {
        return view('SettingsModule::edit');
    }

    public function edit(Setting $setting) {
        return view('SettingsModule::edit', ['setting' => $setting]);
    }

    public function store(Request $request) {
        $setting = $this->save($request);
        if ($setting) {
            $request->session()->flash('alert-success', 'Ustawienie zostało pomyślnie utworzone');
            return ['redirect' => route('SettingsModule::settings.edit', ['setting' => $setting])];
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

        return ['redirect' => route('SettingsModule::settings.edit', ['setting' => $setting])];
    }

    private function save(Request $request, Setting $setting = null) :? Setting
    {
        $obj   = json_decode($request->get('obj'));
        $type  = $obj->type;
        $value = $obj->value;
        $key   = $obj->key;

        $request->merge([
            'key'   => $key,
            'type'  => $type,
            'value' => $value,
        ]);
        if (SettingType::isFile($type)) {
            $request->merge(['value' => $request->get('files')]);
        } elseif ($type === 'bool') {
            $request->merge(['value' => $request->get('value')]);
        } elseif ($type === 'int') {
            $request->merge(['value' => $value >> 0]);
        } elseif ($type === 'decimal') {
            $request->merge(['value' => (float) str_replace(',', '.', $value)]);
        } elseif ($type === 'array') {
            $request->merge(['value' => json_encode($obj->array)]);
        }

        if ($setting === null) {
            return Setting::create($request->all());
        }
        if ($setting->update($request->all())) {
            return $setting;
        }
        return null;
    }

    public function destroy(Setting $setting, Request $request): void
    {
        try {
            $setting->delete();
            $request->session()->flash('alert-success', 'Ustawienie zostało usunęte');
        } catch (\Exception $e) {
            $request->session()->flash('alert-error', 'Error: ' . $e->getMessage());
        }
    }

    public function types(): JsonResponse
    {
        return response()->json(SettingType::toArray());
    }

    public function get($id): JsonResponse
    {
        return response()->json(Setting::query()->findOrFail($id));
    }

    public function getByKey(string $key): JsonResponse
    {
        return response()->json(Setting::query()->where('key', '=', $key)->firstOrFail());
    }

    public function checkKey($id, Request $request): JsonResponse
    {
        $exists = Setting::query()->where('_id', '!=', $id)
            ->where('key', '=', $request->get('key'))
            ->exists();
        return response()->json(!$exists);
    }
}
