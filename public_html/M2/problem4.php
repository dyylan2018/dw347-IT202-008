<?php

require_once "base.php";

$ucid = "dw347"; // <-- set your ucid


$array1 = ["hello world!", "php programming", "special@#$%^&characters", "numbers 123 456", "mIxEd CaSe InPut!"];
$array2 = ["hello world", "php programming", "this is a title case test", "capitalize every word", "mixEd CASE input"];
$array3 = ["  hello   world  ", "php    programming  ", "  extra    spaces  between   words   ",
    "      leading and trailing spaces      ", "multiple      spaces"];
$array4 = ["hello world", "php programming", "short", "a", "even"];


function transformText($arr, $arrayNumber) {
    // Only make edits between the designated "Start" and "End" comments
    printArrayInfoBasic($arr, $arrayNumber);

    // Challenge 1: Remove non-alphanumeric characters except spaces
    // Challenge 2: Convert text to Title Case
    // Challenge 3: Trim leading/trailing spaces and remove duplicate spaces
    // Result 1-3: Assign final phrase to `$placeholderForModifiedPhrase`
    // Challenge 4 (extra credit): Extract middle 3 characters (beginning starts at middle of phrase),
    // assign to `$placeholderForMiddleCharacters`
    // if not enough characters assign "Not enough characters"

    // Step 1: sketch out plan using comments (include ucid and date)
    // Step 2: Add/commit your outline of comments (required for full credit)
    // Step 3: Add code to solve the problem (add/commit as needed)
    $placeholderForModifiedPhrase = "";
    $placeholderForMiddleCharacters = "";
    foreach ($arr as $index => $text) {
        // Start Solution Edits
        //dw347 2-15-25
        // Step 1: Remove non-alphanumeric characters except spaces
        $cleanText = preg_replace('/[^a-zA-Z0-9 ]/', '', $text);

        // Step 2: Convert text to Title Case
        $titleCaseText = ucwords(strtolower($cleanText));

        // Step 3: Trim leading/trailing spaces and remove duplicate spaces
        $finalText = trim(preg_replace('/\s+/', ' ', $titleCaseText));

        // Step 4: Extract middle 3 characters (Extra Credit)
        $textLength = strlen($finalText);
        if ($textLength < 3) {
            $middleCharacters = "Not enough characters";
        } else {
            $middleIndex = floor($textLength / 2) - 1;
            $middleCharacters = substr($finalText, $middleIndex, 3);
        }

        // Assign final values to placeholders
        $placeholderForModifiedPhrase = $finalText;
        $placeholderForMiddleCharacters = $middleCharacters;


        

        // End Solution Edits

        printStringTransformations($index, $placeholderForModifiedPhrase, $placeholderForMiddleCharacters);
    }

    echo "<br>______________________________________<br>";
}

// Run the problem
printHeader($ucid, 4);
transformText($array1, 1);
transformText($array2, 2);
transformText($array3, 3);
transformText($array4, 4);
printFooter($ucid, 4);

?>