<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use App\Models\Major;
use App\Models\SchoolClass;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SchoolClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = SchoolClass::with(['major', 'academicYear'])->withCount('students');

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->filled('grade_level')) {
            $query->where('grade_level', $request->grade_level);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $classes = $query->orderBy('grade_level')->orderBy('name')->get();
        $majors = Major::where('is_active', true)->get();
        $academicYears = AcademicYear::where('is_active', true)->get();

        return view('classes.index', compact('classes', 'majors', 'academicYears'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'major_id' => ['required', 'exists:majors,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'grade_level' => ['required', 'in:10,11,12'],
            'name' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        $cls = SchoolClass::create($validated);

        AuditLog::log('CREATE_CLASS', 'classes', $cls->id, ['name' => $cls->name]);

        return back()->with('success', "Kelas {$cls->name} berhasil ditambahkan.");
    }

    public function update(Request $request, SchoolClass $schoolClass): RedirectResponse
    {
        $validated = $request->validate([
            'major_id' => ['required', 'exists:majors,id'],
            'academic_year_id' => ['nullable', 'exists:academic_years,id'],
            'grade_level' => ['required', 'in:10,11,12'],
            'name' => ['required', 'string', 'max:50'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $schoolClass->update($validated);

        AuditLog::log('UPDATE_CLASS', 'classes', $schoolClass->id, ['name' => $schoolClass->name]);

        return back()->with('success', "Kelas {$schoolClass->name} berhasil diperbarui.");
    }

    public function destroy(SchoolClass $schoolClass): RedirectResponse
    {
        if ($schoolClass->students()->exists()) {
            return back()->with('error', 'Kelas tidak dapat dihapus karena masih memiliki data siswa di dalamnya.');
        }

        $name = $schoolClass->name;
        $schoolClass->delete();

        AuditLog::log('DELETE_CLASS', 'classes', $schoolClass->id, ['name' => $name]);

        return back()->with('success', "Kelas {$name} berhasil dihapus.");
    }
}

