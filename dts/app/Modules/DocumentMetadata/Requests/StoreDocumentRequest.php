<?php

namespace App\Modules\DocumentMetadata\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'documentType' => 'required|string',
            'file' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ];
    }
}