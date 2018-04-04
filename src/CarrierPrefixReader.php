<?php
declare(strict_types=1);

namespace chr\phoneNumber;

use InvalidArgumentException;
use libphonenumber\PhoneNumber;
use libphonenumber\prefixmapper\PrefixFileReader;
use libphonenumber\prefixmapper\MappingFileProvider;

class CarrierPrefixReader extends PrefixFileReader
{
    /**
     * @var PrefixFileReader
     */
    private $originalReader;

    /**
     * @inheritdoc
     */
    public function __construct($phonePrefixDataDirectory, PrefixFileReader $reader)
    {
        parent::__construct($phonePrefixDataDirectory);
        $this->originalReader = $reader;
    }

    /**
     * @inheritdoc
     * @throws \InvalidArgumentException
     */
    protected function loadMappingFileProvider()
    {
        $mapPath = $this->phonePrefixDataDirectory . DIRECTORY_SEPARATOR . 'Map.php';
        if (!file_exists($mapPath)) {
            throw new InvalidArgumentException('Invalid data directory');
        }

        $map = require $mapPath;

        $this->mappingFileProvider = new MappingFileProvider($map);
    }

    /**
     * @inheritdoc
     */
    public function getDescriptionForNumber(PhoneNumber $number, $language, $script, $region)
    {
        $value = parent::getDescriptionForNumber(
            $number,
            $language,
            $script,
            $region
        );

        return $value === '' ? $this->originalReader->getDescriptionForNumber(
            $number,
            $language,
            $script,
            $region
        ) : $value;
    }
}
