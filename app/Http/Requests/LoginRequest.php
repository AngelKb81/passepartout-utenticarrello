<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Il login è pubblico, quindi sempre autorizzato.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per il login.
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Messaggi di errore personalizzati in italiano.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'password.required' => 'La password è obbligatoria.',
        ];
    }
}
