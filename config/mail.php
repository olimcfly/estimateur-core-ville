<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Configuration Email
    |--------------------------------------------------------------------------
    */
    'driver'     => $_ENV['MAIL_DRIVER']     ?? 'smtp',
    'host'       => $_ENV['MAIL_HOST']       ?? 'smtp.mailtrap.io',
    'port'       => $_ENV['MAIL_PORT']       ?? 587,
    'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
    'username'   => $_ENV['MAIL_USERNAME']   ?? '',
    'password'   => $_ENV['MAIL_PASSWORD']   ?? '',

    /*
    |--------------------------------------------------------------------------
    | Expéditeur par défaut
    |--------------------------------------------------------------------------
    */
    'from' => [
        'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'contact@estimateur-immo.fr',
        'name'    => $_ENV['MAIL_FROM_NAME']    ?? 'Estimateur Immobilier',
    ],

    /*
    |--------------------------------------------------------------------------
    | Destinataires des leads
    |--------------------------------------------------------------------------
    */
    'leads' => [
        'to'      => $_ENV['MAIL_LEADS_TO']  ?? 'leads@estimateur-immo.fr',
        'cc'      => $_ENV['MAIL_LEADS_CC']  ?? '',
    ],

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    */
    'timeout'    => 10,
    'debug'      => $_ENV['MAIL_DEBUG'] ?? false,

];
