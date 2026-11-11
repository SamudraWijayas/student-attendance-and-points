<?php

namespace App\Http\Controllers;

use App\Enums\PointType;
use App\Enums\ViolationSeverity;
use App\Models\AuditLog;
use App\Models\PointCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PointCategoryController extends Controller
{
    public function index(): View
    {
        $prestasiCategories = PointCategory::where('type', PointType::PRESTASI)->orderBy('min_points')->get();
        $pelanggaranCategories = PointCategory::where('type', PointType::PELANGGARAN)->orderBy('min_points')->get();

        return view('point-categories.index', compact('prestasiCategories', 'pelanggaranCategories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:prestasi,pelanggaran'],
            'severity' => ['nullable', 'in:ringan,sedang,berat,fatal'],
            'category_name' => ['required', 'string', 'max:100'],
            'action_name' => ['required', 'string', 'max:200'],
            'min_points' => ['required', 'integer', 'min:1'],
            'max_points' => ['required', 'integer', 'gte:min_points'],
            'default_points' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);
        $category = PointCategory::create($validated);

        AuditLog::log('CREATE_POINT_CATEGORY', 'point_categories', $category->id);

        return back()->with('success', 'Kategori poin berhasil ditambahkan.');
    }

    public function update(Request $request, PointCategory $pointCategory): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:prestasi,pelanggaran'],
            'severity' => ['nullable', 'in:ringan,sedang,berat,fatal'],
            'category_name' => ['required', 'string', 'max:100'],
            'action_name' => ['required', 'string', 'max:200'],
            'min_points' => ['required', 'integer', 'min:1'],
            'max_points' => ['required', 'integer', 'gte:min_points'],
            'default_points' => ['required', 'integer'],
            'description' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $pointCategory->update($validated);

        AuditLog::log('UPDATE_POINT_CATEGORY', 'point_categories', $pointCategory->id);

        return back()->with('success', 'Kategori poin berhasil diperbarui.');
    }

    public function toggleActive(PointCategory $pointCategory): RedirectResponse
    {
        $pointCategory->update(['is_active' => !$pointCategory->is_active]);

        return back()->with('success', "Status kategori '{$pointCategory->action_name}' berhasil diubah.");
    }
}

