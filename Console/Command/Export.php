<?php
declare(strict_types=1);

namespace Phpro\Translations\Console\Command;

use Phpro\Translations\Api\ExportManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Export extends Command
{
    private const COMMAND_NAME = 'phpro:translations:export';
    private const ARGUMENT_LOCALES = 'locales';

    /**
     * @var ExportManagementInterface
     */
    private $exporter;

    public function __construct(
        ExportManagementInterface $exporter
    ) {
        $this->exporter = $exporter;
        parent::__construct();
    }

    /**
     * Configure export command
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Export translations from database to CSV.');
        $this->addArgument(self::ARGUMENT_LOCALES, InputArgument::IS_ARRAY, 'Locales (multiple locales with a space');

        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $locales = $input->getArgument(self::ARGUMENT_LOCALES);

            $output->writeln(
                sprintf(
                    '<info>Exporting translations to CSV file</info>'
                )
            );

            $exportStats = $this->exporter->export($locales);

            $output->writeln('<info>Csv file: ' . $exportStats->getFileName() . '</info>');
            $output->writeln('<info>Total written rows: ' . $exportStats->getTotalRows() . '</info>');

            $output->writeln('<info>Done!</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }
}
