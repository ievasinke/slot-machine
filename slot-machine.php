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
$totalWinnings = 0;

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

echo "Welcome! Let's play a game.\n";
$totalTokens = (int)readline("Enter amount of tokens to play: ");

if ($totalTokens < $betTokens) {
    exit("Invalid value.\n");
}

echo "Minimum for the BET are 5 tokens.\n";
echo "1 to place as a single BET.\n";
echo "2 to double the BET.\n";
echo "3 to triple the BET etc.\n";
$betMultiplier = (int)readline("Enter number to multiply the BET: ");
$betTokens *= $betMultiplier;

if ($betMultiplier < 1 || $betTokens > $totalTokens) {
    exit("Game over. See you soon!");
}

echo "Your BET is $betTokens tokens.\n";

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

    foreach ($elements as $element => $weight) {
        foreach ($winningPatterns as $pattern) {
            $patternFound = true;

            foreach ($pattern as $coordinate) {
                list($row, $column) = $coordinate;

                if ($board[$row][$column] !== $element) {
                    $patternFound = false;
                    break;
                }
            }
            if ($patternFound) {
                $winningAmount += round((10 * $betTokens) / $weight);
            }
        }
    }
    if ($winningAmount > 0) {
        echo "You won $winningAmount\n";
        $totalWinnings += $winningAmount;
    } else {
        echo "No winning pattern found\n";
    }
    echo "Tokens left: $totalTokens\n";
}

exit("Thank you for playing! You won $totalWinnings.\n");