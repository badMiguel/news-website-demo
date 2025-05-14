<?php

declare(strict_types=1);

class Paginator
{
    public int $amountToDisplay;
    private int $totalNews;
    public int $currentPage;
    private Model $model;

    public function __construct(Model $model)
    {
        $this->amountToDisplay = 5;
        $this->currentPage = 1;
        $this->model = $model;
        $this->totalNews = 0;
    }

    public function start(?string $category): array
    {
        $this->totalNews = $this->model->getTotalNewsCount($category);
        if ($category) {
            return $this->model->getNewsListCategory(0, $this->amountToDisplay, $category);
        }
        return $this->model->getNewsList(0, $this->amountToDisplay);
    }

    public function getTotalPages(): int
    {
        return (int) ceil($this->totalNews / $this->amountToDisplay);
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
        } else if ($totalPages === 3) {
            return [2, 3];
        } else if ($totalPages === 4) {
            return [2, 4];
        }

        $pageStart = $this->currentPage;
        $pageEnd = $this->currentPage;
        $pageToDisplay = 5;
        $maxCounter = $this->getTotalPages() - 2 < $pageToDisplay // excluding first and last page
            ? $this->getTotalPages() - 2
            : $pageToDisplay;
        $counter = 1;

        if ($this->currentPage === 1 && $totalPages > 3) {
            $pageEnd++;
        }

        if ($this->currentPage === $totalPages && $totalPages - 3 > 1) {
            $pageStart--;
        }

        while ($counter < $maxCounter) {

            if ($pageStart - 1 > 1) {
                $pageStart--;
                $counter++;
            }

            if ($pageEnd + 1 < $totalPages) {
                $pageEnd++;
                $counter++;
            }
        }

        return [$pageStart ?? 0, $pageEnd ?? 0];
    }

    public function skipToPage(int $currentPage): array
    {
        session_start();
        $_SESSION["currentPage"] = $currentPage;
        session_write_close();

        $this->currentPage = $currentPage;

        $currIdx = ($this->currentPage - 1) * $this->amountToDisplay;
        return $this->model->getNewsList($currIdx, $this->amountToDisplay);
    }

    public function changeAmountToDisplay(int $value): void
    {
        session_start();
        $_SESSION["amountToDisplay"] = $value;
        session_write_close();

        $this->amountToDisplay = $value;
    }
}
