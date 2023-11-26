<?php

declare(strict_types=1);

namespace CoStack\StackTest\TYPO3;

use Closure;
use CoStack\StackTest\Session;
use CoStack\StackTest\Test\Constraint\Existence\ElementExists;
use Facebook\WebDriver\WebDriverBy;

class TYPO3Helper
{
    public static function inIFrameContext(Session $session, Closure $closure): void
    {
        $session->inIFrameContext(WebDriverBy::id('typo3-contentIframe'), $closure);
    }

    public static function waitUntilContentIFrameIsLoaded(Session $session): void
    {
        $session->waitUntil(TYPO3ExpectedCondition::contentIFrameIsLoaded());
    }

    public static function backendLogin(Session $session, string $url, string $username, string $password): void
    {
        $session->get($url);

        $loginFormSelector = WebDriverBy::name('loginform');

        $constrain = new ElementExists($session);
        $loginFormExists = $constrain->eval($loginFormSelector);
        if ($loginFormExists) {
            $session->submitForm($loginFormSelector, [
                'username' => $username,
                'p_field' => $password,
            ]);
        }
    }
}
