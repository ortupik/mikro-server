<?php
if (!isset($_SESSION["surf"])) {
    header("Location:../admin.php?id=login");
  } else {
        $_SESSION["v"] = "3.20 06-30-2021";
    
    }
