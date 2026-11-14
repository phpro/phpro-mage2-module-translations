<?php
declare(strict_types=1);

namespace Phpro\Translations\Test\Unit\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Phpro\Translations\Ui\Component\Listing\Column\TranslationActions;
use PHPUnit\Framework\TestCase;

class TranslationActionsTest extends TestCase
{
    public function testPrepareDataSourceBuildsEditAndDeleteUrls(): void
    {
        $dataSource = $this->prepare(TranslationActions::class);

        $actions = $dataSource['data']['items'][0]['actions'];
        static::assertSame('phpro_translations/translation/edit?key_id=7', $actions['edit']['href']);
        static::assertSame('phpro_translations/translation/delete?key_id=7', $actions['delete']['href']);
    }

    public function testPrepareDataSourceWorksOnSubclass(): void
    {
        $subclass = new class($this->createMock(ContextInterface::class), $this->createMock(UiComponentFactory::class), $this->urlBuilder(), [], ['name' => 'actions']) extends TranslationActions {
        };

        $dataSource = $subclass->prepareDataSource($this->dataSource());

        $actions = $dataSource['data']['items'][0]['actions'];
        static::assertSame('phpro_translations/translation/edit?key_id=7', $actions['edit']['href']);
        static::assertSame('phpro_translations/translation/delete?key_id=7', $actions['delete']['href']);
    }

    private function prepare(string $class): array
    {
        $column = new $class(
            $this->createMock(ContextInterface::class),
            $this->createMock(UiComponentFactory::class),
            $this->urlBuilder(),
            [],
            ['name' => 'actions']
        );

        return $column->prepareDataSource($this->dataSource());
    }

    private function dataSource(): array
    {
        return ['data' => ['items' => [['key_id' => 7]]]];
    }

    private function urlBuilder(): UrlInterface
    {
        $urlBuilder = $this->createMock(UrlInterface::class);
        $urlBuilder->method('getUrl')->willReturnCallback(
            static fn (string $path, array $params): string => $path . '?key_id=' . $params['key_id']
        );

        return $urlBuilder;
    }
}
