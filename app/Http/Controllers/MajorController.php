<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Major;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MajorController extends Controller
{
    public function index(): View
    {
        $majors = Major::withCount('classes')->orderBy('code')->get();
        return view('majors.index', compact('majors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', 'unique:majors,code'],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $major = Major::create($validated);

        AuditLog::log('CREATE_MAJOR', 'majors', $major->id, ['code' => $major->code, 'name' => $major->name]);

        return back()->with('success', "Jurusan {$major->name} ({$major->code}) berhasil ditambahkan.");
    }

    public function update(Request $request, Major $major): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:20', Rule::unique('majors')->ignore($major->id)],
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $major->update($validated);

        AuditLog::log('UPDATE_MAJOR', 'majors', $major->id, ['code' => $major->code, 'name' => $major->name]);

        return back()->with('success', "Jurusan {$major->name} berhasil diperbarui.");
    }

    public function destroy(Major $major): RedirectResponse
    {
        if ($major->classes()->exists()) {
            return back()->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki relasi data kelas.');
        }

        $majorName = $major->name;
        $major->delete();

        AuditLog::log('DELETE_MAJOR', 'majors', $major->id, ['name' => $majorName]);

        return back()->with('success', "Jurusan {$majorName} berhasil dihapus.");
    }
}

