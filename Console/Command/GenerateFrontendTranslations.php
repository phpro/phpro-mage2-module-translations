<?php
declare(strict_types=1);

namespace Phpro\Translations\Console\Command;

use Phpro\Translations\Model\Data\InlineGenerateStatsCollection;
use Phpro\Translations\Model\InlineTranslationsGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateFrontendTranslations extends Command
{
    private const COMMAND_NAME = 'phpro:translations:generate-frontend-translations';

    /**
     * @var InlineTranslationsGenerator
     */
    private $inlineTranslationsGenerator;

    /**
     * GenerateFrontendTranslations constructor.
     *
     * @param InlineTranslationsGenerator $inlineTranslations
     */
    public function __construct(
        InlineTranslationsGenerator $inlineTranslations
    ) {
        $this->inlineTranslationsGenerator = $inlineTranslations;
        parent::__construct();
    }

    /**
     * Configure frontend translations command
     */
    protected function configure(): void
    {
        $this->setName(self::COMMAND_NAME);
        $this->setDescription('Re-generate the frontend translations (js-translations.json) for given store view.');
        $this->addArgument('storeId', InputArgument::OPTIONAL, 'Leave empty for all store views', 0);
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output): void
    {
        try {
            $storeId = (int)$input->getArgument('storeId');
            $statsCollection = $this->generatedTranslations($storeId);

            $table = new Table($output);
            $table->setHeaders(['# Translations', 'Store view information', 'Store id']);
            foreach ($statsCollection as $stats) {
                $table->addRow([
                    $stats->getAmountGenerated(),
                    $stats->getStoreInformation(),
                    $stats->getStoreId(),
                ]);
            }
            $output->writeln('<info>Inline frontend translations successfully re-generated for:</info>');
            $table->render();
            $output->writeln('<info>Please clean full_page and block_html caches manually</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
        }
    }

    /**
     * @param int $storeId
     * @return InlineGenerateStatsCollection
     */
    private function generatedTranslations(int $storeId): InlineGenerateStatsCollection
    {
        if (0 === $storeId) {
            return $this->inlineTranslationsGenerator->forAll();
        }

        return $this->inlineTranslationsGenerator->forStores([$storeId]);
    }
}
