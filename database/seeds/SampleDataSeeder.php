<?php

use App\Activity;
use App\Channel;
use App\Favorite;
use App\Reply;
use App\Thread;
use App\ThreadSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Schema::disableForeignKeyConstraints();

        $this->channels()->content();

        Schema::enableForeignKeyConstraints();
    }

    protected function channels()
    {
        Channel::truncate();

        collect([
            [
                'name' => 'PHP',
                'description' => 'A channel for general PHP questions. Use this channel if you can\'t find a more specific channel for your question.',
                'archived' => false,
            ],
            [
                'name' => 'VueJS',
                'description' => 'A channel for general VueJS questions. Use this channel if you can\'t find a more specific channel for your question.',
                'archived' => false,
            ]
        ])->each(function ($channel) {
            factory(Channel::class)->create([
                'name' => $channel['name'],
                'description' => $channel['description'],
                'archived' => false,
            ]);
        });

        return $this;
    }

    /**
     * Seed the thread-related tables.
     */
    protected function content()
    {
        Thread::truncate();
        Reply::truncate();
        ThreadSubscription::truncate();
        Activity::truncate();
        Favorite::truncate();

        factory(Thread::class, 50)->states('from_existing_channels_and_users')->create()->each(function ($thread) {
            $this->recordActivity($thread, 'created', $thread->owner()->first()->id);
        });
    }

    /**
     * @param $model
     * @param $event_type
     * @param $user_id
     *
     * @throws ReflectionException
     */
    public function recordActivity($model, $event_type, $user_id)
    {
        $type = strtolower((new \ReflectionClass($model))->getShortName());

        $model->morphMany('App\Activity', 'subject')->create([
            'user_id' => $user_id,
            'type' => "{$event_type}_{$type}",
        ]);
    }
}
