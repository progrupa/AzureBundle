<?php

namespace Progrupa\AzureBundle\Azure;


class Response
{
    private $raw;

    /**
     * Response constructor.
     * @param $raw
     */
    public function __construct($raw)
    {
        $this->raw = $raw;
    }
}
