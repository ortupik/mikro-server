<?php
session_unset();
session_destroy();
header("Location: http://surf.co.ke/login");
exit();
