<?php

namespace Progrupa\AzureBundle\Azure;


use JMS\Serializer\Annotation as Serializer;

class Error
{
    /**
     * @var integer
     * @Serializer\Type("string")
     */
    private $code;

    private $message;

    /** @var  string */
    private $raw;

    /**
     * Error constructor.
     * @param int $code
     * @param string $raw
     */
    public function __construct($code, $raw)
    {
        $this->code = $code;
        $this->raw = $raw;
    }
}
