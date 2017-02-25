<?php
declare(strict_types=1);

/**
 * @author    Yuriy Davletshin <yuriy.davletshin@gmail.com>
 * @copyright 2017 Yuriy Davletshin
 * @license   MIT
 */
namespace Satori\Pagination;

class Paginator
{
    protected $last;
    protected $current;
    protected $count;
    protected $perPage;
    protected $offset;

    public function __construct(int $current, int $count, int $perPage = null)
    {
        $this->current = $current > 0 ? $current : 1;
        $this->count = $count;
        $this->perPage = $perPage ?? 10;
        $this->offset = ($this->current - 1) * $this->perPage;
        $last = (int) ceil($this->count / $this->perPage);
        $this->last = $last ? $last : 1;

    }

    public function __get(string $name): int
    {
        switch ($name) {
            case 'first':
                return 1;

            case 'last':
                return $this->last;

            case 'current':
                return $this->current;

            case 'next':
                return $this->current + 1;

            case 'previous':
                return $this->current - 1;

            case 'count':
                return $this->count;

            case 'perPage':
                return $this->perPage;

            case 'offset':
                return $this->offset;

            default:
                throw new \LogicException(sprintf('Attribute "%s" does not exist.', $name));
        }
    }

    public function isFirst(): bool
    {
        return $this->current === 1;
    }

    public function isLast(): bool
    {
        return $this->current === $this->last;
    }

    public function fromStart(int $step): bool
    {
        return $this->current <= (1 + $step);
    }

    public function fromEnd(int $step): bool
    {
        return $this->current >= ($this->last - $step);
    }

    public function goForward(int $step): int
    {
        $page = $this->current + $step;

        return $page > $this->last ? $this->last : $page;
    }

    public function goBackward(int $step): int
    {
        $page = $this->current - $step;

        return $page < 1 ? 1 : $page;
    }

    public function getPageList(int $buttons = null, string $between = null): array
    {
        $buttons = $buttons ?? 5;
        $start = $this->current - (int) floor($buttons / 2);
        $start = $start > 1 ? $start : 1;
        $finish = $start + $buttons - 1;
        $finish = $finish < $this->last ? $finish : $this->last;
        $result = [];
        for ($i = $start; $i <= $finish; $i++) {
            $from = $i * $this->perPage - $this->perPage + 1;
            $to = $i * $this->perPage;
            $to = $to <= $this->count ? $to : $this->count;
            $result[] = ['page' => $i, 'range' =>  $from . ($between ?? '...') . $to];
        }

        return $result;
    }
}
