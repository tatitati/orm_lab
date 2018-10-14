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

    /**
     * in the User data model class we will specify the field $address as "address" type. This will let to know to Doctrine
     * that this field has some mapper behind the scene. The it will use this mapper.
     *
     * @see \App\Entity\PersistenceModel\User::46
     */
    public function getName()
    {
        return 'address';
    }
}