<?php


namespace App\System\Exceptions;

use Throwable;

class BaseException extends \Exception
{
    /**
     * @var int
     */
    protected $code = 400;

    /**
     * @var string
     */
    protected $message = 'Unknown error.';

    /**
     * @var array
     */
    protected $details = [];

    /**
     * BaseException constructor.
     *
     * @param  string  $message
     * @param  array  $details
     * @param  Throwable  $previous
     */
    public function __construct($message = null, $details = null, Throwable $previous = null)
    {
        if ($message) {
            $this->message = $message;
        }
        if ($details) {
            $this->details = $details;
        }

        parent::__construct($this->message, $this->code, $previous);
    }

    /**
     * @return array
     */
    public function getDetails()
    {
        return $this->details;
    }
}
