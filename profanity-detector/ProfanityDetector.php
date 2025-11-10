<?php

require_once 'FindProfanity.php';

class ProfanityDetector
{
    use FindProfanity;
}

// Example usage
$detector = new ProfanityDetector();

// Test strings
$testStrings = [
    "This is a clean sentence.",
    "This contains a bad word like damn.",
    "What the hell is going on?",
    "This is completely appropriate text.",
    "You're such an idiot!",
    "I love programming in PHP!"
];

echo "=== Profanity Detection Results ===\n\n";

foreach ($testStrings as $index => $text) {
    echo "Text " . ($index + 1) . ": \"$text\"\n";

    if ($detector->containsProfanity($text)) {
        echo "Result: ⚠️  PROFANITY DETECTED\n";

        $foundProfanities = $detector->findProfanities($text);
        echo "Found words: " . implode(', ', $foundProfanities) . "\n";
    } else {
        echo "Result: ✅ Clean text\n";
    }

    echo str_repeat("-", 50) . "\n";
}

// Interactive example
echo "\n=== Interactive Test ===\n";
echo "Enter a text to check for profanity (or 'quit' to exit):\n";

while (true) {
    $input = trim(fgets(STDIN));

    if (strtolower($input) === 'quit' || empty($input)) {
        echo "Goodbye!\n";
        break;
    }

    if ($detector->containsProfanity($input)) {
        $foundProfanities = $detector->findProfanities($input);
        echo "⚠️  PROFANITY DETECTED: " . implode(', ', $foundProfanities) . "\n";
    } else {
        echo "✅ Clean text\n";
    }

    echo "Enter another text (or 'quit' to exit):\n";
}
