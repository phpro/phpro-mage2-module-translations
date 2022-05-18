<?php
declare(strict_types=1);

namespace Phpro\Translations\Model\InlineTranslations;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlInterface;

/**
 * This class is responsible for generating and retrieving (frontend/js) translation.json files in the pub/media folder.
 * Mainly because of Magento Cloud doesn't allow write actions is pub/static folder.
 */
class FileManager
{
    private const JS_TRANSLATION_DIRECTORY = 'phpro_translations';
    private const JS_TRANSLATION_FILENAME_SUFFIX = 'translation.json';
    private const JS_DEPLOYED_VERSION_FILENAME = 'deployed_version.txt';

    /**
     * @var Json
     */
    private $serializer;
    /**
     * @var File
     */
    private $fileDriver;
    /**
     * @var Filesystem
     */
    private $filesystem;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var UrlInterface
     */
    private $urlGenerator;
    /**
     * @var TimezoneInterface
     */
    private $date;

    /**
     * FileManager constructor.
     *
     * @param Json $serializer
     * @param DirectoryList $directoryList
     * @param Filesystem $filesystem
     * @param File $driverFile
     * @param UrlInterface $urlGenerator
     * @param TimezoneInterface $dateTime
     */
    public function __construct(
        Json $serializer,
        DirectoryList $directoryList,
        Filesystem $filesystem,
        File $driverFile,
        UrlInterface $urlGenerator,
        TimezoneInterface $dateTime
    ) {
        $this->serializer = $serializer;
        $this->fileDriver = $driverFile;
        $this->filesystem = $filesystem;
        $this->directoryList = $directoryList;
        $this->urlGenerator = $urlGenerator;
        $this->date = $dateTime;
    }

    /**
     * This method will return a public URL containing the replacement url for the original js-translation.json located in pub/static/frontend/<ThemeName>/<name>/<locale>/... folders.
     *
     * E.g. https://www.webshop.com/media/phpro_translations/ThemeName/default/nl_BE/1642691554translation.json
     *
     * @param string $themePath
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     * @return string
     */
    public function getJsTranslationUrl(string $themePath): string
    {
        $versionFilePath = $this->getRelativeVersionFilePath($themePath);
        $reader = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        if (!$reader->isExist($versionFilePath)) {
            throw new LocalizedException(
                __('No %1 file found in directory %2', self::JS_DEPLOYED_VERSION_FILENAME, $versionFilePath)
            );
        }

        return \sprintf(
            '%s/%s/%s/%s',
            rtrim($this->urlGenerator->getBaseUrl(['_type' => UrlInterface::URL_TYPE_MEDIA]), '/'),
            self::JS_TRANSLATION_DIRECTORY,
            $themePath,
            $reader->readFile($versionFilePath) . self::JS_TRANSLATION_FILENAME_SUFFIX
        );
    }

    /**
     * This method saves the translations as json string in the media directory for related theme and locale.
     *
     * Also old generated translation files will be cleaned up and a new version string is created.
     * Example save path: pub/media/phpro_translations/ThemeName/default/nl_BE/1642691554translation.json
     *
     * @param array $translations
     * @param string $themePath
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function writeJsTranslationFileContent(array $translations, string $themePath): void
    {
        $savePath = sprintf(
            '%s/%s/%s',
            $this->directoryList->getPath(DirectoryList::MEDIA),
            self::JS_TRANSLATION_DIRECTORY,
            $themePath
        );

        // Ensure directory exists:
        $savePathExists = $this->fileDriver->isExists($savePath);
        if (!$savePathExists) {
            $this->fileDriver->createDirectory($savePath);
        }

        // Clean-up old translation json files:
        if ($savePathExists) {
            $this->cleanupOldTranslationFiles($themePath, $savePath);
        }

        $versionString = (string)$this->date->date()->getTimestamp();
        // Write new translation json file:
        $this->fileDriver->filePutContents(
            sprintf(
                '%s/%s',
                $savePath,
                $versionString . self::JS_TRANSLATION_FILENAME_SUFFIX
            ),
            $this->serializer->serialize($translations)
        );

        // Save version string
        $this->fileDriver->filePutContents(
            sprintf(
                '%s/%s',
                $savePath,
                self::JS_DEPLOYED_VERSION_FILENAME
            ),
            $versionString
        );
    }

    /**
     * @param string $themePath
     * @param string $savePath
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function cleanupOldTranslationFiles(string $themePath, string $savePath): void
    {
        $versionFilePath = $this->getRelativeVersionFilePath($themePath);
        $reader = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        if (!$reader->isExist($versionFilePath)) {
            return;
        }

        $currentTranslationFile = $reader->readFile($versionFilePath) . self::JS_TRANSLATION_FILENAME_SUFFIX;
        foreach ($this->fileDriver->readDirectory($savePath) as $file) {
            // phpcs:disable
            if ('json' !== pathinfo($file, PATHINFO_EXTENSION) ||
                $currentTranslationFile === pathinfo($file, PATHINFO_BASENAME)) {
                continue;
            }
            // phpcs:enable
            $this->fileDriver->deleteFile($file);
        }
    }

    /**
     * @param string $themeLocalePath
     * @return string
     */
    private function getRelativeVersionFilePath(string $themeLocalePath): string
    {
        return sprintf(
            '%s/%s/%s',
            self::JS_TRANSLATION_DIRECTORY,
            $themeLocalePath,
            self::JS_DEPLOYED_VERSION_FILENAME
        );
    }
}
