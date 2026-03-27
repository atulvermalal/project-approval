<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectStoreRequest extends FormRequest
{
    public static function baseRules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:5000'],
            'files' => ['nullable', 'array', 'max:5'],
            'files.*' => ['file', 'max:5120', 'mimes:pdf,doc,docx,png,jpg,jpeg,zip'],
        ];
    }

    public function authorize(): bool
    {
        return $this->user()?->hasPermission('projects.create') ?? false;
    }

    public function rules(): array
    {
        return self::baseRules();
    }
}
