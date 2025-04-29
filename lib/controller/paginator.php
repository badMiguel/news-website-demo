<?php

declare(strict_types=1);

class Paginator
{
    public int $amountToDisplay;
    private int $totalNews;
    private array $newsList;
    private int $currentNews;
    public int $currentPage;

    public function __construct()
    {
        $this->amountToDisplay = 5;
        $this->currentNews = 0;
        $this->currentPage = 1;
    }

    public function start(array $newsList)
    {
        $this->newsList = $newsList;
        $this->totalNews = count($newsList);

        $maxItem = $this->currentNews + $this->amountToDisplay;
        if ($maxItem - 1 > $this->totalNews) {
            $maxItem = $this->totalNews - 1;
        }
        return array_slice($this->newsList, $this->currentNews, $maxItem);
    }

    public function getTotalPages(): int
    {
        return intdiv($this->totalNews, $this->amountToDisplay);
    }

    /**
     *
     * Returns the start and end of page to display based on the current page.
     *
     */
    public function getPageRange(): array
    {
        $totalPages = $this->getTotalPages();

        if ($totalPages <= 2) {
            return [];
        }

        $currentPage = $this->currentPage;
        $pageStart = $currentPage;
        $pageEnd = $currentPage;
        $counter = 0;

        if ($currentPage === 1 && $totalPages > 3) {
            $pageEnd++;
        }

        if ($currentPage === $totalPages && $totalPages - 3 > 1) {
            $pageStart--;
        }

        while ($counter < 2) {
            if ($pageStart - 1 > 1) {
                $pageStart--;
                $counter++;
            }

            if ($counter === 1 && $totalPages < 5) {
                break;
            } else if ($counter === 3) {
                break;
            }

            if ($pageEnd + 1 < $totalPages) {
                $pageEnd++;
                $counter++;
            }

            if ($counter === 1 && $totalPages < 5) {
                break;
            }
        }

        return [$pageStart ?? 0, $pageEnd ?? 0];
    }

    public function changeAmountToDisplay(int $value)
    {
        $this->amountToDisplay = $value;
        // TODO re-render home
    }

    public function skipToPage(): array {}

    public function nextPage(): array {}

    public function prevPage(): array {}
}
