<?php

declare(strict_types=1);

namespace CoStack\StackTest\Exception;

use CoStack\StackTest\StackTestException;
use Facebook\WebDriver\Remote\RemoteWebElement;
use JetBrains\PhpStorm\Pure;
use Throwable;

class HiddenInputCanNotBeFilledException extends StackTestException
{
    protected const MESSAGE = 'The element you selected is hidden. You can not fill hidden inputs interactively. Use Session::fillHiddenInput to set values of hidden input fields.';
    public const CODE = 1700902848;

    #[Pure]
    public function __construct(public readonly RemoteWebElement $element, ?Throwable $previous = null)
    {
        parent::__construct(self::MESSAGE, self::CODE, $previous);
    }
}
