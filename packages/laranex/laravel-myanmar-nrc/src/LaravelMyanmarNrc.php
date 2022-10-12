<?php

namespace laranex\LaravelMyanmarNRC;

use Exception;
use Illuminate\Support\Str;
use laranex\LaravelMyanmarNRC\Models\State;
use laranex\LaravelMyanmarNRC\Models\Township;
use laranex\LaravelMyanmarNRC\Models\Type;

class LaravelMyanmarNrc
{
    public function isValidMyanmarNRC($nrc): bool
    {
        $nrc = Str::of($nrc)->explode('-');
        if ($nrc->count() < 4) {
            return false;
        }

        try {
            $state = State::findOrFail($nrc->get(0));
            $township = Township::findOrFail($nrc->get(1));

            if ($state->id !== $township->nrc_state_id) {
                return false;
            }

            if (! Type::findOrFail($nrc[2])) {
                return false;
            }
        } catch (Exception $_) {
            return false;
        }

        return  true;
    }
}
