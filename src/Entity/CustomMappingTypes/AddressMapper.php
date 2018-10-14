<?php
namespace App\Entity\CustomMappingTypes;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

class AddressMapper extends Type
{

    /** @return string */
    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getVarcharTypeDeclarationSQL($fieldDeclaration);
    }

    /** @return Address */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        list($city, $postcode, $street) = explode(",", $value);
        return new Address($city, $postcode, $street);
    }

    /** @return string */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return implode(',', [
            $value->getCity(),
            $value->getPostcode(),
            $value->getStreet()
        ]);
    }

    public function getName()
    {
        return 'address';
    }
}