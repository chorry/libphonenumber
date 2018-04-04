<?php

namespace chr\tests\phoneNumber;

use libphonenumber\PhoneNumberUtil;
use chr\phoneNumber\CarrierMapper;
use PHPUnit\Framework\TestCase;

class CarrierMapperTest extends TestCase
{
    public function testMatchWithOverride()
    {
        $phoneNumber = '79966998123';
        $number = PhoneNumberUtil::getInstance()->parse($phoneNumber, 'RU');
        $operator = CarrierMapper::getInstance()->getNameForValidNumber(
            $number,
            'en'
        );
        $this->assertSame('Sberbank Telecom', $operator);
    }

    public function testMatchWithoutOverride()
    {
        $phoneNumber = '79966098123';
        $number = PhoneNumberUtil::getInstance()->parse($phoneNumber, 'RU');
        $operator = CarrierMapper::getInstance()->getNameForValidNumber(
            $number,
            'en'
        );
        $this->assertSame($operator,'MegaFon');
    }
}
