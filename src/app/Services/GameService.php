<?php


namespace App\Services;


use Illuminate\Support\Facades\Storage;

/**
 * Class GameService
 * @package App\Services
 */
class GameService
{

    public const SEQ_LENGTH = 4;

    public const PATH_TO_STORE_IN_PROGRESS = 'private/in-progress';

    public const PATH_TO_STORE_COMPLETED = 'private/complete/';

    public const TOP_N_RESULTS = 10;

    /**
     * @var int
     */
    protected $bulls = 0;
    /**
     * @var int
     */
    protected $caws = 0;

    /** @var int  */
    protected $suggestionsCount = 0;

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

    public function storeGameResult(string $gameId) {

        $storageSuggestions = Storage::disk('local')->get(GameService::PATH_TO_STORE_IN_PROGRESS.$gameId.'.txt');
        if($storageSuggestions) {
            $storageSuggestions = (int)$storageSuggestions + 1;
        } else {
            $storageSuggestions = 1;
        }

        Storage::disk('local')->put(GameService::PATH_TO_STORE_IN_PROGRESS.$gameId.'.txt', $storageSuggestions);

        $this->suggestionsCount = $storageSuggestions;
    }

    public function updateStoreOnGameComplete($gameId) {

        $files = Storage::files(GameService::PATH_TO_STORE_COMPLETED);
        $currentFiles = $this->getStats();
        if(count($files) < GameService::TOP_N_RESULTS) {
            Storage::disk('local')->put(GameService::PATH_TO_STORE_COMPLETED.$gameId.'.txt', $this->suggestionsCount);
        } else {

            $last = end($currentFiles);
            if($last > Storage::disk('local')->get(GameService::PATH_TO_STORE_IN_PROGRESS.$gameId.'.txt')) {
                Storage::disk('local')->put(GameService::PATH_TO_STORE_COMPLETED.$gameId.'.txt', $this->suggestionsCount);
                $lastKey = key($last);
                Storage::disk('local')->delete($lastKey);
            }
        }

        Storage::disk('local')->delete(GameService::PATH_TO_STORE_IN_PROGRESS.$gameId.'.txt');
    }

    public function getStats() {

        $files = Storage::files(GameService::PATH_TO_STORE_COMPLETED);
        $currentFiles = [];
        foreach($files as $file) {
            $currentFiles[$file] = Storage::disk('local')->get($file);
        }
        asort($currentFiles);

        return $currentFiles;
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