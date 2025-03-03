<?php
include __DIR__ . '/persons_array.php';

// Разбиение и объединение ФИО

function getFullnameFromParts($surname, $name, $patronomyc)
{
    return $surname . " " . $name . " " . $patronomyc;
};

function getPartsFromFullname($fullName)
{
    $keys = ["surname", "name", "patronomyc"];
    $values = explode(" ", $fullName);
    return array_combine($keys, $values);
};

// Сокращение ФИО

function getShortName($fullName)
{
    $name = getPartsFromFullname($fullName)["name"];
    $surname = getPartsFromFullname($fullName)["surname"];
    return $name . " " . mb_substr($surname, 0, 1) . ".";
}

// Функция определения пола по ФИО

function getGenderFromName($fullName)
{
    $gender = 0;
    $surname = getPartsFromFullname($fullName)["surname"];
    $name = getPartsFromFullname($fullName)["name"];
    $patronomyc = getPartsFromFullname($fullName)["patronomyc"];
    if (
        mb_substr($patronomyc, mb_strlen($patronomyc) - 3) === "вна"
    ) {
        $gender -= 1;
    } elseif (
        mb_substr($patronomyc, mb_strlen($patronomyc) - 2) === "ич"
    ) {
        $gender += 1;
    }
    if (
        mb_substr($name, mb_strlen($name) - 1) === "а"
    ) {
        $gender -= 1;
    } elseif (
        (mb_substr($name, mb_strlen($name) - 1) === "й" || mb_substr($name, mb_strlen($name) - 1) === "н")
    ) {
        $gender += 1;
    }
    if (
        mb_substr($surname, mb_strlen($surname) - 2) === "ва"
    ) {
        $gender -= 1;
    } elseif (
        mb_substr($surname, mb_strlen($surname) - 1) === "в"
    ) {
        $gender += 1;
    }
    if ($gender > 0) {
        return 1;
    } elseif ($gender < 0) {
        return -1;
    } else {
        return 0;
    }
}

// Исправление регистра шрифта для имени

function upperCaseFirst($str)
{
    $firstChar = mb_strtoupper(mb_substr($str, 0, 1));
    $otherChars = mb_strtolower(mb_substr($str, 1));
    return $firstChar . $otherChars;
}

// Определение возрастно-полового состава

function getGenderDescription($personsArrow)
{
    $male = array_filter($personsArrow, function ($arr) {
        return getGenderFromName($arr["fullname"]) === 1;
    });
    $female = array_filter($personsArrow, function ($arr) {
        return getGenderFromName($arr["fullname"]) === -1;
    });
    $notGender = array_filter($personsArrow, function ($arr) {
        return getGenderFromName($arr["fullname"]) === 0;
    });
    $percentMale = round(count($male) / count($personsArrow) * 1000) / 10;
    $percentFemale = round(count($female) / count($personsArrow) * 1000) / 10;
    $percentNotGender = round(count($notGender) / count($personsArrow) * 1000) / 10;
    return <<<RESULT

    Гендерный состав аудитории:
    -----------------------------
    Мужчины - $percentMale%
    Женщины - $percentFemale%
    Не удалось определить - $percentNotGender%
    
    RESULT;
}

// Идеальный подбор пары

function getPerfectPartner($perfSurname, $perfName, $perfPatronomyc, $personsArrow)
{
    $newFullName = getFullnameFromParts(upperCaseFirst($perfSurname), upperCaseFirst($perfName), upperCaseFirst($perfPatronomyc));
    $newGender = getGenderFromName($newFullName);
    $gender = 0;
    while ($gender === $newGender || $gender === 0 || $newGender === 0) {
        $fullName = $personsArrow[random_int(0, count($personsArrow) - 1)]['fullname'];
        $gender = getGenderFromName($fullName);
    }

    $newShortName = getShortName($newFullName);
    $shortName = getShortName($fullName);
    $compatibility = random_int(5000, 10000) / 100;
    return <<<EOS
    $newShortName + $shortName =
    ♡ Идеально на $compatibility% ♡;
    EOS;
}
print_r(getShortName('Петрякова Валентина Фёдоровна') . "\n");