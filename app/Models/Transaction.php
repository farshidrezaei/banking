<?php

namespace App\Models;

use App\Enums\TransactionStatusEnum;
use App\Enums\TransactionTypeEnum;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;
    use HasUlids;

    protected $guarded = ['id'];

    protected $casts = [
        'status' => TransactionStatusEnum::class,
        'type' => TransactionTypeEnum::class,
        'done_at' => 'datetime'
    ];

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function fee(): HasOne
    {
        return $this->hasOne(Fee::class, 'transaction_id');
    }


}
