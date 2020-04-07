<?php

namespace Selene\Modules\SettingsModule\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Selene\Modules\SettingsModule\Support\SettingType;

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
            $settings[$setting->key]['type'] = $setting->type;

            if (SettingType::isFile($setting->type) || $setting->type === 'array') {
                $settings[$setting->key]['value'] = json_decode($setting->value, true);
            } else {
                $settings[$setting->key]['value'] = $setting->value;
            }
        }
        return $settings;
    }
}
