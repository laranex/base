<?php

namespace laranex\LaravelMyanmarNRC;

use Exception;
use Illuminate\Support\Str;
use laranex\LaravelMyanmarNRC\Models\State;
use laranex\LaravelMyanmarNRC\Models\Township;
use laranex\LaravelMyanmarNRC\Models\Type;

class LaravelMyanmarNrc
{
    private function parseToMMNumber($nrcNumber): string
    {
        $myanmarNumbers = ['၀', '၁', '၂', '၃', '၄', '၅', '၆', '၇', '၈', '၉'];

        return collect(Str::of($nrcNumber)->split(1))->map(fn ($number) => $myanmarNumbers[$number])->implode('');
    }

    /**
     * @throws Exception
     */
    public function parseNRC($nrc, $lang = null): string
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

        if (! preg_match('/^[1-9][0-9]*$/', $nrcNumber)) {
            throw new Exception($exceptionMessage);
        }

        try {
            $state = State::findOrFail($nrc->get(0));
            $township = Township::findOrFail($nrc->get(1));

            if ($state->id !== $township->nrc_state_id) {
                throw new Exception($exceptionMessage);
            }

            if (! $type = Type::findOrFail($nrc[2])) {
                throw new Exception($exceptionMessage);
            }

            $state = $lang === 'en' ? $state->code : $state->code_mm;
            $township = $lang === 'en' ? $township->code : $township->code_mm;
            $type = $lang === 'en' ? $type->code : $type->code_mm;
            $nrcNumber = $lang === 'en' ? $nrcNumber : $this->parseToMMNumber($nrcNumber);

            return "$state/$township($type)$nrcNumber";
        } catch (Exception $_) {
            throw new Exception($exceptionMessage);
        }
    }

    public function isValidMyanmarNRC($nrc): bool
    {
        try {
            $this->parseNRC($nrc);

            return true;
        } catch (Exception $_) {
            return false;
        }

        return true;
    }
}
