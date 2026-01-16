<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(
                storage_path('firebase/firebase-adminsdk.json')
            )
            ->withDatabaseUri(
                'https://marketcuy-68a16-default-rtdb.firebaseio.com'
            );

        $this->database = $factory->createDatabase();

    }

    public function database()
    {
        return $this->database;
    }
}
