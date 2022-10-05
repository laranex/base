<?php

namespace laranex\LaravelMyanmarNRC;

use Illuminate\Support\Facades\Facade;

/**
 * @see \laranex\LaravelMyanmarNRC\Skeleton\SkeletonClass
 */
class LaravelMyanmarNrcFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-myanmar-nrc';
    }
}
