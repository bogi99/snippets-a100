<?php

trait FindProfanity
{
    private $profanityList = null;

    private function getProfanityList()
    {
        if ($this->profanityList === null) {
            $rawList = file('bad-words.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $this->profanityList = [];

            foreach ($rawList as $profanity) {
                $cleanWord = trim(strtolower($profanity));
                if (!empty($cleanWord)) {
                    $this->profanityList[] = $cleanWord;
                }
            }
        }

        return $this->profanityList;
    }

    public function containsProfanity($text)
    {
        $profanities = $this->getProfanityList();
        $text = strtolower($text);

        foreach ($profanities as $profanity) {
            if (stripos($text, $profanity) !== false) {
                return true;
            }
        }

        return false;
    }

    public function findProfanities($text)
    {
        $profanities = $this->getProfanityList();
        $text = strtolower($text);
        $found = [];

        foreach ($profanities as $profanity) {
            if (stripos($text, $profanity) !== false) {
                $found[] = $profanity;
            }
        }

        return $found;
    }
}
