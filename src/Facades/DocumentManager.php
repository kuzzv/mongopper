<?php

namespace Mongopper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Created by PhpStorm.
 * User: kz
 * Date: 8/19/16
 * Time: 4:19 PM
 */
class DocumentManager extends Facade
{
    public static function getFacadeAccessor()
    {
        return \Doctrine\ODM\MongoDB\DocumentManager::class;
    }
}