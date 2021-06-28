<?php
return [
    'driver'     => env('MAIL_DRIVER', 'smtp'),
    'host'       => env('MAIL_HOST', 'smtp.mailgun.org'),
    'port'       => env('MAIL_PORT', 587),
    'from'       => array('address' => 'bsms.support@ideaserv.com.ph', 'name' => 'support'),
    'encryption' => env('MAIL_ENCRYPTION', 'tls'),
    'username'   => env('BSMS_USERNAME'),
    'password'   => env('BSMS_PASSWORD'),
];