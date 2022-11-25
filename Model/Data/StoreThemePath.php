<?php

declare(strict_types=1);

namespace Phpro\Translations\Model\Data;

class StoreThemePath
{
    private int $storeId;
    private string $path;

    public function __construct(int $storeId, string $path)
    {
        $this->storeId = $storeId;
        $this->path = $path;
    }

    /**
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->storeId;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
