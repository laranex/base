<?php

namespace laranex\LaravelMyanmarNRC\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use laranex\LaravelMyanmarNRC\Models\State;
use laranex\LaravelMyanmarNRC\Models\Township;
use laranex\LaravelMyanmarNRC\Models\Type;

class SeedMyanmarNRCCommand extends Command
{
    protected $signature = 'mm-nrc:seed';

    protected $description = 'Delete the previous data from Myanmar NRC tables, Seed the data again';

    public function handle()
    {
        $this->info('Loading and seeding NRCs from configs/laravel-myanmar-nrc');

        $this->warn('Deleting NRCs from database');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Type::truncate();
        State::truncate();
        Township::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->warn('Deleting NRCs from database is completed');

        collect(config('laravel-myanmar-nrc.types'))->map(function ($type) {
            Type::create($type);
        });

        $townships = collect();

        collect(config('laravel-myanmar-nrc.states'))->each(function ($state) use (&$townships) {
            $state = collect($state);

            $stateId = State::create($state->except(['townships'])->toArray())->id;

            $now = now();
            collect($state->get('townships'))->each(function ($township) use (&$townships, $stateId, $now) {
                $townships->push([
                    ...$township,
                    'nrc_state_id' => $stateId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            });
        });

        Township::insert($townships->all());

        $this->info('NRCs from configs/laravel-myanmar-nrc were seeded into database');
    }
}
