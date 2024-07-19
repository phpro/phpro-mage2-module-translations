<?php
declare(strict_types=1);

namespace Phpro\Translations\Console\Command;

use Magento\Setup\Module\I18n\Dictionary\Options\ResolverFactory;
use Magento\Setup\Module\I18n\Parser\Adapter\Html;
use Magento\Setup\Module\I18n\Parser\Adapter\Js;
use Magento\Setup\Module\I18n\Parser\Adapter\Php;
use Magento\Setup\Module\I18n\Parser\Adapter\Php\Tokenizer;
use Magento\Setup\Module\I18n\Parser\Adapter\Php\Tokenizer\PhraseCollector;
use Magento\Setup\Module\I18n\Parser\Adapter\Xml;
use Magento\Setup\Module\I18n\Parser\Parser;
use Phpro\Translations\Api\TranslationDataManagementInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PrepareKeysCommand extends Command
{
    /**
     * @var ResolverFactory
     */
    private $optionResolverFactory;

    /**
     * @var Parser
     */
    private $parser;

    /**
     * @var TranslationDataManagementInterface
     */
    private $translationDataManagement;

    public function __construct(
        ResolverFactory $optionResolverFactory,
        Parser $parser,
        TranslationDataManagementInterface $translationDataManagement,
        string $name = null
    ) {
        $this->optionResolverFactory = $optionResolverFactory;
        $this->parser = $parser;

        parent::__construct($name);
        $this->translationDataManagement = $translationDataManagement;
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->setName('phpro:translations:prepare-keys');
        $this->setDescription('Prepare translations keys');
        parent::configure();
    }

    /**
     * CLI command description
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $phraseCollector = new PhraseCollector(new Tokenizer());
            $adapters = [
                'php' => new Php($phraseCollector),
                'html' => new Html(),
                'js' => new Js(),
                'xml' => new Xml(),
            ];
            $optionResolver = $this->optionResolverFactory->create(BP, false);
            foreach ($adapters as $type => $adapter) {
                $this->parser->addAdapter($type, $adapter);
            }
            $this->parser->parse($optionResolver->getOptions());
            $phraseList = $this->parser->getPhrases();

            foreach ($phraseList as $phrase) {
                $this->translationDataManagement->prepare($phrase->getPhrase(), $phrase->getTranslation());
            }

            $output->writeln('<info>Keys successfully created.</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>' . $e->getMessage() . '</error>');
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
