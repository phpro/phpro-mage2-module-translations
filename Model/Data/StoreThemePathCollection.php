<?php

declare(strict_types=1);

namespace Phpro\Translations\Model\Data;

use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

class StoreThemePathCollection implements IteratorAggregate, Countable
{
    /**
     * @var StoreThemePath[]
     */
    private array $storeThemeItems;

    public function __construct(StoreThemePath ...$storeThemeItems)
    {
        $this->storeThemeItems = $storeThemeItems;
    }

    /**
     * @param StoreThemePath $storeThemePath
     */
    public function add(StoreThemePath $storeThemePath)
    {
        $this->storeThemeItems[] = $storeThemePath;
    }

    /**
     * @return ArrayIterator|StoreThemePath[]
     */
    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->storeThemeItems);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return \count($this->storeThemeItems);
    }
}
