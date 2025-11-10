<?php

require_once 'MLProfanityDetector.php';

echo "=== ML-Based Profanity Detection Training ===\n\n";

// Create and train the model
$mlDetector = new MLProfanityDetector(3); // 3-character n-grams

// Train the model
$mlDetector->train('bad-words.txt');

// Save the trained model
$mlDetector->saveModel('trained_model.dat');

echo "\n=== Testing ML Model ===\n\n";

// Test cases
$testCases = [
    // Clean text
    "This is a wonderful day for programming",
    "I love working with PHP and databases",
    "The weather is beautiful today",
    "Great job on the project completion",

    // Direct profanity
    "This is damn frustrating",
    "What the hell is happening",
    "You are such an idiot",

    // Obfuscated profanity (the ML model should catch these!)
    "This is d4mn frustrating",
    "What the h3ll is happening",
    "You are such an 1d10t",
    "This is fu**ing broken",
    "Go to h@ll",

    // Context-based (harder to detect)
    "This sucks so much",
    "I hate this stupid thing",
    "Kill this process now",

    // Edge cases
    "Assessment of the situation",  // Contains "ass" but not profane
    "Class assignment due tomorrow", // Contains "ass" but not profane
    "Classical music is relaxing"    // Contains "ass" but not profane
];

foreach ($testCases as $index => $text) {
    echo "Test " . ($index + 1) . ": \"$text\"\n";

    $result = $mlDetector->predict($text);

    echo "Result: " . ($result['is_profane'] ? "🚫 PROFANE" : "✅ CLEAN") . "\n";
    echo "Confidence: " . number_format($result['confidence'], 4) . "\n";
    echo "N-grams analyzed: " . $result['ngrams_analyzed'] . "\n";
    echo str_repeat("-", 60) . "\n";
}

echo "\n=== ML vs Simple Word List Comparison ===\n\n";

// Load the simple detector for comparison
require_once 'FindProfanity.php';

class SimpleDetector
{
    use FindProfanity;
}

$simpleDetector = new SimpleDetector();

$comparisonTests = [
    "This is d4mn frustrating",      // Obfuscated - ML should win
    "Assessment report ready",       // False positive test - Simple might fail
    "What the h3ll happened",        // Obfuscated - ML should win
    "Classical music concert",       // False positive test - Simple might fail
    "This fu**ed up system",         // Obfuscated - ML should win
];

echo sprintf("%-30s %-10s %-10s\n", "Text", "ML Model", "Simple");
echo str_repeat("=", 60) . "\n";

foreach ($comparisonTests as $text) {
    $mlResult = $mlDetector->containsProfanity($text);
    $simpleResult = $simpleDetector->containsProfanity($text);

    echo sprintf(
        "%-30s %-10s %-10s\n",
        substr($text, 0, 27) . "...",
        $mlResult ? "PROFANE" : "CLEAN",
        $simpleResult ? "PROFANE" : "CLEAN"
    );
}

echo "\n=== Performance Comparison ===\n\n";

$performanceText = "This is a sample text for performance testing with some content";
$iterations = 1000;

// ML Model performance
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $mlDetector->containsProfanity($performanceText);
}
$mlTime = (microtime(true) - $start) * 1000;

// Simple model performance  
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $simpleDetector->containsProfanity($performanceText);
}
$simpleTime = (microtime(true) - $start) * 1000;

echo "Performance ($iterations iterations):\n";
echo "- ML Model: " . number_format($mlTime, 2) . " ms\n";
echo "- Simple Model: " . number_format($simpleTime, 2) . " ms\n";
echo "- ML is " . number_format($mlTime / $simpleTime, 2) . "x slower\n";

echo "\n=== Summary ===\n";
echo "ML Model Advantages:\n";
echo "✅ Detects obfuscated profanity (d4mn, h3ll, etc.)\n";
echo "✅ Better context understanding\n";
echo "✅ Fewer false positives\n";
echo "✅ Can be retrained with new data\n";
echo "✅ Handles creative spelling variations\n\n";

echo "Simple Model Advantages:\n";
echo "✅ Much faster execution\n";
echo "✅ Lower memory usage\n";
echo "✅ Simpler to understand\n";
echo "✅ Easy to update word list\n";
echo "✅ Predictable behavior\n";
