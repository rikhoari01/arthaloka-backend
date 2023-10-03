<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class HistoryHeader extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'atm_id',
        'total_withdraw',
    ];

    public function detail(): HasOne
    {
        return $this->hasOne(HistoryDetail::class);
    }
}
