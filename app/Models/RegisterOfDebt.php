<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegisterOfDebt extends Model
{
    use HasFactory;

    protected $table = 'register_of_debts';

    protected $primaryKey = 'uuid';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'uuid',
        'amount',
        'dueDate',
        'name',
        'email',
        'government_id',
        'collectionlist_id',
        'notified_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'notified_at' => 'datetime',
    ];
}
