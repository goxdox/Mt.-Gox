<?php
# REGISTER CUSTOM SESSION FUNCTIONS
session_set_save_handler('db_sess_connect', 'db_sess_disconnect', 'sess_get', 'sess_put', 'sess_del', 'sess_clean');

# BEGIN SESSION
session_start();
?>
