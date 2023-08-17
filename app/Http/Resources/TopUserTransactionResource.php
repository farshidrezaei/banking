<?php

namespace App\Http\Resources;

use App\Library\StringHelper;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


class TopUserTransactionResource extends JsonResource
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
            'fee_amount' => $this->resource->fee_amount,
            'status' => $this->resource->status,
            'type' => $this->resource->type,
            'card_number' => StringHelper::maskString($this->resource->card_number, 4, 8),
            'done_at' => $this->resource->done_at,
            'track_id' => $this->resource->track_id,
        ];
    }
}
