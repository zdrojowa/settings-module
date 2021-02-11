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

    public function checkKey(Request $request): JsonResponse
    {
        $key = $request->get('key');
        if (empty($key)) {
            return response()->json(['message' => 'Key is required'],JsonResponse::HTTP_BAD_REQUEST);
        }
        $exists = Setting::query();
        if ($request->has('id')) {
            $exists->where('_id', '!=', $request->get('id'));
        }
        return response()->json(!$exists->where('key', '=', $key)->exists());
    }

    public function remove(Setting $setting, Request $request): JsonResponse
    {
        try {
            $setting->delete();
            return response()->json(['message' => 'Ustawienie zostało usunęte']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()],JsonResponse::HTTP_BAD_REQUEST);
        }
    }
}
