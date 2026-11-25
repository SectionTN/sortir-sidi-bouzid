<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9-]+$/',
                Rule::notIn(['create', 'passes', 'brouillons']),
                Rule::unique('events', 'slug'),
            ],
            'summary' => ['nullable', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20'],
            'starts_at' => ['required', 'date'],
            'price' => ['required', 'integer', 'min:0', 'max:1000'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'venue_id' => ['required', 'integer', 'exists:venues,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
            'is_published' => ['nullable', 'boolean'],
        ];
    }
}
