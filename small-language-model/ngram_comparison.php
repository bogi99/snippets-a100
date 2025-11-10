<?php

require_once 'MLProfanityDetector.php';

echo "=== N-gram Size Comparison Test ===\n\n";

// Create models with different n-gram sizes
$detector3 = new MLProfanityDetector(3); // 3-character n-grams
$detector4 = new MLProfanityDetector(4); // 4-character n-grams
$detector5 = new MLProfanityDetector(5); // 5-character n-grams for comparison

echo "Training models with different n-gram sizes...\n";

// Train all models
$detector3->train('bad-words.txt');
$detector4->train('bad-words.txt');
$detector5->train('bad-words.txt');

echo "Training completed!\n\n";

// Test cases to see the differences
$testCases = [
    "damn",           // Short word
    "hello",          // Clean short word
    "assessment",     // Contains "ass" - false positive test
    "damnation",      // Longer word with profanity root
    "This is damn frustrating",
    "What the hell happened",
    "Classical music",
    "d4mn",           // Obfuscated
    "h3ll",           // Obfuscated
    "fu**ing",        // Censored
];

echo "=== Prediction Results by N-gram Size ===\n\n";
echo sprintf("%-25s %-15s %-15s %-15s\n", "Text", "3-gram", "4-gram", "5-gram");
echo str_repeat("=", 80) . "\n";

foreach ($testCases as $text) {
    $result3 = $detector3->predict($text);
    $result4 = $detector4->predict($text);
    $result5 = $detector5->predict($text);

    $status3 = $result3['is_profane'] ? "PROFANE" : "CLEAN";
    $status4 = $result4['is_profane'] ? "PROFANE" : "CLEAN";
    $status5 = $result5['is_profane'] ? "PROFANE" : "CLEAN";

    echo sprintf(
        "%-25s %-15s %-15s %-15s\n",
        substr($text, 0, 22) . (strlen($text) > 22 ? "..." : ""),
        $status3,
        $status4,
        $status5
    );
}

echo "\n=== N-gram Analysis Details ===\n\n";

// Analyze a specific word to show n-gram differences
$analysisWord = "damn";
echo "Analyzing word: '$analysisWord'\n";

// Manually generate n-grams to show the difference
function generateNgramsManual($text, $size)
{
    $ngrams = [];
    $text = strtolower(preg_replace('/[^a-z0-9]/', '', $text));

    for ($i = 0; $i <= strlen($text) - $size; $i++) {
        $ngrams[] = substr($text, $i, $size);
    }

    return $ngrams;
}

echo "3-grams: " . implode(", ", generateNgramsManual($analysisWord, 3)) . "\n";
echo "4-grams: " . implode(", ", generateNgramsManual($analysisWord, 4)) . "\n";
echo "5-grams: " . implode(", ", generateNgramsManual($analysisWord, 5)) . "\n\n";

// Performance comparison
echo "=== Performance Comparison ===\n\n";

$testText = "This is a sample text for performance testing";
$iterations = 100;

// Test 3-gram performance
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $detector3->containsProfanity($testText);
}
$time3 = (microtime(true) - $start) * 1000;

// Test 4-gram performance
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $detector4->containsProfanity($testText);
}
$time4 = (microtime(true) - $start) * 1000;

// Test 5-gram performance
$start = microtime(true);
for ($i = 0; $i < $iterations; $i++) {
    $detector5->containsProfanity($testText);
}
$time5 = (microtime(true) - $start) * 1000;

echo "Performance ($iterations iterations):\n";
echo "- 3-gram: " . number_format($time3, 2) . " ms\n";
echo "- 4-gram: " . number_format($time4, 2) . " ms\n";
echo "- 5-gram: " . number_format($time5, 2) . " ms\n\n";

echo "=== N-gram Size Analysis ===\n\n";

echo "**3-gram Effects:**\n";
echo "✅ More n-grams per word = more features\n";
echo "✅ Better for short words (3-4 characters)\n";
echo "✅ Faster training and prediction\n";
echo "❌ Less specific patterns\n";
echo "❌ More noise from common letter combinations\n\n";

echo "**4-gram Effects:**\n";
echo "✅ More specific character patterns\n";
echo "✅ Better discrimination between similar words\n";
echo "✅ Reduced false positives from common trigrams\n";
echo "❌ Fewer features for short words\n";
echo "❌ Slightly slower processing\n";
echo "❌ May miss patterns in very short profanity\n\n";

echo "**5-gram Effects:**\n";
echo "✅ Very specific patterns\n";
echo "✅ Excellent for longer words\n";
echo "❌ Many short words produce no 5-grams\n";
echo "❌ Sparse feature space\n";
echo "❌ May underfit on short profanity\n\n";

echo "**Recommendation:**\n";
echo "- Use 3-grams for general purpose detection\n";
echo "- Use 4-grams when false positives are a major concern\n";
echo "- Use 5-grams for specialized longer-word detection\n";
echo "- Consider ensemble approach: combine multiple n-gram sizes\n";
