<?php


namespace App\Http\Controllers;


use App\Services\GameService;
use App\Services\ValidateService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class GameController extends Controller
{
    /** @var GameService */
    protected $gameService;

    /** @var ValidateService */
    protected $validateService;

    public function __construct(GameService $gameService, ValidateService $validateService)
    {
        $this->gameService = $gameService;
        $this->validateService = $validateService;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function startGame()
    {

        $gameNumber = $this->gameService->generateUniqueDigitSequence();
        return view('game', [
            'gameId' => $this->gameService->generateGameId(),
            'sequence' => implode('', $gameNumber),
            'topResults' => $this->gameService->getStatsInfoParsed()
        ]);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function game(Request $request): array
    {


        $suggestion = str_split($request->input('suggestion') ?? '');
        $sequence = str_split($request->input('sequence') ?? '');
        $gameId = $request->input('gameId');

        if (!$this->validateService->isNumberValid($suggestion)) {
            return [
                'error' => 'Suggestion must be 4 digit number with unique digits',
            ];
        }

        $this->gameService->storeGameResult($gameId);

        $gameRound = $this->gameService->calculateCawsAndBulls($sequence, $suggestion);
        $gameComplete = isset($gameRound['bulls']) && $gameRound['bulls'] == 4 ? 1 : 0;
        if($gameComplete) {
            $this->gameService->updateStoreOnGameComplete($gameId);
        }
        return [
            'gameComplete' => $gameComplete,
            'bulls' => isset($gameRound['bulls']) ? $gameRound['bulls'] : 0,
            'caws' => isset($gameRound['caws']) ? $gameRound['caws'] : 0,
            'gameId' => $gameId,
        ];

    }

}