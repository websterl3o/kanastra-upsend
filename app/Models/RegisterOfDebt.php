<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegisterOfDebt extends Model
{
    protected $table = 'register_of_debt';

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
}
