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

    public static function getAllByKey() {
        $settings = [];
        foreach (self::all() as $setting) {
            $settings[$setting->key] = $setting;
        }
        return $settings;
    }
}
