<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionList extends Model
{
    protected $table = 'collection_lists';

    protected $fillable = [
        'original_name',
        'name',
        'path',
        'processed',
    ];
}
