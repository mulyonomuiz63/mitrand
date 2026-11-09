<?php

declare(strict_types=1);

namespace App\Libraries;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\Pager\Pager;
use DateTimeImmutable;

class TableQuery
{
    public function __construct(private readonly IncomingRequest $request)
    {
    }

    public static function current(): self
    {
        return new self(service('request'));
    }

    public function search(): string
    {
        $value = trim((string) $this->request->getGet('q'));
        $value = preg_replace('/[\x00-\x1F\x7F]+/u', ' ', $value) ?? '';
        $value = preg_replace('/\s+/u', ' ', $value) ?? '';

        return mb_substr(trim($value), 0, 80);
    }

    public function perPage(): int
    {
        $size = (int) $this->request->getGet('per_page');

        return in_array($size, [10, 25, 50], true) ? $size : 10;
    }

    public function page(): int
    {
        $page = (int) $this->request->getGet('page');

        return max(1, $page);
    }

    /**
     * @param array<string, string> $allowed
     */
    public function sort(array $allowed, string $default): string
    {
        $sort = (string) $this->request->getGet('sort');

        return array_key_exists($sort, $allowed) ? $sort : $default;
    }

    public function direction(string $default = 'DESC'): string
    {
        $direction = strtoupper((string) $this->request->getGet('dir'));
        if (! in_array($direction, ['ASC', 'DESC'], true)) {
            return $default === 'ASC' ? 'ASC' : 'DESC';
        }

        return $direction;
    }

    /**
     * @param list<string> $allowed
     */
    public function oneOf(string $key, array $allowed): string
    {
        $value = (string) $this->request->getGet($key);

        return in_array($value, $allowed, true) ? $value : '';
    }

    public function day(string $key): string
    {
        $value = (string) $this->request->getGet($key);
        $date  = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === $value ? $value : '';
    }

    public function month(string $key): string
    {
        $value = (string) $this->request->getGet($key);

        return preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $value) === 1 ? $value : '';
    }

    /**
     * @param list<string> $only
     *
     * @return array{rows: list<array<string, mixed>>, pager: Pager, total: int, from: int, to: int, page: int, per_page: int}
     */
    public static function slice(BaseBuilder $builder, int $page, int $perPage, string $column, string $direction, array $only): array
    {
        $total = $builder->countAllResults(false);
        $pages = max(1, (int) ceil($total / $perPage));
        $page  = min(max(1, $page), $pages);
        $rows  = $builder
            ->orderBy($column, $direction, true)
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        $pager = service('pager');
        $pager->only($only);
        $pager->makeLinks($page, $perPage, $total, 'bootstrap_full');

        return [
            'rows'     => $rows,
            'pager'    => $pager,
            'total'    => $total,
            'from'     => $total === 0 ? 0 : (($page - 1) * $perPage) + 1,
            'to'       => min($total, $page * $perPage),
            'page'     => $page,
            'per_page' => $perPage,
        ];
    }
}
