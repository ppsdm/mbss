<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=aws.ppsdm.com;dbname=ppsdm_mbss',
            'username' => 'root',
            'password' => '#root123',
            'charset' => 'utf8',
        ],
//        'taodb' => [
//            'class' => 'yii\db\Connection',
//            'dsn' => 'mysql:host=202.67.13.34;dbname=ppsdm_tao',
//            'username' => 'ppsdm',
//            'password' => 'ppsdm-mysql',
//            'charset' => 'utf8',
//        ],
         'taodb' => [
             'class' => 'yii\db\Connection',
             'dsn' => 'mysql:host=18.141.43.66;dbname=taodb',
             'username' => 'ppsdm',
             'password' => 'Prima2020#',
             'charset' => 'utf8',
         ],
        'coredb' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=aws.ppsdm.com;dbname=ppsdm_core',
            'username' => 'root',
            'password' => '#root123',
            'charset' => 'utf8',
    ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
];
