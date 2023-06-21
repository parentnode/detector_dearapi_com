<?php

$message = preg_replace("/\; USERAGENT\:/", ";\nUSERAGENT:", $message);
$message = preg_replace("/\; SEGMENT\:/", ";\nSEGMENT:", $message);

?>