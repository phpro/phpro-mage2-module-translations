<?php
declare(strict_types=1);

namespace Phpro\Translations\Console\Command;

use Magento\Framework\App\Cache\Manager;
use Phpro\Translations\Api\ImportManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Import extends Command
{
    private const COMMAND_NAME = 'phpro:translations:import';
    private const ARGUMENT_CSV_FILE = 'csvfile';
    private const ARGUMENT_LOCALE = 'locale';
    private const OPTION_CLEAR_CACHE = 'clear-cache';

    /**
     * @var ImportManagementInterface
     */
    private $importer;

    /**
     * @var Manager
     */
    private $cacheManager;

    public function __construct(
        ImportManagementInterface $importer,
        Manager $cacheManager
    ) {
        $this->importer = $importer;
        $this->cacheManager = $cacheManager;
        parent::__construct();
    }

    /**
     * Configure import command
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Import Magento translation CSVs to the database.');
        $this->addArgument(self::ARGUMENT_CSV_FILE, InputArgument::REQUIRED);
        $this->addArgument(self::ARGUMENT_LOCALE, InputArgument::REQUIRED);
        $this->addOption(self::OPTION_CLEAR_CACHE, null, InputOption::VALUE_NONE, 'Clear related caches');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $exitCode = 0;

        try {
            $csvFile = $input->getArgument(self::ARGUMENT_CSV_FILE);
            $locale = $input->getArgument(self::ARGUMENT_LOCALE);

            $output->writeln(
                sprintf(
                    '<info>Importing CSV file %s for locale %s</info>',
                    $csvFile,
                    $locale
                )
            );

            $importStats = $this->importer->importMagentoCsv($csvFile, $locale);

            $output->writeln('<info>#created: ' . $importStats->getCreatedCount() . '</info>');
            $output->writeln('<info>#skipped: ' . $importStats->getSkippedCount() . '</info>');
            $output->writeln('<info>#failed: ' . $importStats->getFailedCount() . '</info>');

            if (false !== $input->hasOption(self::OPTION_CLEAR_CACHE)) {
                $cacheTypes = ['full_page', 'block_html', 'translate'];
                $this->cacheManager->clean(['full_page', 'block_html', 'translate']);
                $output->writeln('<info>Caches cleared: ' . implode(', ', $cacheTypes) . '</info>');
            }

            $output->writeln('<info>Done!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            $exitCode = 1;
        }

        return $exitCode;
    }
}
