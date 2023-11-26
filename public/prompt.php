<?php

declare(strict_types=1);

?>
<h1>Confirmation</h1>
<input type="text" name="result" id="prompt-result">

<script type="text/javascript">
    const element = document.getElementById('prompt-result')
    const message = prompt('Enter your test message');
    element.value = message;
</script>
