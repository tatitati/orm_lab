<?php
namespace App\Repository;

use App\Entity\PersistenceModel\User;

interface UserRepository
{
    public function save(User $user);
}