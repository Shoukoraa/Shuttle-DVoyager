<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|string|max:20',
        ]);

        $user->update($request->only(['name', 'phone']));

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user]);
    }

    public function updatePhoto(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'profile_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $file = $request->file('profile_photo');

        if (!$file) {
            return response()->json([
                'message' => 'File profile_photo tidak ditemukan.'
            ], 422);
        }

        $oldPhotoPath = $user->profile_photo_path;
        $newPhotoPath = $file->store('profile-photos', 'public');

        $user->update([
            'profile_photo_path' => $newPhotoPath,
        ]);

        if ($oldPhotoPath && $oldPhotoPath !== $newPhotoPath && !filter_var($oldPhotoPath, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($oldPhotoPath);
        }

        return response()->json([
            'message' => 'Foto profil berhasil diperbarui.',
            'user_id' => $user->id,
            'profile_photo_path' => $newPhotoPath,
            'profile_photo_url' => asset(Storage::url($newPhotoPath)),
        ]);
    }

    public function updatePassword(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $hasPassword = !empty($user->password);

        $validated = $request->validate([
            'current_password' => [$hasPassword ? 'required' : 'nullable', 'string'],
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($hasPassword && !Hash::check($validated['current_password'], $user->password)) {
            return response()->json([
                'message' => 'Password lama tidak sesuai.',
                'errors' => [
                    'current_password' => ['Password lama tidak sesuai.'],
                ],
            ], 422);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json([
            'message' => $hasPassword ? 'Password berhasil diperbarui.' : 'Password berhasil dibuat.',
        ]);
    }

    public function destroy(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        // Cabut semua token pengguna
        $user->tokens()->delete();

        // Soft delete user (karena ada softDeletes di model User)
        $user->delete();

        return response()->json(['message' => 'Akun berhasil dihapus.']);
    }
}
