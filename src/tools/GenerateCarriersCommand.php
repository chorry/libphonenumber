<?php
declare(strict_types=1);

namespace chr\phoneNumber\tools;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCarriersCommand extends Command
{
    private const FILE = 'file';
    private const DEFCODE = 'def';

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this
            ->setName('generate-carriers')
            ->setDescription('Generates carriers list from DEF-codes')
            ->setHelp('Generates carriers list by downloading file with DEF-codes');
        $this->addOption(
            self::FILE,
            null,
            InputOption::VALUE_REQUIRED,
            'File with DEF-codes'
        );
        $this->addOption(
            self::DEFCODE,
            null,
            InputOption::VALUE_OPTIONAL,
            'DEF code string'
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fileSource = $input->getOption(self::FILE);

        $operators = $this->extractRecordsFromFile(
            fopen($fileSource, 'rb'),
            (string) $input->getOption(self::DEFCODE)
        );

        $prefixGenerator = new PrefixGenerator();
        foreach ($operators as $operator) {
            print (string) $operator . "\n";
            foreach ($operator->getRange() as $range) {
                $prefixList = $prefixGenerator->generatePrefixes($range['from'], $range['to']);
                $prefixList = array_fill_keys($prefixList, $operator->getName());
                print_r($prefixList);
            }
        }
    }


    /**
     * @param resource $fh
     * @param string   $code
     *
     * @return DefOperator[]
     */
    private function extractRecordsFromFile($fh, $code = ''): array
    {
        $process = true;
        $line = 0;

        /** @var DefOperator[] $defOperators */
        $defOperators = [];

        while (($recordLine = fgets($fh)) && $process) {
            ++$line;

            [$defCode, $rangeStart, $rangeEnd, $capacity, $operator, $geo] = str_getcsv($recordLine, ';');
            $operator = $this->processStringLine($operator);
            if ($this->filterDefCode($defCode, $code)) {
                if (!isset($defOperators[$operator])) {
                    $defOperators[$operator] = new DefOperator($operator);
                }
                $defOperators[$operator]->addRange($code . $rangeStart, $code . $rangeEnd);
            }
            if ($code !== '' && $defCode !== $code) {
                //$process = false;
            }

        }
        return $defOperators;
    }

    private function processStringLine(string $line): string
    {
        return iconv('cp1251', 'utf8', $line);
    }

    private function filterDefCode(string $found, string $expected): bool
    {
        return $expected === '' || $found === $expected;
    }

}
