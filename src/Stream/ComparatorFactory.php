<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Hunts Chen <hunts.c@gmail.com>
 */

namespace Stream;


/**
 * Static factory for creating comparator object.
 */
class ComparatorFactory implements Comparator
{
    private static $comparators = array();

    private $compareFunc;

    public function __construct($compareFunc)
    {
        $this->compareFunc = $compareFunc;
    }

    /**
     * @inheritDoc
     */
    public function compare($first, $second)
    {
        return call_user_func($this->compareFunc, $first, $second);
    }

    /**
     *
     * @param callable $compareFunc Custom compare function.
     * @return Comparator
     */
    public static function create(callable $compareFunc)
    {
        return new self($compareFunc);
    }

    /**
     * @param $isCaseSensitive
     * @return Comparator
     */
    public static function stringComparator($isCaseSensitive = false)
    {
        $cacheKey = 'b_' . $isCaseSensitive;

        if (!array_key_exists($cacheKey, self::$comparators)) {
            self::$comparators[$cacheKey] = self::create(function ($first, $second) use ($isCaseSensitive) {
                if ($isCaseSensitive) {
                    return strcmp($first, $second);
                }

                return strcasecmp($first, $second);
            });
        }
        return self::$comparators[$cacheKey];
    }

    /**
     * @param number $epsilon
     * @return Comparator
     */
    public static function floatComparator($epsilon)
    {
        $cacheKey = 'f_' . $epsilon;

        if (!array_key_exists($cacheKey, self::$comparators)) {
            self::$comparators[$cacheKey] = self::create(function ($first, $second) use ($epsilon) {
                if (abs($first - $second) <= $epsilon) {
                    return 0;
                }

                if ($first > $second) {
                    return 1;
                }

                return -1;
            });
        }

        return self::$comparators[$cacheKey];
    }

    /**
     * @return Comparator
     */
    public static function intComparator()
    {
        return self::floatComparator(0);
    }
}