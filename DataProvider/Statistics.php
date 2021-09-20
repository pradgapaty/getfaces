<?php

declare(strict_types = 1);

namespace DataProvider;

use Models\ParseThisperson;
use Models\ParseAnalysis;

class Statistics
{

    private $photoThispersonModel = null;

    public function __construct()
    {
        if (is_null($this->photoThispersonModel)) {
            $this->photoThispersonModel = new ParseThisperson();
        }
    }

    public function getCountTotalPhotos(): int
    {
        return $this->photoThispersonModel->getCountTotalPhotos();
    }

    public function getCountNeededAnalyzePhotos(): int
    {
        return $this->photoThispersonModel->getCountByStatus(1);
    }

    public function getCountAnalyzedPhotos(): int
    {
        return $this->photoThispersonModel->getCountByStatus(2);
    }

    public function getActiveAnalyzatorsCount(): int
    {
        $analyzatorsModel = new ParseAnalysis();
        return $analyzatorsModel->getActiveAnalyzatorsCount();
    }
}