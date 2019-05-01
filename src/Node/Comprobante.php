<?php

use \Exception as Exception;

class Comprobante
{
    #########################
    ##        PRESETS      ##
    #########################

    // none
    const SERIE_WRONG_LENGTH_ERROR = 1;


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * Display Label = Version
     * @var string
     */
    private $version = "3.3";

    /**
     * Display Label = Serie
     * MaxLength = 25
     * RegExp = ^[a-zA-Z0-0]$
     * @var string
     */
    private $serie;


    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string|null
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Comprobante
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getSerie(): string
    {
        return $this->serie;
    }

    /**
     * @param string $serie
     * @return Comprobante
     * @throws Exception
     */
    public function setSerie(string $serie): self
    {
        // Validate Length
        $l = strlen($serie);

        if ($l < 1 || $l > 25) {
            throw new Exception("Serie contains wrong length.", self::SERIE_WRONG_LENGTH_ERROR);
        }

        // Validate contents
        if (preg_match('/^[a-zA-Z0-0]$/'), $serie)



        $this->serie = $serie;
        return $this;
    }
}