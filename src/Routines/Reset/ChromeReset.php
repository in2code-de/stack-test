<?php

declare(strict_types=1);

namespace CoStack\StackTest\Routines\Reset;

use CoStack\StackTest\Routines\Routine;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\RemoteWebElement;
use Facebook\WebDriver\Remote\ShadowRoot;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

class ChromeReset implements Routine
{
    public function execute(RemoteWebDriver $driver): void
    {
        $driver->navigate()->to('chrome://settings/clearBrowserData');
        $settingsUi = $this->trySelectElement($driver, 'settings-ui')->getShadowRoot();
        $settingsMain = $this->trySelectElement($settingsUi, 'settings-main')->getShadowRoot();
        $settingsBasicPage = $this->trySelectElement($settingsMain, 'settings-basic-page')->getShadowRoot();
        $settingsPrivacyPage = $this->trySelectElement(
            $settingsBasicPage,
            'settings-section > settings-privacy-page',
        )->getShadowRoot();
        $settingsClearBrowsingDataDialog = $this->trySelectElement(
            $settingsPrivacyPage,
            'settings-clear-browsing-data-dialog',
        )->getShadowRoot();

        $clearBrowsingDataDialog = $this->trySelectElement(
            $settingsClearBrowsingDataDialog,
            '#clearBrowsingDataDialog',
        );
        $pagesTabs = $this->trySelectElement($clearBrowsingDataDialog, 'iron-pages#tabs');

        $clearFromBasic = $this->trySelectElement($pagesTabs, 'settings-dropdown-menu#clearFromBasic')->getShadowRoot();
        $dropdownMenu = $this->trySelectElement($clearFromBasic, 'select#dropdownMenu');
        $element = new WebDriverSelect($dropdownMenu);
        $element->selectByValue('4');

        $clearBrowsingDataDialog = $this->trySelectElement(
            $settingsClearBrowsingDataDialog,
            '#clearBrowsingDataDialog',
        );
        $clearBrowsingDataConfirm = $this->trySelectElement($clearBrowsingDataDialog, '#clearBrowsingDataConfirm');
        $clearBrowsingDataConfirm->click();

        $driver->navigate()->to('chrome://about');
    }

    protected function trySelectElement(
        RemoteWebElement|RemoteWebDriver|ShadowRoot $element,
        string $locator,
    ): RemoteWebElement {
        $elapsed = 0;
        $timeout = 100000;
        $pollingInterval = 200;
        do {
            try {
                return $element->findElement(WebDriverBy::cssSelector($locator));
            } catch (NoSuchElementException) {
                usleep($pollingInterval);
            } finally {
                $elapsed += $pollingInterval;
            }
        } while ($elapsed < $timeout);

        throw new NoSuchElementException(
            sprintf('Cannot locate element by css selector: %s', $locator),
        );
    }
}
