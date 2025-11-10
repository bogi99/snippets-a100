# Machine Learning Profanity Detection

A PHP-based machine learning approach to profanity detection using Naive Bayes classification and n-gram analysis.

## Features

### 🧠 **Machine Learning Capabilities**
- **Naive Bayes Classification**: Statistical approach to text classification
- **N-gram Analysis**: Character-level pattern recognition (default: 3-grams)
- **Obfuscation Detection**: Catches variations like "d4mn", "h3ll", "fu**"
- **Context Awareness**: Better understanding than simple word matching
- **Trainable Model**: Can be retrained with new datasets

### ⚡ **Performance & Efficiency**
- **Model Persistence**: Save and load trained models
- **Memory Optimization**: Trained model cached in memory
- **Laplace Smoothing**: Handles unseen n-grams gracefully

## How It Works

### 1. **Training Phase**
```php
$detector = new MLProfanityDetector(3); // 3-character n-grams
$detector->train('bad-words.txt');
$detector->saveModel('trained_model.dat');
```

### 2. **Prediction Phase**
```php
$detector->loadModel('trained_model.dat');
$result = $detector->predict("This d4mn thing is broken");
// Returns: ['is_profane' => true, 'confidence' => 0.85, ...]
```

### 3. **N-gram Generation**
Text: "damn" → N-grams: ["dam", "amn"]
- Each n-gram gets frequency counts for profane vs clean categories
- Prediction uses Bayesian probability calculation

## Advantages over Simple Word Lists

| Feature | ML Model | Simple Word List |
|---------|----------|------------------|
| **Obfuscation Detection** | ✅ Catches "d4mn", "h3ll" | ❌ Misses variations |
| **Context Understanding** | ✅ Better accuracy | ❌ Literal matching only |
| **False Positives** | ✅ Fewer (e.g., "Assessment") | ❌ More common |
| **Retraining** | ✅ Adaptable to new data | ❌ Manual list updates |
| **Performance** | ❌ ~135x slower | ✅ Very fast |
| **Memory Usage** | ❌ Higher | ✅ Minimal |
| **Complexity** | ❌ More complex | ✅ Simple |

## Performance Comparison

```
1000 iterations test:
- ML Model: ~10ms  
- Simple Model: ~0.07ms
- Trade-off: 135x slower but much smarter detection
```

## Use Cases

### **When to Use ML Model:**
- User-generated content platforms
- Social media filtering
- Chat applications with creative spelling
- Content moderation systems
- When obfuscation is common

### **When to Use Simple Model:**
- High-performance APIs
- Real-time chat (speed critical)
- Simple form validation
- Resource-constrained environments
- When word list is comprehensive

## Future Enhancements

### 🔮 **Possible Improvements**
- **Multi-language Support**: Train models for different languages
- **Word Embeddings**: Use pre-trained vectors for semantic understanding
- **Deep Learning**: Neural network approaches (would need PHP-ML library)
- **Ensemble Methods**: Combine multiple models
- **Real-time Learning**: Update model based on user feedback

### 📊 **Advanced Features**
- **Severity Scoring**: Rate profanity intensity
- **Category Classification**: Type of profanity (violence, sexual, etc.)
- **Context Analysis**: Sentence-level understanding
- **False Positive Learning**: Adapt to reduce incorrect flags

## Technical Details

### **Algorithm: Naive Bayes**
- P(profane|text) = P(text|profane) × P(profane) / P(text)
- Uses character n-grams as features
- Logarithmic probability to prevent underflow
- Laplace smoothing for unknown n-grams

### **Model Storage**
- Serialized PHP arrays
- Vocabulary, frequency counts, totals
- Persistent across sessions
- ~50KB typical model size

This represents a significant step up in sophistication while remaining pure PHP!