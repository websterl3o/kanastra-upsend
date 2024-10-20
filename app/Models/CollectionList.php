<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollectionList extends Model
{
    use HasFactory;

    protected $table = 'collection_lists';

    protected $fillable = [
        'original_name',
        'name',
        'path',
        'processed_at',
    ];

    protected $casts = [
        'processed_at' => 'datetime',
    ];
}
