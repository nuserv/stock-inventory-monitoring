<?php
return [
  'bcc' => explode(',', env('EMAIL_ADD')),
  'disabled' => env('MAIL_DISABLE', false),
];
