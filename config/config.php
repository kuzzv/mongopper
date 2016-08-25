<?php
/**
 * Created by PhpStorm.
 * User: kz
 * Date: 8/19/16
 * Time: 4:08 PM
 */

return [
    'documentsPath' => 'Documents',
    'host'          => env('MONGO_HOST', 'homestead'),
    'database'      => env('MONGO_DATABASE', 'homestead'),
    'password'      => env('MONGO_PASSWORD', 'secret'),
    'port'          => env('MONGO_PORT', '27017'),
    'username'      => env('MONGO_USERNAME', 'homestead'),
];