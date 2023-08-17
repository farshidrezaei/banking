<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TopUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->resource->name,
            'mobile' => $this->resource->mobile,
            'transactions_count' => $this->resource->transactions_count,
            'last_transactions' => TopUserTransactionResource::collection($this->resource->transactions),
        ];
    }
}
