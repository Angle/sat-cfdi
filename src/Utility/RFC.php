<?php

namespace Angle\CFDI\Utility;

class RFC
{
    const RFC_GENERIC_NATIONAL = 'XAXX010101000';
    const RFC_GENERIC_FOREIGN  = 'XEXX010101000';

    const LEGAL_ENTITY_PATTERN = "[A-Z&amp;Ñ]{3}[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]";
    const NATURAL_PERSON_PATTERN = "[A-Z&amp;Ñ]{4}[0-9]{2}(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])[A-Z0-9]{2}[0-9A]";

    /**
     * @var string $rfc
     */
    private $rfc;

    /**
     * Disambiguation code
     * @var string $rfc
     */
    private $homoclave;

    /**
     * @var bool $naturalPerson
     */
    private $naturalPerson = false;

    /**
     * @var bool $legalEntity
     */
    private $legalEntity = false;

    /**
     * @var bool $genericNational
     */
    private $genericNational = false;

    /**
     * @var bool $genericForeign
     */
    private $genericForeign = false;

    // TODO: BirthDate


    public static function createFromString(string $rfc): ?self
    {
        $rfc = preg_replace('/\s/', '', $rfc);
        $rfc = str_replace('-', '', $rfc);
        $rfc = strtoupper($rfc);

        $entity = new self();
        $entity->rfc = $rfc;

        if ($rfc == self::RFC_GENERIC_NATIONAL) {
            $entity->genericNational = true;
            return $entity;
        }

        if ($rfc == self::RFC_GENERIC_FOREIGN) {
            $entity->genericForeign = true;
            return $entity;
        }

        // Check for Natual Person
        if (preg_match("/^" . self::NATURAL_PERSON_PATTERN . "$/", $rfc)) {
            $entity->naturalPerson = true;
            $entity->homoclave = substr($rfc, -3);
            return $entity;
        }

        // Check for Legal Entity
        if (preg_match("/^" . self::LEGAL_ENTITY_PATTERN . "$/", $rfc)) {
            $entity->legalEntity = true;
            $entity->homoclave = substr($rfc, -3);
            return $entity;
        }

        return null;
    }


    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string
     */
    public function getRfc(): ?string
    {
        return $this->rfc;
    }

    /**
     * @return string
     */
    public function getHomoclave(): ?string
    {
        return $this->homoclave;
    }

    /**
     * @return bool
     */
    public function isNaturalPerson(): bool
    {
        return $this->naturalPerson;
    }

    /**
     * @return bool
     */
    public function isLegalEntity(): bool
    {
        return $this->legalEntity;
    }

    /**
     * @return bool
     */
    public function isGenericNational(): bool
    {
        return $this->genericNational;
    }

    /**
     * @return bool
     */
    public function isGenericForeign(): bool
    {
        return $this->genericForeign;
    }


}