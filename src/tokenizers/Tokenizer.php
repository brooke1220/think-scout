<?php

namespace brooke\scout\tokenizers;

use TeamTNT\TNTSearch\Support\TokenizerInterface;

abstract class Tokenizer implements TokenizerInterface
{
    public function getConfig($name)
    {
        $config = config("tntsearch.tokenizers.{$name}");

        unset($config['driver']);

        return $config;
    }

    public function tokenize($text, $stopwords = [])
    {
        $tokens = $this->getTokens($text);

        $tokens = array_filter($tokens, 'trim');

        return array_diff($tokens, $stopwords);
    }

    abstract protected function getTokens($text);
}
