<?php
declare(strict_types=1);

namespace chr\phoneNumber\tools;


use PHPUnit\Framework\TestCase;

class PrefixGeneratorTest extends TestCase
{

    /**
     * @dataProvider dataSource
     *
     * @param array $expected
     * @param array $range
     */
    public function testGeneratePrefixes(array $expected, array $range)
    {
        $g = new PrefixGenerator();
        $list = $g->generatePrefixes($range[0], $range[1]);

        $this->assertEquals($expected, $list);
    }

    /**
     * @return array
     */
    public function dataSource(): array
    {
        return [
            'simple' => [
                ['0','10','11','12','13','14','15'],
                ['00','15'],
            ],
            '1' => [
                ['000','001','002','003','0040','0041','0042','0043','0044'],
                ['0000000','0044999'],
            ],
            '2' => [
                ['109','110','111','112','113','114','1150000'],
                ['1090000','1150000'],
            ],
            '3' => [
                [
                    '082',
                    '083',
                    '084',
                    '085',
                    '086',
                    '087',
                    '088',
                    '089',
                    '0900',
                    '0901',
                    '0902',
                    '0903',
                    '0904',
                    '0905',
                    '0906',
                    '0907',
                    '0908',
                    '09090',
                    '09091',
                    '09092',
                    '09093',
                    '09094',
                    '09095',
                    '09096',
                    '09097',
                    '09098',
                    '090990',
                    '090991',
                    '090992',
                    '090993',
                    '090994',
                    '090995',
                    '090996',
                    '090997',
                    '0909980',
                    '0909981',
                    '0909982',
                    '0909983',
                    '0909984',
                    '0909985',
                    '0909986',
                    '0909987',
                    '0909988'
                ],
                [ '0820000', '0909988'],
            ],
        ];
    }

}
