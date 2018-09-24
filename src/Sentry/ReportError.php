<?php

namespace Yosmy\Sentry;

use Yosmy;
use Sentry;
use JsonSerializable;
use LogicException;
use Exception;

/**
 * @di\service()
 */
class ReportError implements Yosmy\ReportError
{
    /**
     * @var string
     */
    private $dsn;

    /**
     * @di\arguments({
     *     dsn: "%sentry_dsn%"
     * })
     *
     * @param string $dsn
     */
    public function __construct(
        string $dsn
    ) {
        $this->dsn = $dsn;
    }

    /**
     */
    public function register()
    {
        Sentry\init(['dsn' => $this->dsn]);
    }

    /**
     * @param $e
     */
    public function report(
        $e
    ) {
        $code = $e->getCode();

        if ($e instanceof JsonSerializable) {
            $message = print_r($e->jsonSerialize(), true);

            $e = new LogicException($message, $code, $e);
        } else if (
            $e instanceof Exception
            && $e->getPrevious()
            && $e->getPrevious() instanceof JsonSerializable
        ) {
            $e = $e->getPrevious();

            /** @var JsonSerializable $e */

            $message = print_r($e->jsonSerialize(), true);

            $e = new LogicException($message, $e->getCode(), $e);
        }

        Sentry\captureException($e);
    }
}
