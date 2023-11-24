<?php

declare(strict_types=1);

echo "hi there";

if (isset($_GET['foo'])) {
    echo 'foo=' . htmlspecialchars($_GET['foo']);
}

echo '<br>';
echo $_SERVER['HTTP_USER_AGENT'];
?>
<a href="/test2.php">test2</a>
