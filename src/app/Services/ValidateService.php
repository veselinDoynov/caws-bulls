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
        return $this->validateSequence($number) && $this->isUniqueSequence($number);
    }


    /**
     * @param array $number
     * @return bool
     */
    protected function isUniqueSequence(array $number): bool
    {
        return count($number) == count(array_flip($number));
    }

    /**
     * @param array $number
     * @return false|int
     */
    protected function validateSequence(array $number): bool {

        $sequence = (string)implode('', $number);
        return preg_match('/^[0-9]{'.GameService::SEQ_LENGTH.'}$/', $sequence);
    }
}