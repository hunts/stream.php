<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Hunts\Stream;

/**
 * Static factory for creating comparator object.
 */
class ComparatorFactory implements Comparator
{
    /**
     * @var Comparator[]
     */
    private static $comparators = [];

    /**
     * @var callable
     */
    private $compareFunc;

    private function __construct(callable $compareFunc)
    {
        $this->compareFunc = $compareFunc;
    }

    /**
     * @inheritDoc
     */
    public function compare($first, $second): int
    {
        return call_user_func($this->compareFunc, $first, $second);
    }

    /**
     *
     * @param callable $compareFunc Custom compare function.
     *
     * @return Comparator
     */
    public static function create(callable $compareFunc): Comparator
    {
        return new self($compareFunc);
    }

    /**
     * @param bool $isCaseSensitive
     * @param int $flags [optional] <p>
     * Flag determining how to deal with <i>null</i>:
     * </p><ul>
     * <li>
     * <b>NULL_AS_ZERO</b> - <i>NULL</i> is treat as equal to the
     * empty string
     * </li>
     * <li>
     * <b>NULL_LT_ZERO</b> - <i>NULL</i> is treat as less than
     * the empty string
     * </li>
     * <li>
     * <b>NULL_GT_ANY</b> - <i>NULL</i> is treat as greater than
     * any string
     * </li>
     * </ul>
     *
     * @return Comparator
     */
    public static function stringComparator(bool $isCaseSensitive = false, int $flags = 0b0): Comparator
    {
        $cacheKey = "b_{$flags}_{$isCaseSensitive}";

        if (!array_key_exists($cacheKey, self::$comparators)) {
            self::$comparators[$cacheKey] = self::create(function (?string $first, ?string $second) use ($isCaseSensitive, $flags) {
                if ($first === null || $second === null) {
                    return self::compareNulls($first, $second, $flags);
                }

                if ($isCaseSensitive) {
                    return strcmp($first, $second) <=> 0;
                }

                return strcasecmp($first, $second) <=> 0;
            });
        }

        return self::$comparators[$cacheKey];
    }

    /**
     * @param float $epsilon
     * @param int $flags [optional] <p>
     * Flag determining how to deal with <i>null</i>:
     * </p><ul>
     * <li>
     * <b>NULL_AS_ZERO</b> - <i>NULL</i> is treat as equal to 0
     * </li>
     * <li>
     * <b>NULL_LT_ZERO</b> - <i>NULL</i> is treat as less than 0
     * </li>
     * <li>
     * <b>NULL_GT_ANY</b> - <i>NULL</i> is treat as greater than
     * any value
     * </li>
     * </ul>
     *
     * @return Comparator
     */
    public static function floatComparator(float $epsilon, int $flags = 0b0): Comparator
    {
        $cacheKey = "f_{$flags}_{$epsilon}";

        if (!array_key_exists($cacheKey, self::$comparators)) {
            self::$comparators[$cacheKey] = self::create(function (?float $first, ?float $second) use ($epsilon, $flags) {
                if ($first === null || $second === null) {
                    return self::compareNulls($first, $second, $flags);
                }

                if (abs($first - $second) <= $epsilon) {
                    return 0;
                }

                return $first <=> $second;
            });
        }

        return self::$comparators[$cacheKey];
    }

    /**
     *
     * @param int $flags [optional] <p>
     * Flag determining how to deal with <i>null</i>:
     * </p><ul>
     * <li>
     * <b>NULL_AS_ZERO</b> - <i>NULL</i> is treat as equal to 0
     * </li>
     * <li>
     * <b>NULL_LT_ZERO</b> - <i>NULL</i> is treat as less than 0
     * </li>
     * <li>
     * <b>NULL_GT_ANY</b> - <i>NULL</i> is treat as greater than
     * any value
     * </li>
     * </ul>
     *
     * @return Comparator
     */
    public static function intComparator(int $flags = 0b0): Comparator
    {
        $cacheKey = "i_{$flags}";

        if (!array_key_exists($cacheKey, self::$comparators)) {
            self::$comparators[$cacheKey] = self::create(function (?int $first, ?int $second) use ($flags) {
                if ($first === null || $second === null) {
                    return self::compareNulls($first, $second, $flags);
                }

                return $first <=> $second;
            });
        }

        return self::$comparators[$cacheKey];
    }

    /**
     * Compares two values in which at least one is NULL.
     *
     * @param mixed $a
     * @param mixed $b
     * @param int $flags
     *
     * @return int
     */
    private static function compareNulls($a, $b, int $flags): int
    {
        if ($a === null && $b === null) {
            return 0;
        }

        if ($flags === Comparator::NULL_AS_ZERO) {
            // spaceship operator treat null as zero value
            return $a <=> $b;
        }

        return ($flags === Comparator::NULL_LT_ZERO xor $a === null)
            ? 1
            : -1;
    }
}
