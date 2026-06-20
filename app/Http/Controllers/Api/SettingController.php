<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateSettingRequest;
use App\Http\Resources\UserSettingResource;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $settings = $request->user()->settings ?? UserSetting::create(['user_id' => $request->user()->id]);

        return response()->json(new UserSettingResource($settings));
    }

    public function update(UpdateSettingRequest $request): JsonResponse
    {
        $settings = $request->user()->settings ?? UserSetting::create(['user_id' => $request->user()->id]);

        $settings->update($request->validated());

        return response()->json([
            'message' => 'Pengaturan berhasil diperbarui',
            'settings' => new UserSettingResource($settings->fresh()),
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $request->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'Password berhasil diubah',
        ]);
    }
}