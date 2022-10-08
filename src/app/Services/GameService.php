<?php


namespace App\Services;


/**
 * Class GameService
 * @package App\Services
 */
class GameService
{

    public const SEQ_LENGTH = 4;

    /**
     * @var int
     */
    protected $bulls = 0;
    /**
     * @var int
     */
    protected $caws = 0;

    /**
     * @return string|null
     */
    public function generateGameId()
    {
        return uuid_create();
    }


    /**
     * @param int $length
     * @return array
     */
    public function generateUniqueDigitSequence($length = GameService::SEQ_LENGTH): array
    {
        $seq = $this->sequence($length);

        while (!$this->isOk($seq)) {
            $seq = $this->sequence($length);
        }

        return $seq;
    }

    /**
     * @param int $length
     * @return array
     */
    protected function sequence(int $length): array
    {

        $seq = [];
        $available = [1, 2, 3, 4, 5, 6, 7, 8, 9];
        shuffle($available);

        for ($i = 0; $i < $length; $i++) {
            $seq[] = $available[array_rand($available, 1)];;
        }

        return $seq;
    }

    /**
     * @param array $number
     * @param array $suggestion
     * @return array
     */
    public function calculateCawsAndBulls(array $number, array $suggestion): array
    {

        for ($i = 0; $i < count($suggestion); $i++) {
            if (in_array($suggestion[$i], $number) && $suggestion[$i] == $number[$i]) {
                $this->bulls++;
                unset($number[$i]);
            } else if (in_array($suggestion[$i], $number)) {
                $this->caws++;
            }
        }

        return ['caws' => $this->caws, 'bulls' => $this->bulls];
    }

    /**
     * @param array $seq
     * @return bool
     */
    protected function isOk(array $seq): bool
    {

        $uniqueSeq = array_unique($seq);
        if (count($uniqueSeq) < count($seq)) {
            return false;
        }
        for ($i = 0; $i < count($seq); $i++) {
            if (isset($seq[$i - 1])) {

                if ($seq[$i - 1] == 1 && $seq[$i] != 8) {
                    return false;
                }
                if ($seq[$i - 1] == 8 && $seq[$i] != 1) {
                    return false;
                }
            }

            if ($i % 2 == 0 && ($seq[$i] == 4 || $seq[$i] == 5)) {
                return false;
            }
        }

        return true;
    }

}