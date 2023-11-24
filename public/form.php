<?php

declare(strict_types=1);
?>

<h2>GET Form</h2>
<form method="get" name="form1">
    <fieldset>
        <legend>Input text</legend>
        <label>
            Input without attributes
            <input type="text">
        </label>
    </fieldset>
    <fieldset>
        <legend>Input text with name name1</legend>
        <label>
            Input name1
            <?php
            if (isset($_GET['name1'])) {
                echo sprintf('<input type="text" name="name1" value="%s">', htmlspecialchars($_GET['name1']));
            } else {
                echo '<input type="text" name="name1">';
            }
            ?>
        </label>
    </fieldset>
    <fieldset>
        <legend>Input text with id id1</legend>
        <label>
            Input id1
            <input type="text" id="id1">
        </label>
    </fieldset>
    <fieldset>
        <legend>Input text with class class1</legend>
        <label>
            Input class1
            <input type="text" class="class1">
        </label>
    </fieldset>
    <fieldset>
        <legend>Input text with default value value1</legend>
        <label>
            Input value1
            <input type="text" value="value1">
        </label>
    </fieldset>
    <fieldset>
        <legend>Input text with default value value2 with name name2</legend>
        <label>
            Input name1
            <?php
            if (isset($_GET['name1'])) {
                echo sprintf('<input type="text" value="%s" name="name2">', htmlspecialchars($_GET['name2']));
            } else {
                echo '<input type="text" value="value2" name="name2">';
            }
            ?>
        </label>
    </fieldset>
    <fieldset>
        <legend>Input text with default value value3 with id id2</legend>
        <label>
            Input name2
            <input type="text" value="value3" id="id2">
        </label>
    </fieldset>
    <fieldset>
        <legend>Input text with default value value4 with class class2</legend>
        <label>
            Input name3
            <input type="text" value="value4" class="class2">
        </label>
    </fieldset>
    <fieldset>
        <legend>Hidden input</legend>
        <label>
            Input hidden
            <input type="text" hidden>
        </label>
    </fieldset>
    <fieldset>
        <legend>Hidden input text with name3</legend>
        <label>
            Input hidden name3
            <input type="text" hidden name="name3">
        </label>
    </fieldset>
    <fieldset>
        <legend>input hidden with name3</legend>
        <label>
            Input hidden name4
            <input type="hidden" name="name4">
        </label>
    </fieldset>
    <fieldset>
        <legend>Disabled input with name5</legend>
        <label>
            Input disabled
            <input type="text" disabled name="name5">
        </label>
    </fieldset>
    <fieldset>
        <legend>Checkbox with name check1</legend>
        <label>
            Checkbox
            <input type="checkbox" name="check1" value="">
            <input type="checkbox" name="check1" value="1">
            <input type="checkbox" name="check1" value="2" checked>
            <input type="checkbox" name="check1" value="3">
        </label>
    </fieldset>
    <fieldset>
        <legend>radio with name radio1</legend>
        <label>
            Radio
            <input type="radio" name="radio1" value="">
            <input type="radio" name="radio1" value="1">
            <input type="radio" name="radio1" value="2" checked>
            <input type="radio" name="radio1" value="3">
        </label>
    </fieldset>
    <fieldset>
        <legend>select with name select1</legend>
        <label>
            Select
            <select name="select1">
                <option>None</option>
                <option value="1">Value1</option>
                <option value="2" selected>Value2</option>
                <option value="3">Value3</option>
            </select>
        </label>
    </fieldset>
    <fieldset>
        <legend>select multi with name select2</legend>
        <label>
            Select
            <select name="select2" multiple>
                <option>None</option>
                <option value="1">Value1</option>
                <option value="2" selected>Value2</option>
                <option value="3" selected>Value3</option>
            </select>
        </label>
    </fieldset>
    <fieldset>
        <legend>Submit without value</legend>
        <button type="submit">Submit without value</button>
    </fieldset>
    <fieldset>
        <legend>Submit with name button1 and value value1</legend>
        <button type="submit" name="button1" value="value1">Submit without value</button>
    </fieldset>
</form>

<h2>POST form</h2>
<form method="post">

</form>
