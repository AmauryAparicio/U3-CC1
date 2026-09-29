<?php
$cfg['blowfish_secret'] = 'local-demo-cafenahual-1234567890ab';
$i = 1;
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['host'] = '127.0.0.1';
$cfg['Servers'][$i]['user'] = 'wpuser';
$cfg['Servers'][$i]['password'] = 'wp1234';
$cfg['Servers'][$i]['only_db'] = 'wp_cafenahual';
$cfg['Servers'][$i]['AllowNoPassword'] = false;
$cfg['TempDir'] = '/tmp/pma_uploads';
$cfg['Lang'] = 'es';
