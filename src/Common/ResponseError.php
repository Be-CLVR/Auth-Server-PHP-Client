<?php

namespace BeCLVR\AuthServerClient\Common;

/**
 * Class ResponseError.
 */
class ResponseError
{
    public $errors = [];

    /**
     * Load the error data into an array.
     *
     * @param mixed $body
     */
    public function __construct($body)
    {
        if (!empty($body->errors)) {
            foreach ($body->errors as $error) {
                $this->errors[] = $error;
            }
        }

        if (isset($body->message)) {
            $this->errors[] = $body->message;
        }
    }

    /**
     * Get a string of all of this response's concatenated error descriptions.
     *
     * @return string
     */
    public function getErrorString()
    {
        $errorDescriptions = [];

        foreach ($this->errors as $error) {
            $errorDescriptions[] = $error->description;
        }

        return implode(', ', $errorDescriptions);
    }
}
