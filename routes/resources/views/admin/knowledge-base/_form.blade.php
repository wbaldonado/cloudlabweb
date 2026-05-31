@csrf

<div class="row">
    <div class="col-md-6">
        <h5 class="mb-3 text-primary">Spanish (ES)</h5>
        
        <div class="mb-3">
            <label for="question_es" class="form-label">Question (Spanish) <span class="text-muted">(optional)</span></label>
            <input type="text" name="question_es" id="question_es" value="{{ old('question_es', $entry->question_es ?? '') }}" class="form-control" placeholder="Enter question in Spanish">
            <div class="form-text">This helps identify the question but is not required for matching.</div>
            @error('question_es')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="answer_es" class="form-label">Answer (Spanish) <span class="text-danger">*</span></label>
            <textarea name="answer_es" id="answer_es" rows="5" class="form-control" required placeholder="Enter answer in Spanish">{{ old('answer_es', $entry->answer_es ?? '') }}</textarea>
            @error('answer_es')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="keywords_es" class="form-label">Keywords (Spanish) <span class="text-muted">(optional)</span></label>
            <input type="text" name="keywords_es" id="keywords_es" value="{{ old('keywords_es', is_array($entry->keywords_es ?? null) ? implode(', ', $entry->keywords_es) : ($entry->keywords_es ?? '')) }}" class="form-control" placeholder="palabra1, palabra2, palabra3">
            <div class="form-text">Comma-separated keywords to help match user questions (e.g., herramienta, tecnologia, seguridad).</div>
            @error('keywords_es')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <h5 class="mb-3 text-primary">English (EN)</h5>
        
        <div class="mb-3">
            <label for="question_en" class="form-label">Question (English) <span class="text-muted">(optional)</span></label>
            <input type="text" name="question_en" id="question_en" value="{{ old('question_en', $entry->question_en ?? '') }}" class="form-control" placeholder="Enter question in English">
            <div class="form-text">This helps identify the question but is not required for matching.</div>
            @error('question_en')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="answer_en" class="form-label">Answer (English) <span class="text-danger">*</span></label>
            <textarea name="answer_en" id="answer_en" rows="5" class="form-control" required placeholder="Enter answer in English">{{ old('answer_en', $entry->answer_en ?? '') }}</textarea>
            @error('answer_en')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="keywords_en" class="form-label">Keywords (English) <span class="text-muted">(optional)</span></label>
            <input type="text" name="keywords_en" id="keywords_en" value="{{ old('keywords_en', is_array($entry->keywords_en ?? null) ? implode(', ', $entry->keywords_en) : ($entry->keywords_en ?? '')) }}" class="form-control" placeholder="word1, word2, word3">
            <div class="form-text">Comma-separated keywords to help match user questions (e.g., tool, technology, security).</div>
            @error('keywords_en')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>
</div>

<hr class="my-4">

<div class="row">
    <div class="col-md-6">
        <div class="mb-3">
            <label for="display_order" class="form-label">Display Order</label>
            <input type="number" name="display_order" id="display_order" value="{{ old('display_order', $entry->display_order ?? 0) }}" class="form-control" min="0">
            <div class="form-text">Lower numbers appear first (priority for matching).</div>
            @error('display_order')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-check form-switch mb-4 mt-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ old('is_active', $entry->is_active ?? true) ? 'checked' : '' }}>
            <label class="form-check-label" for="is_active">Active</label>
            <div class="form-text">Inactive entries won't be displayed.</div>
        </div>
    </div>
</div>

<button type="submit" class="btn btn-primary">{{ $submitLabel ?? 'Save Entry' }}</button>
<a href="{{ route('admin.knowledge-base.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>

