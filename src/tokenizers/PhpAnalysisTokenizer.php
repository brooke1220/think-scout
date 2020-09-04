<?php

namespace brooke\scout\tokenizers;

use Str;
use Phpanalysis\Phpanalysis;

class PhpAnalysisTokenizer extends Tokenizer
{
    protected $analysis;

    public function __construct()
    {
        $this->analysis = new Phpanalysis;

        foreach ($this->getConfig('phpanalysis') as $key => $value) {
            $key = Str::camel($key);

            if (property_exists($this->analysis, $key)) {
                $this->analysis->$key = $value;
            }
        }
    }

    public function getTokens($text)
    {
        $this->analysis->SetSource($text);

        $this->analysis->StartAnalysis();

        $result = $this->analysis->GetFinallyResult();

        $result = str_replace(['(', ')'], '', trim($result));

        return explode(' ', $result);
    }

    public function getAnalysis()
    {
        return $this->analysis;
    }
}
