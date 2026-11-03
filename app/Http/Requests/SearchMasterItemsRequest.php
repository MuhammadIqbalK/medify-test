<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SearchMasterItemsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'hargamin' => ['nullable', 'numeric', 'min:0'],
            'hargamax' => array_merge(['nullable', 'numeric', 'min:0'], $this->filled('hargamin') ? ['gte:hargamin'] : []),
        ];
    }
}
