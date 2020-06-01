<?php

namespace Tests\Unit;

use App\Inspections\Spam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SpamTest extends TestCase
{
    use RefreshDatabase;
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
