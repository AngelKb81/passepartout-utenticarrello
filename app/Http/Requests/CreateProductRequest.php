<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CreateProductRequest extends FormRequest
{
    /**
     * Solo gli admin possono creare prodotti.
     */
    public function authorize(): bool
    {
        return Auth::check() && Auth::user()->isAdmin();
    }

    /**
     * Regole di validazione per la creazione prodotto.
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'codice' => ['required', 'string', 'max:100', 'unique:products,codice'],
            'descrizione' => ['required', 'string'],
            'categoria' => ['required', 'string', 'max:100'],
            'prezzo' => ['required', 'numeric', 'min:0.01'],
            'immagine' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'], // 5MB
            'attivo' => ['nullable', 'boolean'],
            'scorte' => ['required', 'integer', 'min:0'],
        ];
    }

    /**
     * Messaggi di errore personalizzati in italiano.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'Il nome del prodotto è obbligatorio.',
            'codice.required' => 'Il codice prodotto è obbligatorio.',
            'codice.unique' => 'Questo codice prodotto è già in uso.',
            'descrizione.required' => 'La descrizione è obbligatoria.',
            'categoria.required' => 'La categoria è obbligatoria.',
            'prezzo.required' => 'Il prezzo è obbligatorio.',
            'prezzo.numeric' => 'Il prezzo deve essere un numero valido.',
            'prezzo.min' => 'Il prezzo deve essere maggiore di zero.',
            'immagine.image' => 'Il file deve essere un\'immagine.',
            'immagine.mimes' => 'L\'immagine deve essere in formato: jpeg, jpg, png, webp.',
            'immagine.max' => 'L\'immagine non può superare i 5MB.',
            'scorte.required' => 'Le scorte sono obbligatorie.',
            'scorte.integer' => 'Le scorte devono essere un numero intero.',
            'scorte.min' => 'Le scorte non possono essere negative.',
        ];
    }
}
