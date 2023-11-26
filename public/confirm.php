<?php

declare(strict_types=1);

?>
<h1>Confirmation</h1>
<input type="text" name="result" id="confirmation">

<script type="text/javascript">
    const element = document.getElementById('confirmation')
    if (confirm('Test confirm Message')) {
        element.value = 'Confirmed';
    } else {
        element.value = 'Declined';
    }
</script>
