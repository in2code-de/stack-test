<?php

declare(strict_types=1);

namespace CoStack\StackTest\Routines\Reset;

use CoStack\StackTest\Routines\Routine;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;

class FirefoxReset implements Routine
{
    public function execute(RemoteWebDriver $driver): void
    {
        $driver->navigate()->to('about:preferences#privacy');
        $driver->findElement(WebDriverBy::id('clearSiteDataButton'))->click();
        $driver->executeScript(
            "document.querySelector('browser.dialogFrame').contentDocument.querySelector('dialog').shadowRoot.querySelector('[dlgtype=\'accept\']').click();",
        );
        $driver->switchTo()->alert()->accept();
        $driver->switchTo()->defaultContent();
        $driver->navigate()->to('about:newtab');
    }
}
