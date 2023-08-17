<?php

namespace App\Http\Resources;

use App\Enums\TransactionTypeEnum;
use App\Library\StringHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'amount' => $this->resource->amount,
            'fee_amount' => $this->when($this->resource->type === TransactionTypeEnum::OUTCOME, $this->resource->fee?->amount),
            'status' => $this->resource->status->value,
            'type' => $this->resource->type->value,
            'card_number' => StringHelper::maskString($this->resource->card->number, 4, 8),
            'done_at' => $this->resource->done_at?->format('Y/m/d H:i:s'),
            'track_id' => $this->resource->track_id,
        ];
    }
}
