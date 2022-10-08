<?php


namespace App\Http\Controllers;


use App\Services\GameService;
use App\Services\ValidateService;
use Illuminate\Http\Request;


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
                'suggestion' => $suggestion,
            ];
        }


        $gameRound = $this->gameService->calculateCawsAndBulls($sequence, $suggestion);
        return [
            'gameComplete' => isset($gameRound['bulls']) && $gameRound['bulls'] == 4 ? 1 : 0,
            'bulls' => isset($gameRound['bulls']) ? $gameRound['bulls'] : 0,
            'caws' => isset($gameRound['caws']) ? $gameRound['caws'] : 0,
            'gameId' => $gameId,
        ];

    }

}