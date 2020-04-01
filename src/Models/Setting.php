<?php

namespace Selene\Modules\SettingsModule\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * @method static create(array $all)
 */
class Setting extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'settings';

    protected $appends = ['id'];
    protected $hidden  = ['_id'];

    protected $primaryKey = '_id';

    protected $fillable = ['key', 'type', 'value'];
}
