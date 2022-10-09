<?php


namespace Tests\Unit;

use App\Services\ValidateService;
use Tests\TestCase;

class GameValidateServiceTest extends TestCase
{
    /** @var ValidateService */
    protected $validateService;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->validateService = new ValidateService();
    }

    public function testIsNumberValidNumberSequence()
    {
        $seq = [1, 2, 3, 4];
        $this->assertTrue($this->validateService->isNumberValid($seq));

        $seq = [1, 2, 'a', 4];
        $this->assertFalse($this->validateService->isNumberValid($seq));
    }

    public function testIsValidUniqueNumberSeq()
    {

        $seq = [1, 2, 3, 4];
        $this->assertTrue($this->validateService->isNumberValid($seq));

        $seq = [1, 2, 3, 4, 8];
        $this->assertFalse($this->validateService->isNumberValid($seq));

        $seq = ["a", 2, 3, 4, 8];
        $this->assertFalse($this->validateService->isNumberValid($seq));
    }
}