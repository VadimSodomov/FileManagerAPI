<?php

namespace App\Repository;

use App\Entity\File;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<File>
 */
class FileRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, File::class);
    }

    public function getFilesForDelete(int $folderId): array|false
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        WITH RECURSIVE folder_tree AS (
            SELECT id
            FROM folder
            WHERE id = :folder_id
        
            UNION ALL
        
            SELECT f.id
            FROM folder f
                     INNER JOIN folder_tree ft ON f.parent_id = ft.id
        )
        SELECT f.server_name
        FROM file f
                 INNER JOIN folder_tree ft ON f.folder_id = ft.id;
        SQL;

        $result = $conn->executeQuery($sql, ['folder_id' => $folderId])->fetchAllAssociative();

        return array_column($result, 'server_name');
    }

    //    /**
    //     * @return File[] Returns an array of File objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('f.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?File
    //    {
    //        return $this->createQueryBuilder('f')
    //            ->andWhere('f.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
