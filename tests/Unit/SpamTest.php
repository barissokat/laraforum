<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Tests\TestCase;

class SpamTest extends TestCase
{

    /**
     *
     * @return void
     */
    public function testItChecksForInvalidKeywords()
    {
        $spam = new Spam();

        $this->assertFalse($spam->detect('Innocent reply here'));

        $this->expectException(\Exception::class);

        $spam->detect('yahoo customer support');
    }
}
