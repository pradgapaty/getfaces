<?php

declare(strict_types = 1);

namespace Helpers;

class Template
{

    public static function getGenderList(): array
    {
        return [
        "male",
        "female",
        ];
    }

    public static function getAgeList(): array
    {
        return [
        "18-25",
        "26-35",
        "36-45",
        "46-60",
        "61-99",
        ];
    }

    public static function getLimitList(): array
    {
        return [
        "10",
        "20",
        "50",
        "100",
        ];
    }

    public static function separateAge(string $ageRange): array
    {
        $ageArr = [];

        if (!empty($ageRange)) {
            $ageFrom = substr($ageRange, 0, 2);
            $ageTo = substr($ageRange, 3, 2);

            $ageArr = [
                "from" => $ageFrom,
                "to" => $ageTo,
            ];
        }

        return $ageArr;
    }

    public static function menuList(): array
    {
        return [
        "home" => "index",
        "donate" => "donate",
        "stats" => "statistics",
        "about" => "about",
        ];
    }
}