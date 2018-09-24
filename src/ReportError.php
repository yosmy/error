<?php

namespace Yosmy;

interface ReportError
{
    /**
     */
    public function register();

    /**
     * @param $e
     */
    public function report($e);
}