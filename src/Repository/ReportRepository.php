<?php

namespace App\Repository;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ParameterType;
use Symfony\Component\String\UnicodeString;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ReportRepository
{
    public function __construct(
        private Connection $connection,
        private TagAwareCacheInterface $reportCache
    ) {}

    /**
     * @param int[] $subjectIds
     * @param int[] $authorIds
     *
     * @return array{reports: array<int, list<array<string, mixed>>>, total: int, page: int, totalPages: int}
     */
    public function findForReport(array $subjectIds, array $authorIds, ?string $search, int $page, int $limit = 20): array
    {
        sort($subjectIds);
        sort($authorIds);

        return $this->cached(
            'report',
            [
                $subjectIds,
                $authorIds,
                $search,
                $page,
                $limit
            ],
            function () use ($subjectIds, $authorIds, $search, $page, $limit) {
                [$where, $parameters, $types] = $this->buildFilters($subjectIds, $authorIds, $search);
                $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

                $total = (int) $this->connection->fetchOne(
                    'SELECT COUNT(*) FROM vw_livros_por_autor r' . $whereSql,
                    $parameters,
                    $types,
                );

                $totalPages = max(1, (int) ceil($total / $limit));
                $page = min(max(1, $page), $totalPages);
                $parameters['limit'] = $limit;
                $parameters['offset'] = ($page - 1) * $limit;

                $items = $this->connection->fetchAllAssociative(
                    'SELECT r.* FROM vw_livros_por_autor r' . $whereSql . ' ORDER BY r.autor_nome, r.titulo, r.livro_codigo LIMIT :limit OFFSET :offset',
                    $parameters,
                    $types + [
                        'limit' => ParameterType::INTEGER,
                        'offset' => ParameterType::INTEGER,
                    ],
                );

                return [
                    'reports' => $this->groupBooksByAuthor($items),
                    'total' => $total,
                    'page' => $page,
                    'totalPages' => $totalPages,
                ];
            }
        );
    }

    /**
     * @param int[] $subjectIds
     * @param int[] $authorIds
     *
     * @return list<array<string, mixed>>
     */
    public function findAllForExport(array $subjectIds, array $authorIds, ?string $search): array
    {
        sort($subjectIds);
        sort($authorIds);

        return $this->cached('report_export', [$subjectIds, $authorIds, $search], function () use ($subjectIds, $authorIds, $search) {
            [$where, $parameters, $types] = $this->buildFilters($subjectIds, $authorIds, $search);
            $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

            return $this->connection->fetchAllAssociative(
                'SELECT r.* FROM vw_livros_por_autor r' . $whereSql . ' ORDER BY r.autor_nome, r.titulo, r.livro_codigo',
                $parameters,
                $types,
            );
        });
    }

    /**
     * @param int[] $subjectIds
     * @param int[] $authorIds
     * @return array{list<string>, array<string, mixed>, array<string, mixed>}
     */
    private function buildFilters(array $subjectIds, array $authorIds, ?string $search): array
    {
        $where = [];
        $parameters = [];
        $types = [];

        if ($authorIds !== []) {
            $where[] = 'r.autor_codigo IN (:authorIds)';
            $parameters['authorIds'] = $authorIds;
            $types['authorIds'] = ArrayParameterType::INTEGER;
        }

        if ($subjectIds !== []) {
            $where[] = 'EXISTS (
                SELECT 1
                FROM Livro_Assunto ls
                INNER JOIN Assunto s ON s.CodAs = ls.Assunto_CodAs
                WHERE ls.Livro_CodL = r.livro_codigo
                    AND s.CodAs IN (:subjectIds)
                    AND s.DeletedAt IS NULL
            )';
            $parameters['subjectIds'] = $subjectIds;
            $types['subjectIds'] = ArrayParameterType::INTEGER;
        }

        if ($search !== null && $search !== '') {
            $where[] = '(LOWER(r.titulo) LIKE :search OR LOWER(r.editora) LIKE :search)';
            $parameters['search'] = '%' . (new UnicodeString($search))->lower()->toString() . '%';
        }

        return [$where, $parameters, $types];
    }

    /**
     * @param list<array<string, mixed>> $items
     *
     * @return array<int, list<array<string, mixed>>>
     */
    private function groupBooksByAuthor(array $items): array
    {
        $reports = [];
        foreach ($items as $item) {
            $reports[(int) $item['autor_codigo']][] = $item;
        }

        return $reports;
    }


    /**
     * cached
     *
     * @param string $prefix
     * @param array $key
     * @param callable $callback
     * @return mixed
     */
    private function cached(string $prefix, array $key, callable $callback): mixed
    {
        $cacheKey = $prefix . '_' . md5(serialize($key));

        return $this->reportCache->get($cacheKey, function (ItemInterface $item,) use ($callback) {
            $item->tag(['report']);

            return $callback();
        });
    }
}
