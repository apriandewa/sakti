<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Verification extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'verification';
    }
}
