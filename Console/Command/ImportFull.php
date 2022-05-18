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

class ImportFull extends Command
{
    private const COMMAND_NAME = 'phpro:translations:import-full';
    private const ARGUMENT_CSV_FILE = 'csvfile';
    private const OPTION_CLEAR_CACHE = 'clear-cache';

    /**
     * @var ImportManagementInterface
     */
    private $importer;

    /**
     * @var Manager
     */
    private $cacheManager;

    /**
     * ImportFull constructor.
     *
     * @param ImportManagementInterface $importer
     * @param Manager $cacheManager
     */
    public function __construct(
        ImportManagementInterface $importer,
        Manager $cacheManager
    ) {
        $this->importer = $importer;
        $this->cacheManager = $cacheManager;
        parent::__construct();
    }

    /**
     * Configure import full command
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Import translation CSVs to the database based on exports.');
        $this->addArgument(self::ARGUMENT_CSV_FILE, InputArgument::REQUIRED);
        $this->addOption(self::OPTION_CLEAR_CACHE, null, InputOption::VALUE_NONE, 'Clear related caches');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $csvFile = $input->getArgument(self::ARGUMENT_CSV_FILE);

            $output->writeln(
                sprintf(
                    '<info>Importing CSV file %s</info>',
                    $csvFile
                )
            );

            $importStats = $this->importer->importFull($csvFile);

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
        }
    }
}
