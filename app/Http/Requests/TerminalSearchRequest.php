<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TerminalSearchRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => 'required|string|min:2'
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
