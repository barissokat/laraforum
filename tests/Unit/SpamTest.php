<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{

    /**
     * @test
     *
     * @return void
     */
    public function itChecksForInvalidKeywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException(\Exception::class);

        $spam->detect('yahoo customer support');
    }

    /**
     * @test
     *
     * @return void
     */
    public function itChecksForAnyKeyBeingHeldDown()
    {
        $spam = new Spam();

        $this->expectException(\Exception::class);

        $spam->detect('Hello world aaaaaaaaaaaa');
    }
}
