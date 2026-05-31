<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KnowledgeBase;
use Illuminate\Http\Request;

class KnowledgeBaseController extends Controller
{
    public function index()
    {
        $knowledgeBase = KnowledgeBase::orderBy('display_order')->orderBy('id')->paginate(15);

        return view('admin.knowledge-base.index', compact('knowledgeBase'));
    }

    public function create()
    {
        return view('admin.knowledge-base.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question_es' => 'nullable|string',
            'question_en' => 'nullable|string',
            'answer_es' => 'required|string',
            'answer_en' => 'required|string',
            'keywords_es' => 'nullable|string',
            'keywords_en' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Process keywords - convert comma-separated string to array
        if (!empty($validated['keywords_es'])) {
            $validated['keywords_es'] = array_map('trim', explode(',', $validated['keywords_es']));
        } else {
            $validated['keywords_es'] = [];
        }

        if (!empty($validated['keywords_en'])) {
            $validated['keywords_en'] = array_map('trim', explode(',', $validated['keywords_en']));
        } else {
            $validated['keywords_en'] = [];
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        KnowledgeBase::create($validated);

        return redirect()->route('admin.knowledge-base.index')->with('success', 'Knowledge base entry created successfully.');
    }

    public function edit(KnowledgeBase $knowledgeBase)
    {
        return view('admin.knowledge-base.edit', compact('knowledgeBase'));
    }

    public function update(Request $request, KnowledgeBase $knowledgeBase)
    {
        $validated = $request->validate([
            'question_es' => 'nullable|string',
            'question_en' => 'nullable|string',
            'answer_es' => 'required|string',
            'answer_en' => 'required|string',
            'keywords_es' => 'nullable|string',
            'keywords_en' => 'nullable|string',
            'display_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        // Process keywords - convert comma-separated string to array
        if (!empty($validated['keywords_es'])) {
            $validated['keywords_es'] = array_map('trim', explode(',', $validated['keywords_es']));
        } else {
            $validated['keywords_es'] = [];
        }

        if (!empty($validated['keywords_en'])) {
            $validated['keywords_en'] = array_map('trim', explode(',', $validated['keywords_en']));
        } else {
            $validated['keywords_en'] = [];
        }

        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['display_order'] = $validated['display_order'] ?? 0;

        $knowledgeBase->update($validated);

        return redirect()->route('admin.knowledge-base.index')->with('success', 'Knowledge base entry updated successfully.');
    }

    public function destroy(KnowledgeBase $knowledgeBase)
    {
        $knowledgeBase->delete();

        return redirect()->route('admin.knowledge-base.index')->with('success', 'Knowledge base entry deleted successfully.');
    }
}
