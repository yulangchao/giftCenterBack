<?php

return array (
  'autoload' => false,
  'hooks' => 
  array (
    'testhook' => 
    array (
      0 => 'luckydraw',
    ),
  ),
  'route' => 
  array (
    '/qrcode$' => 'qrcode/index/index',
    '/qrcode/build$' => 'qrcode/index/build',
  ),
);