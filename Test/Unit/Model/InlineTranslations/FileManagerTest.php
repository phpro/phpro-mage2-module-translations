<?php
declare(strict_types=1);

namespace Phpro\Translations\Test\Unit\Model\InlineTranslations;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;
use Phpro\Translations\Model\InlineTranslations\FileManager;
use PHPUnit\Framework\TestCase;

class FileManagerTest extends TestCase
{
    private const MEDIA_DIR_ABSOLUTE = '/app/pub/media';
    /**
     * @var Json|\PHPUnit\Framework\MockObject\MockObject
     */
    private $serializer;
    /**
     * @var DirectoryList|\PHPUnit\Framework\MockObject\MockObject
     */
    private $directoryList;
    /**
     * @var Filesystem|\PHPUnit\Framework\MockObject\MockObject
     */
    private $filesystem;
    /**
     * @var File|\PHPUnit\Framework\MockObject\MockObject
     */
    private $fileDriver;
    /**
     * @var UrlInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $urlGenerator;
    /**
     * @var TimezoneInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $date;
    /**
     * @var FileManager
     */
    private $fileManager;

    public function setUp(): void
    {
        $this->serializer = $this->createMock(Json::class);
        $this->directoryList = $this->createMock(DirectoryList::class);
        $this->directoryList->method('getPath')->willReturn(self::MEDIA_DIR_ABSOLUTE);
        $this->filesystem = $this->createMock(Filesystem::class);
        $this->fileDriver = $this->createMock(File::class);
        $this->urlGenerator = $this->createMock(UrlInterface::class);
        $this->date = $this->createMock(TimezoneInterface::class);

        $this->fileManager = new FileManager(
            $this->serializer,
            $this->directoryList,
            $this->filesystem,
            $this->fileDriver,
            $this->urlGenerator,
            $this->date
        );
    }

    public function testWriteJsTranslationFileContentWithoutBaseDirYet()
    {
        $themePath = 'Theme/default/nl_BE';
        $now = new \DateTime('now');
        $version = (string)$now->getTimestamp();
        $savePath = self::MEDIA_DIR_ABSOLUTE . '/phpro_translations/' . $themePath;

        $this->fileDriver
            ->expects(static::once())
            ->method('isExists')
            ->with(self::MEDIA_DIR_ABSOLUTE . '/phpro_translations/' . $themePath)
            ->willReturn(false);
        $this->fileDriver
            ->expects(static::once())
            ->method('createDirectory')
            ->with($savePath);
        $this->date
            ->method('date')
            ->willReturn($now);
        $this->serializer
            ->method('serialize')
            ->willReturn('{"keyValue":"transValue"}');
        $this->fileDriver
            ->method('filePutContents')
            ->withConsecutive(
                [$savePath . '/' . $version . 'translation.json', '{"keyValue":"transValue"}'],
                [$savePath . '/deployed_version.txt', $version],
            );

        $this->fileManager->writeJsTranslationFileContent(
            ['keyValue' => 'transValue'],
            'Theme/default/nl_BE'
        );
    }

    public function testWriteJsTranslationFileContentWithBaseDirAndCleanup()
    {
        $themePath = 'Theme/default/nl_BE';
        $now = new \DateTime('now');
        $version = (string)$now->getTimestamp();
        $savePath = self::MEDIA_DIR_ABSOLUTE . '/phpro_translations/' . $themePath;
        $relativePath = 'phpro_translations/' . $themePath;
        $oldVersion = '123456';

        $this->fileDriver
            ->expects(static::once())
            ->method('isExists')
            ->with(self::MEDIA_DIR_ABSOLUTE . '/phpro_translations/' . $themePath)
            ->willReturn(true);

        $reader = $this->createMock(Read::class);
        $this->filesystem
            ->method('getDirectoryRead')
            ->willReturn($reader);
        $reader
            ->expects(static::once())
            ->method('isExist')
            ->with($relativePath . '/deployed_version.txt')
            ->willReturn(true);
        $reader
            ->expects(static::once())
            ->method('readFile')
            ->with($relativePath . '/deployed_version.txt')
            ->willReturn($oldVersion);
        $this->fileDriver
            ->expects(static::once())
            ->method('readDirectory')
            ->with($savePath)
            ->willReturn([
                $savePath . '/deployed_version.txt',
                $savePath . '/other.file',
                $savePath . '/123456translation.json',
                $savePath . '/654321translation.json',
                $savePath . '/001122translation.json',
            ]);
        $this->fileDriver
            ->expects(static::exactly(2))
            ->method('deleteFile')
            ->withConsecutive(
                [$savePath . '/654321translation.json'],
                [$savePath . '/001122translation.json']
            );
        $this->date
            ->method('date')
            ->willReturn($now);
        $this->serializer
            ->method('serialize')
            ->willReturn('{"keyValue":"transValue"}');
        $this->fileDriver
            ->method('filePutContents')
            ->withConsecutive(
                [$savePath . '/' . $version . 'translation.json', '{"keyValue":"transValue"}'],
                [$savePath . '/deployed_version.txt', $version],
            );

        $this->fileManager->writeJsTranslationFileContent(
            ['keyValue' => 'transValue'],
            'Theme/default/nl_BE'
        );
    }
}
