<?php

namespace App\Exceptions\Bearer;

use Exception;
use Throwable;


/**
 * Exception for defining which route are you redirected
 *
 * Class RedirectException
 * @package App\Exceptions
 */
class BearerRequiredException extends Exception
{
    /**
     * @var string
     */
    private string $status;

    public function __construct(
        string $message = '',
        int $code = 0, string
        $status = 'error',
        Throwable $previous = null
    ){
        $this->status = $status;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
