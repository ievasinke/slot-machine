<?php

/*
 * - Allow enter start amount of virtual coins to play with
 * - Allow to set BET amount per single spin
 * - Continuously play while there is enough coins
 * - Win amount should be sized based on step/size per BET. If base bet is 5
 *  but I set 10 it should give me twice the win per win condition.
 * If there are elements of 3 that each gives 5,
 *  and I bet twice (10) then it should be 3*5*2 = 30 as win amount.
 * - There should be option to change board size with few lines of code
 * - There should be option to define win conditions with few lines of code
 */

$betTokens = 5;
$columns = 5;
$rows = 3;
$totalTokens = 0;

$board = [];
$elements = [
    "$" => 1,
    "*" => 13,
    "+" => 4,
    "M" => 7,
    "#" => 2,
    "=" => 15,
    "@" => 24
];
$winningPatterns = [
    [[0, 0], [0, 1], [0, 2], [0, 3], [0, 4]],
    [[1, 0], [1, 1], [1, 2], [1, 3], [1, 4]],
    [[2, 0], [2, 1], [2, 2], [2, 3], [2, 4]],
    [[0, 0], [1, 1], [2, 2], [1, 3], [0, 4]],
    [[2, 0], [1, 1], [0, 2], [1, 3], [2, 4]],
    [[1, 0], [0, 1], [0, 2], [0, 3], [1, 4]],
    [[1, 0], [2, 1], [2, 2], [2, 3], [1, 4]],
    [[0, 0], [1, 1], [1, 2], [1, 3], [0, 4]],
    [[2, 0], [1, 1], [1, 2], [1, 3], [2, 4]],
    [[0, 0], [1, 1], [0, 2], [1, 3], [0, 4]],
    [[1, 0], [0, 1], [1, 2], [0, 3], [1, 4]],
    [[2, 0], [1, 1], [2, 2], [1, 3], [2, 4]],
    [[1, 0], [2, 1], [1, 2], [2, 3], [1, 4]],
    [[2, 0], [2, 1], [1, 2], [0, 3], [0, 4]],
    [[0, 0], [0, 1], [1, 2], [2, 3], [2, 4]]
];

function weightedSample(array $elements): string
{
    $totalWeight = array_sum($elements);
    $randomIndex = mt_rand(1, $totalWeight);
    foreach ($elements as $element => $weight) {
        $randomIndex -= $weight;
        if ($randomIndex <= 0) {
            return $element;
        }
    }
    return key($elements);
}

function setBet(int $betTokens, int $totalTokens): int
{
    echo "Minimum for the BET are 5 tokens.\n";
    echo "1 to place as a single BET.\n";
    echo "2 to double the BET.\n";
    echo "3 to triple the BET etc.\n";
    $betMultiplier = (int)readline("Enter number to multiply the BET: ");
    $betTokens *= $betMultiplier;
    echo "Your BET is $betTokens tokens.\n";
    if ($betMultiplier < 1 || $betTokens > $totalTokens) {
        exit("Game over. See you soon!\n");
    }
    return $betTokens;
}

echo "Welcome! Let's play a game.\n";
$totalTokens = (int)readline("Enter amount of tokens to play: ");

if ($totalTokens < $betTokens) {
    exit("Invalid value.\n");
}

$betTokens = setBet($betTokens, $totalTokens);

while ($totalTokens >= $betTokens) {
    $totalTokens -= $betTokens;
    $winningAmount = 0;

    for ($row = 0; $row < $rows; $row++) {
        for ($column = 0; $column < $columns; $column++) {
            $board[$row][$column] = weightedSample($elements);
        }
    }
    foreach ($board as $row) {
        foreach ($row as $column) {
            echo " $column";
        }
        echo PHP_EOL;
    }

    foreach ($winningPatterns as $pattern) {
        $winningElements = [];

        foreach ($pattern as $coordinate) {
            list($row, $column) = $coordinate;
            $winningElements[] = $board[$row][$column];
        }
        if (count(array_count_values($winningElements)) === 1) {
            $weight = $elements[$winningElements[0]];
            $winningAmount += round((30 * $betTokens) / $weight);
            $totalTokens += $winningAmount;
        }
    }

    if ($winningAmount > 0) {
        echo "You won $winningAmount\n";
    } else {
        echo "No winning pattern found\n";
    }
    echo "Tokens left: $totalTokens\n";

    $choice = 0;
    if ($totalTokens >= $betTokens) {
        $choice = (int)readline("Do you want to continue (1), change the bet (2), anything to exit: ");
    }
    if ($choice === 1) {
        continue;
    }
    if ($choice === 2) {
        $betTokens = 5;
        $betTokens = setBet($betTokens, $totalTokens);
        continue;
    }
    break;
}

exit("Thank you for playing!\n");