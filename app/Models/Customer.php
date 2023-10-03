<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'account',
        'name',
        'balance',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function history(): HasMany
    {
        return $this->hasMany(HistoryHeader::class);
    }
}
