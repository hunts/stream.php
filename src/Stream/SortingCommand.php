<?php
/**
 * This file is part of the Stream package.
 *
 * (c) Minghang Chen <chen@minghang.dev>
 */

namespace Stream;

/**
 * Represents sorting information which contains the SortOrder and an optional comparator object.
 */
class SortingCommand
{
    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @var Comparator
     */
    private $comparator;

    /**
     * @param int $sortOrder Accepts one of SortOrder::ASC or SortOrder::DESC.
     * @param Comparator $comparator [optional] Comparator to compare two items which performing sort.
     */
    public function __construct(int $sortOrder = SortOrder::ASC, Comparator $comparator = null)
    {
        $this->comparator = $comparator;
        $this->sortOrder = $sortOrder;
    }

    /**
     * Returns SortOrder.
     *
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * Returns comparator object.
     *
     * @return Comparator|null
     */
    public function getComparator(): ?Comparator
    {
        return $this->comparator;
    }
}
