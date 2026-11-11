<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolSettingController extends Controller
{
    /**
     * Profil Sekolah.
     */
    public function school(): View
    {
        $school = SchoolSetting::current();
        return view('settings.school', compact('school'));
    }

    public function updateSchool(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:150'],
            'address' => ['required', 'string'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:100'],
            'website' => ['nullable', 'string', 'max:100'],
            'principal_name' => ['required', 'string', 'max:150'],
            'principal_nip' => ['nullable', 'string', 'max:50'],
        ]);

        $school = SchoolSetting::current();
        $school->update($validated);

        AuditLog::log('UPDATE_SCHOOL_SETTINGS', 'settings', $school->id);

        return back()->with('success', 'Profil sekolah berhasil diperbarui.');
    }

    /**
     * Akun Admin.
     */
    public function account(): View
    {
        $user = Auth::user();
        return view('settings.account', compact('user'));
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:100', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->update($validated);

        AuditLog::log('UPDATE_ADMIN_PROFILE', 'users', $user->id);

        return back()->with('success', 'Informasi akun admin berhasil diperbarui.');
    }
}

