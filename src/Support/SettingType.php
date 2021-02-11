<?php

namespace Selene\Modules\SettingsModule\Support;

use MyCLabs\Enum\Enum;

class SettingType extends Enum
{
    protected const STRING   = 'string';
    protected const BOOL     = 'bool';
    protected const INT      = 'int';
    protected const DECIMAL  = 'decimal';
    protected const LINK     = 'link';
    protected const IMAGE    = 'image';
    protected const VIDEO    = 'video';
    protected const FILE     = 'file';
    protected const DATE     = 'date';
    protected const DATETIME = 'datetime';
    protected const EMAIL    = 'email';
    protected const ARRAY    = 'array';

    public static function isFile($type) {
        return in_array($type, ['image', 'video', 'file']);
    }

    public static function get() {
        $types = [];
        foreach (self::toArray() as $key => $value) {
            $types[] = $value;
        }
        return $types;
    }

    public static function getArray() {
        $types = [];
        foreach (self::toArray() as $key => $value) {
            $types[] = ['value' => $value, 'text' => $value];
        }
        return $types;
    }
}
