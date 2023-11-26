<?php

declare(strict_types=1);
?>
<html lang="en">
    <head>
        <title>Testing Form for test-stack</title>
    </head>
    <body>
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
                    if (isset($_GET['name2'])) {
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
                    Input class2
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
                    <?php
                    if (isset($_GET['name3'])) {
                        echo sprintf(
                            '<input type="text" hidden name="name3" value="%s">',
                            htmlspecialchars($_GET['name3']),
                        );
                    } else {
                        echo '<input type="text" hidden name="name3">';
                    }
                    ?>
                </label>
            </fieldset>
            <fieldset>
                <legend>input hidden with name3</legend>
                <label>
                    Input hidden name4
                    <?php
                    if (isset($_GET['name4'])) {
                        echo sprintf('<input type="hidden" name="name4" value="%s">', htmlspecialchars($_GET['name4']));
                    } else {
                        echo '<input type="hidden" name="name4">';
                    }
                    ?>
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
                    <?php
                    if (!isset($_GET['check1']) || !is_array($_GET['check1'])) {
                        $_GET['check1'] = [
                            2,
                            3,
                        ];
                    }
                    foreach (range(1, 4) as $number) {
                        if (in_array($number, $_GET['check1'])) {
                            echo sprintf('<input type="checkbox" name="check1[]" value="%s" checked>', $number);
                        } else {
                            echo sprintf('<input type="checkbox" name="check1[]" value="%s">', $number);
                        }
                    }
                    ?>
                </label>
            </fieldset>
            <fieldset>
                <legend>radio with name radio1</legend>
                <label>
                    Radio
                    <?php
                    if (!isset($_GET['radio1'])) {
                        $_GET['radio1'] = 2;
                    }
                    foreach (range(1, 4) as $number) {
                        if ($number == $_GET['radio1']) {
                            echo sprintf('<input type="radio" name="radio1" value="%s" checked>', $number);
                        } else {
                            echo sprintf('<input type="radio" name="radio1" value="%s">', $number);
                        }
                    }
                    ?>
                </label>
            </fieldset>
            <fieldset>
                <legend>select with name select1</legend>
                <label>
                    Select
                    <select name="select1">
                        <option>None</option>
                        <?php
                        if (!isset($_GET['select1'])) {
                            $_GET['select1'] = 2;
                        }
                        foreach (range(1, 4) as $number) {
                            if ($number == $_GET['select1']) {
                                echo sprintf('<option value="%s" selected>Value%s</option>', $number, $number);
                            } else {
                                echo sprintf('<option value="%s">Value%s</option>', $number, $number);
                            }
                        }
                        ?>
                    </select>
                </label>
            </fieldset>
            <fieldset>
                <legend>select multi with name select2</legend>
                <label>
                    Select
                    <select name="select2[]" multiple>
                        <option>None</option>
                        <?php
                        if (!isset($_GET['select2']) || !is_array($_GET['select2'])) {
                            $_GET['select2'] = [2, 3];
                        }
                        foreach (range(1, 4) as $number) {
                            if (in_array($number, $_GET['select2'])) {
                                echo sprintf('<option value="%s" selected>Value%s</option>', $number, $number);
                            } else {
                                echo sprintf('<option value="%s">Value%s</option>', $number, $number);
                            }
                        }
                        ?>
                    </select>
                </label>
            </fieldset>
            <fieldset>
                <legend>Submit without value</legend>
                <button type="submit">Submit without value</button>
            </fieldset>
            <fieldset>
                <legend>Submit with name button1 and value value1</legend>
                <button type="submit" name="button1" value="value1">Submit with value</button>
                <?php
                echo sprintf(
                    '<input type="text" value="%s" name="button1input">',
                    htmlspecialchars($_GET['button1'] ?? ''),
                )
                ?>
            </fieldset>
        </form>

        <h2>POST form</h2>
        <form method="post">

        </form>
    </body>
</html>
