<?php

namespace Marshmallow\Zipcode\Facades;

use Illuminate\Support\Facades\Facade;

class Zipcode extends Facade
{
    /**
     * {@inheritdoc}
     */
    protected static function getFacadeAccessor()
    {
        return \Marshmallow\Zipcode\Zipcode::class;
    }
}
