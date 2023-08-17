<?php

namespace App\Http\Requests;

use App\Library\StringHelper;
use App\Rules\CardRule;
use Illuminate\Foundation\Http\FormRequest;

class CardToCardRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'source_card_number' => ['required', 'string', new CardRule(), 'exists:cards,number'],
            'destination_card_number' => ['required', 'string', new CardRule(), 'exists:cards,number'],
            'amount' => ['required', 'integer', 'min:' . 10_000, 'max:' . 500_000_000]
        ];
    }

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'source_card_number' => StringHelper::toEnglish($this->source_card_number),
            'destination_card_number' => StringHelper::toEnglish($this->destination_card_number),
            'amount' => StringHelper::toEnglish($this->amount)
        ]);
    }
}
