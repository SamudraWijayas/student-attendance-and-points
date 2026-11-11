<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\AuditLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcademicYearController extends Controller
{
    public function index(): View
    {
        $years = AcademicYear::withCount(['semesters', 'classes'])->orderBy('start_date', 'desc')->get();
        return view('academic-years.index', compact('years'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20', 'unique:academic_years,name'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
        ]);

        if ($request->boolean('is_active')) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $ay = AcademicYear::create($validated);

        AuditLog::log('CREATE_ACADEMIC_YEAR', 'academic_years', $ay->id, ['name' => $ay->name]);

        return back()->with('success', "Tahun ajaran {$ay->name} berhasil ditambahkan.");
    }

    public function update(Request $request, AcademicYear $academicYear): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:20'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'is_active' => ['boolean'],
        ]);

        if ($request->boolean('is_active')) {
            AcademicYear::where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $validated['is_active'] = $request->boolean('is_active');
        $academicYear->update($validated);

        AuditLog::log('UPDATE_ACADEMIC_YEAR', 'academic_years', $academicYear->id, ['name' => $academicYear->name]);

        return back()->with('success', "Tahun ajaran {$academicYear->name} berhasil diperbarui.");
    }
}

