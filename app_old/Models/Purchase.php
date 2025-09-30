<?php

namespace App\Models;

use App\Models\Payment;
use App\Models\ArrivalTime;
use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Purchase extends Model
{
    public function payment(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function arrivalTime(): BelongsTo
    {
        return $this->belongsTo(ArrivalTime::class);
    }

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }
}
