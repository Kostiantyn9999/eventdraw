<?php

namespace backend\components;
/**
 * Class ArrayValues
 */
class ArrayValues
{
    const STATUS = [
        '9' => 'Trial',
        '10' => 'Full Version',
        '11' => 'Custom Trial',
    ];

    /**
     * @param $value
     * @return int
     */
    public static function userStatusKeyByValue($value): int
    {
        return array_search($value, self::STATUS);
    }
}
