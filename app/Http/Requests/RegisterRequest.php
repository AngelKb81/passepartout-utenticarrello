<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class RegisterRequest extends FormRequest
{
    /**
     * Determina se l'utente è autorizzato a fare questa richiesta.
     * La registrazione è pubblica, quindi sempre autorizzata.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole di validazione per la registrazione utente.
     */
    public function rules(): array
    {
        // DEBUG: Log dei dati ricevuti
        Log::info('Dati registrazione ricevuti:', $this->all());

        return [
            'nome' => ['required', 'string', 'max:255'],
            'cognome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'titolo_studi' => ['nullable', 'string', 'max:255'],
            'data_nascita' => ['nullable', 'date', 'before:today'],
            'citta_nascita' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * Messaggi di errore personalizzati in italiano.
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'Il nome è obbligatorio.',
            'cognome.required' => 'Il cognome è obbligatorio.',
            'email.required' => 'L\'email è obbligatoria.',
            'email.email' => 'Inserisci un indirizzo email valido.',
            'email.unique' => 'Questa email è già registrata.',
            'password.required' => 'La password è obbligatoria.',
            'password.min' => 'La password deve essere di almeno 8 caratteri.',
            'password.confirmed' => 'La conferma password non corrisponde.',
            'data_nascita.date' => 'Inserisci una data di nascita valida.',
            'data_nascita.before' => 'La data di nascita deve essere precedente a oggi.',
        ];
    }
}
