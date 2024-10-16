<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    // Optionnel: méthode pour récupérer tous les auteurs, bien que redondante avec findAll()
    public function findAuthors(): array
    {
        return $this->findAll();
    }

    // Exemple d'une méthode de recherche par un champ spécifique, si nécessaire
    public function findByEmail(string $email): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
