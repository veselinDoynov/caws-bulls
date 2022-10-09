<?php


namespace Tests\Unit;


use App\Services\GameService;
use Tests\TestCase;

class GameServiceTest extends TestCase
{
    /** @var GameService */
    protected $gameService;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->gameService = new GameService();
    }

    public function testGenerateSeq()
    {

        $seq = $this->gameService->sequence(4);
        $this->assertEquals(count($seq), 4);
        $seq = $this->gameService->sequence(5);
        $this->assertEquals(count($seq), 5);
    }

    public function testSeqIsValid()
    {

        $seq = [1, 2, 3, 4];
        $this->assertFalse($this->gameService->isOk($seq));
        $seq = [1, 8, 3, 6];
        $this->assertTrue($this->gameService->isOk($seq));

        $seq = [5, 2, 3, 6];
        $this->assertTrue($this->gameService->isOk($seq));

        $seq = [2, 3, 5, 4];
        $this->assertFalse($this->gameService->isOk($seq));

        $seq = [1, 8, 5, 9];
        $this->assertTrue($this->gameService->isOk($seq));

        $seq = [1, 6, 5, 9];
        $this->assertFalse($this->gameService->isOk($seq));

        $seq = [2, 8, 5, 9];
        $this->assertFalse($this->gameService->isOk($seq));

        $seq = [9, 6, 7, 3];
        $this->assertTrue($this->gameService->isOk($seq));

        $seq = [5, 4, 1, 8];
        $this->assertFalse($this->gameService->isOk($seq));

        $seq = [5, 3, 1, 8];
        $this->assertTrue($this->gameService->isOk($seq));

        $seq = [1, 3, 1, 8];
        $this->assertFalse($this->gameService->isOk($seq));

        $seq = [1, 8, 5, 9];
        $this->assertTrue($this->gameService->isOk($seq));

        $seq = [7, 6, 0, 3];
        $this->assertTrue($this->gameService->isOk($seq));
    }

    public function testGenerateUniqueSequence()
    {

        $seq = $this->gameService->generateUniqueDigitSequence();
        $possibilities = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];

        $this->assertEquals(6, count(array_diff($possibilities, $seq)));
    }

    public function testCalculateCawsAndBulls()
    {
        $seq = [5, 3, 1, 8];
        $suggestion = [5, 1, 2, 9];
        $result = $this->gameService->calculateCawsAndBulls($seq, $suggestion);

        $this->assertEquals(1, $result['bulls']);
        $this->assertEquals(1, $result['caws']);

        $seq = [5, 3, 1, 8];
        $suggestion = [1, 5, 8, 3];
        $result = $this->gameService->calculateCawsAndBulls($seq, $suggestion);


        $this->assertEquals(0, $result['bulls']);
        $this->assertEquals(4, $result['caws']);

        $seq = [5, 3, 1, 8];
        $suggestion = [5, 3, 8, 1];
        $result = $this->gameService->calculateCawsAndBulls($seq, $suggestion);


        $this->assertEquals(2, $result['bulls']);
        $this->assertEquals(2, $result['caws']);

        $seq = [5, 3, 1, 8];
        $suggestion = [5, 3, 1, 8];
        $result = $this->gameService->calculateCawsAndBulls($seq, $suggestion);


        $this->assertEquals(4, $result['bulls']);
        $this->assertEquals(0, $result['caws']);

    }

}