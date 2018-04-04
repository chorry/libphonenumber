<?php
declare(strict_types=1);

namespace chr\phoneNumber;

use libphonenumber\PhoneNumberToCarrierMapper;

class CarrierMapper extends PhoneNumberToCarrierMapper
{
    /**
     * @inheritdoc
     */
    public function __construct($phonePrefixDataDirectory)
    {
        parent::__construct($phonePrefixDataDirectory);
        $this->prefixFileReader =
            new CarrierPrefixReader(
                __DIR__ . $phonePrefixDataDirectory, $this->prefixFileReader
            );
    }
}
