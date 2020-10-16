<?php

namespace Selene\Modules\SettingsModule\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Selene\Modules\SettingsModule\Models\Setting;

class ApiController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        $settings = Setting::query();
        if ($request->has('id')) {
            $settings->where('_id', '=', $request->get('id'));
            return response()->json($settings->first());
        }

        if ($request->has('key')) {
            $settings->where('key', '=', $request->get('key'));
            return response()->json($settings->first());
        }

        return response()->json($settings->get());
    }
}
