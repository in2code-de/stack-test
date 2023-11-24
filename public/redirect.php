<?php

declare(strict_types=1);

if (str_contains($_SERVER['HTTP_USER_AGENT'], 'Firefox')) {
    header('Location: /test.php');
    return;
}

echo 'not redirected';
