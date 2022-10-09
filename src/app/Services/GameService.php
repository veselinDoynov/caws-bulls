<?php


namespace App\Services;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Class GameService
 * @package App\Services
 */
class GameService
{

    public const SEQ_LENGTH = 4;

    public const PATH_TO_STORE_IN_PROGRESS = 'private/in-progress/';

    public const PATH_TO_STORE_COMPLETED = 'private/complete/';

    public const TOP_N_RESULTS = 10;

    public const NEIGHBORS = [1, 8];

    public const ODD_FORBIDDEN = [4, 5];

    public const AVAILABLE_NUMBERS = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];


    /** @var int */
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
     * @param array $seq
     * @return bool
     */
    public function isOk(array $seq): bool
    {

        $uniqueSeq = array_unique($seq);
        if (count($uniqueSeq) < count($seq)) {
            return false;
        }
        for ($i = 0; $i < count($seq); $i++) {

            if (in_array($seq[$i], GameService::NEIGHBORS)) {
                $surroundLeft = isset($seq[$i - 1]) ? $seq[$i - 1] : null;
                $surroundRight = isset($seq[$i + 1]) ? $seq[$i + 1] : null;
                if (!in_array($surroundLeft, GameService::NEIGHBORS) && !in_array($surroundRight, GameService::NEIGHBORS)) {
                    return false;
                }
            }

            if (($i + 1) % 2 == 0 && in_array($seq[$i], GameService::ODD_FORBIDDEN)) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param int $length
     * @return array
     */
    public function sequence(int $length): array
    {

        $seq = [];
        $available = GameService::AVAILABLE_NUMBERS;
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

        $caws = 0;
        $bulls = 0;
        for ($i = 0; $i < count($suggestion); $i++) {
            if (in_array($suggestion[$i], $number) && $suggestion[$i] == $number[$i]) {
                $bulls++;
                unset($number[$i]);
            } else if (in_array($suggestion[$i], $number)) {
                $caws++;
            }
        }

        return ['caws' => $caws, 'bulls' => $bulls];
    }

    public function storeGameResult(string $gameId)
    {

        $storageSuggestions = Storage::disk('local')->get(GameService::PATH_TO_STORE_IN_PROGRESS . $gameId . '.txt');
        if ($storageSuggestions) {
            $storageSuggestions = (int)$storageSuggestions + 1;
        } else {
            $storageSuggestions = 1;
        }

        Storage::disk('local')->put(GameService::PATH_TO_STORE_IN_PROGRESS . $gameId . '.txt', $storageSuggestions);

        $this->suggestionsCount = $storageSuggestions;
    }

    public function updateStoreOnGameComplete($gameId)
    {

        $now = Carbon::now()->toDateTimeString();
        $files = Storage::files(GameService::PATH_TO_STORE_COMPLETED);
        $currentFiles = $this->getStats();
        if (count($files) < GameService::TOP_N_RESULTS) {
            Storage::disk('local')->put(GameService::PATH_TO_STORE_COMPLETED . $gameId . '_' . $now . '.txt', $this->suggestionsCount);
        } else {

            $last = end($currentFiles);

            if ($last >= Storage::disk('local')->get(GameService::PATH_TO_STORE_IN_PROGRESS . $gameId . '.txt')) {
                Storage::disk('local')->put(GameService::PATH_TO_STORE_COMPLETED . $gameId . '_' . $now . '.txt', $this->suggestionsCount);
                $lastKey = array_key_last($currentFiles);
                Storage::disk('local')->delete($lastKey);
            }
        }

        Storage::disk('local')->delete(GameService::PATH_TO_STORE_IN_PROGRESS . $gameId . '.txt');
    }

    public function getStats()
    {

        $files = Storage::files(GameService::PATH_TO_STORE_COMPLETED);
        $currentFiles = [];
        foreach ($files as $file) {
            $currentFiles[$file] = Storage::disk('local')->get($file);
        }
        asort($currentFiles);

        return $currentFiles;
    }

    public function getStatsInfoParsed()
    {
        $stats = $this->getStats();
        $formattedStats = [];

        foreach ($stats as $key => $result) {
            $formattedKey = str_replace(['private/complete/', '.txt'], '', $key);
            $formattedKey = explode('_', $formattedKey);
            $formattedStats[$formattedKey[0]] = [
                'date' => $formattedKey[1],
                'result' => $result
            ];

        }

        return $formattedStats;
    }
}