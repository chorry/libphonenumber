<?php
declare(strict_types=1);

namespace chr\phoneNumber\tools;


class CarrierWriter
{
    private $headerWritten = false;
    public function __construct($resource)
    {
        $this->resource = $resource;
    }

    public function write(array $data)
    {
        if (!$this->headerWritten) {
            $this->headerWritten = true;
            $this->writeHeader($this->resource);
        }
        foreach ($data as $prefix => $carrier)
        {
            fwrite($this->resource, sprintf ("\t%s => '%s',\n", $prefix, str_replace("'", "\'", $carrier)));
        }
    }

    public function __destruct()
    {
        $this->writeFooter($this->resource);
    }


    private function writeHeader($resource)
    {
        fwrite($resource, <<<'TEXT'
<?php
return [

TEXT
);
    }

    private function writeFooter($resource)
    {
        fwrite($resource, '];' . PHP_EOL);
    }
}
