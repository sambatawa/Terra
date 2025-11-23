<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Firebase Realtime Database and Firestore
    |
    */

    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-credentials.json')),
    
    'database_url' => env('FIREBASE_DATABASE_URL', ''),
    
    'project_id' => env('FIREBASE_PROJECT_ID', ''),
    
    'realtime_database' => [
        'url' => env('FIREBASE_REALTIME_DB_URL', ''),
    ],
];

