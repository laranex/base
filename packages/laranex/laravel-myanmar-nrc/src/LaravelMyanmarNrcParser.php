<?php

namespace laranex\LaravelMyanmarNRC;

use Exception;
use laranex\LaravelMyanmarNRC\Data\MyanmarNRCJsonHandler;
use laranex\LaravelMyanmarNRC\Models\State;
use laranex\LaravelMyanmarNRC\Models\Township;
use laranex\LaravelMyanmarNRC\Models\Type;
use Str;

class LaravelMyanmarNrcParser
{
    private function parseToMMNumber($nrcNumber): string
    {
        $myanmarNumbers = ['၀', '၁', '၂', '၃', '၄', '၅', '၆', '၇', '၈', '၉'];

        return collect(Str::of($nrcNumber)->split(1))->map(fn ($number) => $myanmarNumbers[$number])->implode('');
    }

    /**
     * @throws Exception
     */
    public function parseNRC($nrc, $dbDriven = false, $lang = null): string
    {
        $exceptionMessage = 'Invalid NRC';

        if (! $lang) {
            $lang = config('laravel-myanmar-nrc.locale');
        }

        if (! collect(['en', 'mm'])->contains($lang)) {
            throw new Exception('Only en and mm are allowed.');
        }

        $nrc = Str::of($nrc)->explode('-');
        if ($nrc->count() < 4) {
            throw new Exception($exceptionMessage);
        }

        $nrcNumber = $nrc[3];

        if (! preg_match('/^[1-9][0-9]*$/', $nrcNumber) || Str::of($nrcNumber)->length() !== 6) {
            throw new Exception($exceptionMessage);
        }

        // todo - override model methods depending on db_driven option
        try {
            if (config('laravel-myanmar-nrc.db_driven', true)) {
                $state = State::findOrFail($nrc->get(0));
                $township = Township::findOrFail($nrc->get(1));
                $type = Type::findOrFail($nrc->get(2));
            } else {
                $nrcJsonHandler = new MyanmarNRCJsonHandler();

                $state = $nrcJsonHandler->getState($nrc->get(0));
                $township = $nrcJsonHandler->getTownship($nrc->get(1));
                $type = $nrcJsonHandler->getType($nrc->get(2));
            }

            dd($state, $township, $type);

            if ($state->id !== $township->nrc_state_id) {
                throw new Exception($exceptionMessage);
            }

            $state = $lang === 'en' ? $state->code : $state->code_mm;
            $township = $lang === 'en' ? $township->code : $township->code_mm;
            $type = $lang === 'en' ? $type->code : $type->code_mm;
            $nrcNumber = $lang === 'en' ? $nrcNumber : $this->parseToMMNumber($nrcNumber);

            return "$state/$township($type)$nrcNumber";
        } catch (Exception $_) {
            dd($_);
            throw new Exception($exceptionMessage);
        }
    }
}
