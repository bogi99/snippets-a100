<?php

/**
 * Machine Learning-based Profanity Detection
 * Uses Naive Bayes classification with n-gram analysis
 */
class MLProfanityDetector
{
    private $vocabulary = [];
    private $profaneWordFreq = [];
    private $cleanWordFreq = [];
    private $totalProfaneWords = 0;
    private $totalCleanWords = 0;
    private $trained = false;
    private $ngramSize = 3;

    public function __construct($ngramSize = 3)
    {
        $this->ngramSize = $ngramSize;
    }

    /**
     * Train the model using the bad words list and clean text samples
     */
    public function train($badWordsFile = '../bad-words.txt', $cleanSamplesFile = null)
    {
        echo "Training ML Profanity Detector...\n";

        // Load profane words
        $profaneWords = file($badWordsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        // Generate clean samples if not provided
        $cleanWords = $cleanSamplesFile ?
            file($cleanSamplesFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) :
            $this->generateCleanSamples();

        // Train on profane words
        foreach ($profaneWords as $word) {
            $word = trim(strtolower($word));
            if (!empty($word)) {
                $this->trainOnText($word, true);
            }
        }

        // Train on clean words
        foreach ($cleanWords as $word) {
            $word = trim(strtolower($word));
            if (!empty($word)) {
                $this->trainOnText($word, false);
            }
        }

        $this->trained = true;
        echo "Training completed!\n";
        echo "- Profane vocabulary: " . count($this->profaneWordFreq) . " n-grams\n";
        echo "- Clean vocabulary: " . count($this->cleanWordFreq) . " n-grams\n";
        echo "- Total profane words processed: " . $this->totalProfaneWords . "\n";
        echo "- Total clean words processed: " . $this->totalCleanWords . "\n";
    }

    /**
     * Train on a single text sample
     */
    private function trainOnText($text, $isProfane)
    {
        $ngrams = $this->generateNgrams($text);

        foreach ($ngrams as $ngram) {
            if ($isProfane) {
                $this->profaneWordFreq[$ngram] = ($this->profaneWordFreq[$ngram] ?? 0) + 1;
                $this->totalProfaneWords++;
            } else {
                $this->cleanWordFreq[$ngram] = ($this->cleanWordFreq[$ngram] ?? 0) + 1;
                $this->totalCleanWords++;
            }

            // Add to vocabulary
            $this->vocabulary[$ngram] = true;
        }
    }

    /**
     * Generate n-grams from text
     */
    private function generateNgrams($text)
    {
        $ngrams = [];
        $text = strtolower(preg_replace('/[^a-z0-9]/', '', $text));

        // Character-based n-grams
        for ($i = 0; $i <= strlen($text) - $this->ngramSize; $i++) {
            $ngrams[] = substr($text, $i, $this->ngramSize);
        }

        return array_unique($ngrams);
    }

    /**
     * Predict if text contains profanity using Naive Bayes
     */
    public function predict($text)
    {
        if (!$this->trained) {
            throw new Exception("Model must be trained before prediction");
        }

        $ngrams = $this->generateNgrams($text);

        // Calculate probabilities
        $profaneProbability = log(0.5); // Prior probability
        $cleanProbability = log(0.5);   // Prior probability

        foreach ($ngrams as $ngram) {
            // Laplace smoothing to handle unseen n-grams
            $profaneCount = ($this->profaneWordFreq[$ngram] ?? 0) + 1;
            $cleanCount = ($this->cleanWordFreq[$ngram] ?? 0) + 1;

            $profaneProb = $profaneCount / ($this->totalProfaneWords + count($this->vocabulary));
            $cleanProb = $cleanCount / ($this->totalCleanWords + count($this->vocabulary));

            $profaneProbability += log($profaneProb);
            $cleanProbability += log($cleanProb);
        }

        $confidence = abs($profaneProbability - $cleanProbability);
        $isProfane = $profaneProbability > $cleanProbability;

        return [
            'is_profane' => $isProfane,
            'confidence' => $confidence,
            'profane_score' => $profaneProbability,
            'clean_score' => $cleanProbability,
            'ngrams_analyzed' => count($ngrams)
        ];
    }

    /**
     * Simple prediction method (returns boolean)
     */
    public function containsProfanity($text, $threshold = 0.5)
    {
        $result = $this->predict($text);
        return $result['is_profane'] && $result['confidence'] > $threshold;
    }

    /**
     * Generate clean word samples for training
     */
    private function generateCleanSamples()
    {
        return [
            'hello',
            'world',
            'programming',
            'computer',
            'science',
            'technology',
            'development',
            'software',
            'application',
            'website',
            'database',
            'server',
            'client',
            'network',
            'security',
            'performance',
            'optimization',
            'algorithm',
            'data',
            'structure',
            'function',
            'method',
            'class',
            'object',
            'variable',
            'constant',
            'array',
            'string',
            'integer',
            'beautiful',
            'amazing',
            'wonderful',
            'fantastic',
            'excellent',
            'great',
            'good',
            'nice',
            'pleasant',
            'friendly',
            'helpful',
            'professional',
            'reliable',
            'efficient',
            'effective',
            'productive',
            'creative',
            'innovative',
            'modern',
            'advanced',
            'sophisticated',
            'user',
            'interface',
            'experience',
            'design',
            'layout',
            'style',
            'content',
            'information',
            'knowledge',
            'learning',
            'education',
            'training',
            'practice',
            'exercise',
            'project',
            'assignment',
            'business',
            'company',
            'organization',
            'team',
            'collaboration',
            'communication',
            'meeting',
            'discussion',
            'presentation',
            'report'
        ];
    }

    /**
     * Save trained model to file
     */
    public function saveModel($filename)
    {
        $modelData = [
            'vocabulary' => $this->vocabulary,
            'profane_freq' => $this->profaneWordFreq,
            'clean_freq' => $this->cleanWordFreq,
            'total_profane' => $this->totalProfaneWords,
            'total_clean' => $this->totalCleanWords,
            'ngram_size' => $this->ngramSize,
            'trained' => $this->trained
        ];

        file_put_contents($filename, serialize($modelData));
        echo "Model saved to: $filename\n";
    }

    /**
     * Load trained model from file
     */
    public function loadModel($filename)
    {
        if (!file_exists($filename)) {
            throw new Exception("Model file not found: $filename");
        }

        $modelData = unserialize(file_get_contents($filename));

        $this->vocabulary = $modelData['vocabulary'];
        $this->profaneWordFreq = $modelData['profane_freq'];
        $this->cleanWordFreq = $modelData['clean_freq'];
        $this->totalProfaneWords = $modelData['total_profane'];
        $this->totalCleanWords = $modelData['total_clean'];
        $this->ngramSize = $modelData['ngram_size'];
        $this->trained = $modelData['trained'];

        echo "Model loaded from: $filename\n";
    }
}
