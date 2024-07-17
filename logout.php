<?php

session_start();

echo "Please wait you are logout";

session_destroy();
header("Location: /php/Forum_project/index.php");
