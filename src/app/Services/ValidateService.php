<?php


namespace App\Services;


/**
 * Class ValidateService
 * @package App\Services
 */
class ValidateService
{
    /**
     * @param array $number
     * @return bool
     */
    public function isNumberValid(array $number): bool
    {

        return $this->isOnlyDigits($number) && count($number) == GameService::SEQ_LENGTH && $this->isUniqueSequence($number);
    }


    /**
     * @param array $number
     * @return bool
     */
    protected function isUniqueSequence(array $number): bool
    {
        return count($number) == count(array_flip($number));
    }

    protected function isOnlyDigits($number) {

        $numberStr = (int)implode('', $number);
        $newNumber = str_split($numberStr);
        return count($newNumber) == count($number);
    }
}