<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\Data;

use ArrayIterator;
use Countable;
use IteratorAggregate;

class InlineGenerateStatsCollection implements IteratorAggregate, Countable
{
    /**
     * @var InlineGenerateStats[]
     */
    private $statsItems;

    public function __construct(InlineGenerateStats ...$statsItems)
    {
        $this->statsItems = $statsItems;
    }

    public function add(InlineGenerateStats $stats)
    {
        $this->statsItems[] = $stats;
    }

    /**
     * @return ArrayIterator|InlineGenerateStats[]
     */
    public function getIterator()
    {
        return new ArrayIterator($this->statsItems);
    }

    public function count(): int
    {
        return \count($this->statsItems);
    }

    public function toArray(): array
    {
        $result = [];
        foreach ($this->statsItems as $stats) {
            $result[] = sprintf(
                '<b>%s</b> translations for store <b>%s</b>',
                $stats->getAmountGenerated(),
                $stats->getStoreInformation()
            );
        }

        return $result;
    }
}
