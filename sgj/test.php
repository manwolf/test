<?php
$pwd = '123456';
define('SYS_SALT', 		'2fa202jafogagjawh');

echo base64_encode(md5(md5($pwd.SYS_SALT)));