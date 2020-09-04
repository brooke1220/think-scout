
<?php

namespace brooke\scout\tokenizers;

use Latrell\Scws\Scws;

class ScwsTokenizer extends Tokenizer
{
  protected $scws;

  public function __construct()
  {
      $this->scws = new Scws($this->getConfig('scws'));
  }

  public function getTokens($text)
  {
      $this->scws->sendText($text);

      $tokens = [];

      while ($result = $this->scws->getResult()) {
          $tokens = array_merge($tokens, array_column($result, 'word'));
      }

      return $tokens;
  }

  public function getScws()
  {
      return $this->scws;
  }
}
