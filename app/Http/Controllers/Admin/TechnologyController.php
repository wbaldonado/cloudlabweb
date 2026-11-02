<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Technology;
use App\Support\PublicMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class TechnologyController extends Controller
{
    protected function colorOptions(): array
    {
        return [
            'blue',
            'purple',
            'yellow',
            'purple-light',
            'pink',
            'python',
            'tableau',
        ];
    }

    public function index()
    {
        $technologies = Technology::orderBy('display_order')->orderBy('name')->get();

        return view('admin.technologies.index', [
            'technologies' => $technologies,
        ]);
    }

    public function create()
    {
        return view('admin.technologies.create', [
            'colorOptions' => $this->colorOptions(),
            'technology' => new Technology(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:technologies,name'],
            'description' => ['nullable', 'string'],
            'color_class' => ['required', Rule::in($this->colorOptions())],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'icon' => ['required', 'file', 'max:2048', 'mimes:svg,png,jpg,jpeg,webp'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        try {
            $iconPath = PublicMedia::storeImage($request->file('icon'), 'technologies');
        } catch (Throwable $e) {
            Log::error('Technology icon upload failed', ['error' => $e->getMessage()]);

            return back()
                ->withInput()
                ->withErrors([
                    'icon' => 'Could not save the icon file. Ensure public/images/technologies is writable on the server.',
                ]);
        }

        Technology::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color_class' => $validated['color_class'],
            'display_order' => $validated['display_order'] ?? 0,
            'icon_path' => $iconPath,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.technologies.index')->with('success', 'Technology created successfully.');
    }

    public function edit(Technology $technology)
    {
        return view('admin.technologies.edit', [
            'technology' => $technology,
            'colorOptions' => $this->colorOptions(),
        ]);
    }

    public function update(Request $request, Technology $technology)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('technologies', 'name')->ignore($technology->id)],
            'description' => ['nullable', 'string'],
            'color_class' => ['required', Rule::in($this->colorOptions())],
            'display_order' => ['nullable', 'integer', 'min:0'],
            'icon' => ['nullable', 'file', 'max:2048', 'mimes:svg,png,jpg,jpeg,webp'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'color_class' => $validated['color_class'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ];

        if ($request->hasFile('icon')) {
            try {
                PublicMedia::delete($technology->icon_path);
                $data['icon_path'] = PublicMedia::storeImage($request->file('icon'), 'technologies');
            } catch (Throwable $e) {
                Log::error('Technology icon update failed', ['error' => $e->getMessage()]);

                return back()
                    ->withInput()
                    ->withErrors([
                        'icon' => 'Could not save the icon file. Check storage permissions on the server.',
                    ]);
            }
        }

        $technology->update($data);

        return redirect()->route('admin.technologies.index')->with('success', 'Technology updated successfully.');
    }

    public function destroy(Technology $technology)
    {
        PublicMedia::delete($technology->icon_path);

        $technology->delete();

        return redirect()->route('admin.technologies.index')->with('success', 'Technology deleted successfully.');
    }

}
