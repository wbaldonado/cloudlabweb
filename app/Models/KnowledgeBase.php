<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'knowledge_base';

    protected $fillable = [
        'question_es',
        'question_en',
        'answer_es',
        'answer_en',
        'keywords_es',
        'keywords_en',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'keywords_es' => 'array',
        'keywords_en' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get question based on locale
     */
    public function getQuestionAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"question_{$locale}"} ?? $this->question_es;
    }

    /**
     * Get answer based on locale
     */
    public function getAnswerAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"answer_{$locale}"} ?? $this->answer_es;
    }

    /**
     * Get keywords based on locale
     */
    public function getKeywordsAttribute()
    {
        $locale = app()->getLocale();
        return $this->{"keywords_{$locale}"} ?? $this->keywords_es ?? [];
    }
}
