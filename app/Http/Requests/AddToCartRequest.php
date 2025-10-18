<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AddToCartRequest extends FormRequest
{
    /**
     * Solo utenti autenticati possono gestire il carrello.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Prepara i dati per la validazione normalizzando i parametri.
     */
    protected function prepareForValidation()
    {
        // Normalizza quantity -> quantita per compatibilità test
        if ($this->has('quantity') && !$this->has('quantita')) {
            $this->merge(['quantita' => $this->get('quantity')]);
        }
    }

    /**
     * Regole di validazione per aggiungere prodotti al carrello.
     */
    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantita' => ['required', 'integer', 'min:1', 'max:99'],
        ];
    }

    /**
     * Messaggi di errore personalizzati in italiano.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Il prodotto è obbligatorio.',
            'product_id.exists' => 'Prodotto non trovato.',
            'quantita.required' => 'La quantità è obbligatoria.',
            'quantita.integer' => 'La quantità deve essere un numero intero.',
            'quantita.min' => 'La quantità deve essere almeno 1.',
            'quantita.max' => 'La quantità massima è 99.',
        ];
    }
}
