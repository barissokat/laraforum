<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeaderboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     *
     * @return void
     */
    public function testItRetrievesTheTop10UsersSortedByReputation()
    {
        collect([1, 5, 10, 20, 30, 40, 50, 60, 70, 80, 90, 100])->each(function ($reputation) {
            create(User::class, [
                'reputation' => $reputation,
            ]);
        });

        $reputation = collect($this->getJson(route('api.leaderboard.index'))->json()['leaderboard'])
            ->pluck('reputation')
            ->toArray();

        $this->assertEquals([100, 90, 80, 70, 60, 50, 40, 30, 20, 10], $reputation);
    }
}
