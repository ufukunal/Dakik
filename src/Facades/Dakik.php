<?php

namespace KS\Dakik\Facades;

use Illuminate\Support\Facades\Facade;


class Dakik extends Facade
{

  protected static function getFacadeAccessor() { return 'dakik'; }
}
