<?php

declare(strict_types=1);

class Paginator
{
    public int $amountToDisplay;
    private int $totalNews;
    private array $newsList;
    public int $currentPage;

    public function __construct()
    {
        $this->amountToDisplay = 5;
        $this->currentPage = 1;
    }

    public function start(array $newsList)
    {
        $this->newsList = $newsList;
        $this->totalNews = count($newsList);

        return array_slice($this->newsList, 0, $this->amountToDisplay);
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

    public function skipToPage(int $currentPage): array
    {
        session_start();
        $_SESSION["currentPage"] = $currentPage;
        session_write_close();

        $this->currentPage = $currentPage;

        $currIdx = ($this->currentPage - 1) * $this->amountToDisplay;
        return array_slice($this->newsList, $currIdx, $this->amountToDisplay);
    }

    public function nextPage(): array
    {
        session_start();
        if ($this->currentPage + 1 > $this->getTotalPages()) {
            $currIdx = ($this->currentPage - 1) * $this->amountToDisplay;
            return array_slice($this->newsList, $currIdx, $this->amountToDisplay);
        }

        $this->currentPage++;

        $_SESSION["currentPage"] = $this->currentPage;
        session_write_close();

        $currIdx = ($this->currentPage - 1) * $this->amountToDisplay;
        return array_slice($this->newsList, $currIdx, $this->amountToDisplay);
    }

    public function prevPage(): array
    {
        session_start();
        if ($this->currentPage - 1 < 1) {
            $currIdx = ($this->currentPage - 1) * $this->amountToDisplay;
            return array_slice($this->newsList, $currIdx, $this->amountToDisplay);
        }

        $this->currentPage--;

        $_SESSION["currentPage"] = $this->currentPage;
        session_write_close();

        $currIdx = ($this->currentPage - 1) * $this->amountToDisplay;
        return array_slice($this->newsList, $currIdx, $this->amountToDisplay);
    }
}

