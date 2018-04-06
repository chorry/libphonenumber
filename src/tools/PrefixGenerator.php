<?php
declare(strict_types=1);

namespace chr\phoneNumber\tools;


use InvalidArgumentException;

class PrefixGenerator
{
    /**
     * @param $from
     * @param $to
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public function generatePrefixes(string $from, string $to): array
    {
        if (\strlen($from) !== \strlen($to)) {
            throw new InvalidArgumentException(sprintf('Ranges must be of equal length, got %s and %s', $from, $to));
        }
        if ($from === $to) {
            return [$from];
        }

        $ranges = [];
        $startNumber = $from;


        while (1) {
            if (null === $startNumber) {
                $startNumber = $from;
            }
            $decimalPlace = $this->findDecimalPeakValue($startNumber, $to);

            $bigNumber = $this->getNextBigNumberByDecimalPlace($to, $decimalPlace);

            if ($this->checkForNinesAndZeroes($bigNumber, $to, $decimalPlace)) {
                $prefixStart = substr($startNumber, 0, \strlen($startNumber) - $decimalPlace);
                $prefixEnd = substr($bigNumber, 0, \strlen($bigNumber) - $decimalPlace);
                $ranges = array_merge($ranges, $this->pullUpMinorDigits($prefixStart, $prefixEnd));
                $ranges[] = $prefixEnd;
                break;
            }

            $ranges = array_merge($ranges, $this->pullUpMinorDigits($startNumber, $bigNumber));
            if ($bigNumber === $to) {
                $ranges[] = $to;
                break;
            }
            $startNumber = $bigNumber;
        }
        return $ranges;
    }

    private function checkForNinesAndZeroes($candidate, $end, $decimalPlace): bool
    {
        $candidateSubString = substr($candidate, \strlen($candidate) - $decimalPlace);
        $endSubString = substr($end, \strlen($end) - $decimalPlace);
        $candidateHasAllZeroes = preg_match('/^[0]+$/', $candidateSubString);
        $endHasAllNines = preg_match('/^[9]+$/', $endSubString);

        return $candidateHasAllZeroes === $endHasAllNines && $endHasAllNines === 1;
    }

    /**
     * @param string $end
     * @param        $decimalPlace
     *
     * @return null|string
     */
    private function getNextBigNumberByDecimalPlace(string $end, $decimalPlace): ?string
    {
        if ($decimalPlace >= 0) {
            $candidate = (string) (floor($end / (10 ** $decimalPlace)) * (10 ** $decimalPlace));
            $candidate = str_pad($candidate, \strlen($end), '0', STR_PAD_LEFT);
            return $candidate;
        }
        return null;
    }

    /**
     * @param string $numberFrom
     * @param string $peakValue
     *
     * @return array
     * @throws InvalidArgumentException
     */
    private function pullUpMinorDigits(string $numberFrom, string $peakValue): array
    {
        if (\strlen($numberFrom) !== \strlen($peakValue)) {
            throw new InvalidArgumentException(sprintf('%s and %s must be of equal length', $numberFrom, $peakValue));
        }

        $ranges = [];
        $decimalsAmount = \strlen($numberFrom);
        $workingValue = $numberFrom;
        $shiftDecimalUp = false;

        for ($decimalPosition = $decimalsAmount - 1; $decimalPosition >= 0; $decimalPosition--) {
            if ($shiftDecimalUp) {
                $workingValue = $this->shiftDecimalUp($workingValue, $decimalPosition);
                $shiftDecimalUp = false;
            }

            $currentDecimalValue = $workingValue[$decimalPosition];
            $currentDecimalPeakValue = $peakValue[$decimalPosition];
            $prefix = null;

            if ($currentDecimalValue === $currentDecimalPeakValue && $currentDecimalValue === '0') {
                //just ignore
            } else {
                $prefix = substr($workingValue, 0, $decimalPosition);
                if ($currentDecimalPeakValue === '0') {
                    $currentDecimalPeakValue = 10;
                }
                for ($j = $currentDecimalValue; $j < $currentDecimalPeakValue; $j++) {
                    $ranges[] = sprintf('%s%s', $prefix, $j);
                }
                if ($currentDecimalPeakValue === 10) {
                    $shiftDecimalUp = true;
                }
            }

            if ('' !== (string)$prefix && strpos($peakValue, $prefix) === 0) {
                break;
            }
        }

        return $ranges;
    }

    /**
     * @param $stringValue
     * @param $decimalPositionToShift
     *
     * @return string
     */
    private function shiftDecimalUp($stringValue, $decimalPositionToShift): string
    {
        $expectedValue = $stringValue[$decimalPositionToShift] + 1;

        if ($expectedValue === 10) {
            $newPosition = $decimalPositionToShift - 1;
            $expectedValue = 0;
            $stringValue = $this->shiftDecimalUp($stringValue, $newPosition);
        }
        $stringValue[$decimalPositionToShift] = (string) $expectedValue;

        return $stringValue;
    }

    /**
     * @param     $one
     * @param     $two
     * @param int $offset
     *
     * @return int
     * @throws InvalidArgumentException
     */
    private function findDecimalPeakValue($one, $two, $offset = 0): int
    {
        if ($one > $two) {
            throw new InvalidArgumentException('First argument must less than second');
        }

        $length = \strlen($one);
        for ($i = $offset; $i < $length; $i++) {
            if ($one[$i] !== $two[$i]) {
                return $length - $i - 1;
                if ($one[$i] === '0' && $two[$i] === '9') {
                    // We need to look deeper
                } else {
                    return $length - $i - 1;
                }
            }
        }

        return -1;
    }

}
