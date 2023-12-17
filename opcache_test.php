<?php
 
echo('<hr /><pre>' . print_r(opcache_get_status(), true) . '</pre><hr />');
echo('<hr /><pre>' . print_r(opcache_get_configuration(), true) . '</pre><hr />');
