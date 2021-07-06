<?php

namespace App\Enums;

class SyncType extends Base
{
    const SINGLE = 0;
    const PAGINATED = 1;

    public static function getDescription($value): string
    {
        switch ($value) {
            case self::SINGLE: return 'Single';
            case self::PAGINATED: return 'Paginated';
        }

        return parent::getDescription($value);
    }
}
