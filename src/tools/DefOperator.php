<?php
declare(strict_types=1);

namespace chr\phoneNumber\tools;


class DefOperator
{
    private $range = [];
    private $index;
    private $name;

    public function __construct($name = 'noname')
    {
        $this->name = $name;
    }

    public function addRange(string $from, string $to): void
    {
        if (
            isset($this->range[$this->index]['to'])
            && (int) $this->range[$this->index]['to'] + 1 === (int) $from
        ) {
            $this->prolongRange($to);
        } else {
            $this->addNewRange($from, $to);
        }
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    private function prolongRange(string $to): void
    {
        $this->range[\count($this->range) - 1]['to'] = $to;
    }

    private function addNewRange(string $from, string $to): void
    {
        $this->range[] = [
            'from' => $from,
            'to' => $to
        ];
        if ($this->index !== null) {
            $this->index++;
        } else {
            $this->index = 0;
        }
    }

    public function __toString()
    {
        return $this->name . ' with ' . \count($this->range) . ' ranges';
    }

    public function getRange(): array
    {
        return $this->range;
    }
}
