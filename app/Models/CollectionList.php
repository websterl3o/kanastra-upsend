<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CollectionList extends Model
{
    protected $table = 'collection_lists';

    protected $fillable = [
        'name_file',
        'path',
        'processed',
    ];
}
