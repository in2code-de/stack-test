# co-stack/stack-test - enhance your PHPUnit with Browser testing

## Features

* No configuration to get started.
* Countless assertions for any possible assertion of elements, contents, ...
* Assertions can also be used as conditions and expectations (to wait for something to happen in the browser)
* Far less complicated than testing Frameworks like codeception
* Solely based on PHPUnit, therefore fully integrated into PhpStorm.
* Easy Form handling (get and set form data)

## Enable screenshots and more for failed tests

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         xsi:noNamespaceSchemaLocation="https://schema.phpunit.de/10.4/phpunit.xsd"
>
    <!-- Add the extension section to your phpunit.xml file and adjust the paths as needed -->
    <extensions>
        <bootstrap class="CoStack\StackTest\Bootstrap">
            <parameter name="screenshot" value="build/test/artifacts/{testClass}/{testMethod}/{seed}.jpg"/>
            <parameter name="pageSource" value="build/test/artifacts/{testClass}/{testMethod}/{seed}.html"/>
            <parameter name="history" value="build/test/artifacts/{testClass}/{testMethod}/{seed}.history.txt"/>
        </bootstrap>
    </extensions>
</phpunit>
```

## Important things to know

* Firefox always sets cookie secure = true
* Options without value attribute can not be selected by value
