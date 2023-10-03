<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'history_header_id',
        'casette_1',
        'casette_2',
        'casette_3',
        'casette_4',
        'casette_5',
        'casette_6',
        'casette_7',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
