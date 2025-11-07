<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Folder;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class FolderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Folder::class);
    }

    /**
     * Доступный путь к родителским папкам относительно указанной папки по коду
     */
    public function getAccessPathByCode(int $folderId, string $code): ?array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = <<<SQL
        WITH RECURSIVE folder_path AS (
            SELECT
                id,
                parent_id,
                name,
                code,
                code_cdate,
                JSONB_BUILD_ARRAY(
                        JSONB_BUILD_OBJECT('id', id, 'name', name)
                ) as path_objects,
                1 as level
            FROM folder
            WHERE id = :folder_id
            UNION ALL
        
            SELECT
                f.id,
                f.parent_id,
                f.name,
                f.code,
                f.code_cdate,
                JSONB_BUILD_ARRAY(
                        JSONB_BUILD_OBJECT('id', f.id, 'name', f.name)
                ) || fp.path_objects as path_objects,
                fp.level + 1 as level
            FROM folder f
                     INNER JOIN folder_path fp ON f.id = fp.parent_id
            WHERE f.parent_id IS NOT NULL
        )
        SELECT
            path_objects
        FROM folder_path
        WHERE code = :code
        ORDER BY level DESC
        LIMIT 1;
        SQL;

        $result = $conn->executeQuery($sql, ['folder_id' => $folderId, 'code' => $code])->fetchOne();

        return $result ? json_decode($result, true) : null;
    }
}
