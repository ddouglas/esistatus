<?php

namespace ESIS;

use Illuminate\Database\Eloquent\Model;

use ESIS\Traits\HasCompositePrimaryKey;

class Status extends Model
{
    use HasCompositePrimaryKey;

    protected $primaryKey = ['route', 'method'];
    protected $table = 'statuses';
    public $incrementing = false;
    protected static $unguarded = true;

    public function getTagsAttribute($tags)
    {
        return collect(json_decode($tags, true));
    }
}
