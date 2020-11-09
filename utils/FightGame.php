<?php

namespace Utils\Helper;

use Utils\Helper\Player;

class FightGame {

    public $playerOne;
    public $playerTwo;
    // if it's not the player one's turn, then it's the player two's one
    public $isPlayerOnesTurn = true;
    public $recorder = [];

    public function __construct() {
        $this->playerOne = new Player();
        $this->playerTwo = new Player();
    }

    /**
     * Update players' properties according to the rules of the fight game
     */
    private function applyTurnsEffects($punchNumber, $sourcePlayer, $destinationPlayer) {
        $sourcePlayerName = $sourcePlayer->getName();
        $destinationPlayerName = $destinationPlayer->getName();
        array_push($this->recorder, "$sourcePlayerName attacks $destinationPlayerName by $punchNumber");
        $destinationPlayer->setLifeValue($destinationPlayer->getLifeValue() - $punchNumber);
    }

    /**
     * Pick a random number as punch value and then apply the effets according to who is the thrower.
     * Then, it switches isPlayerOnesTurn's value according to the previous thrower for the next turn.
     */
    public function playTurn() {
        //echo(PHP_EOL."New turn!".PHP_EOL);
        $punchNumber = random_int(1, 10);
        //echo(PHP_EOL."\t- Punch $punchNumber".PHP_EOL);
        if ($this->isPlayerOnesTurn) {
            $this->applyTurnsEffects($punchNumber, $this->playerOne, $this->playerTwo);
        } else {
            $this->applyTurnsEffects($punchNumber, $this->playerTwo, $this->playerOne);
        }
        $this->isPlayerOnesTurn = !$this->isPlayerOnesTurn;

        //echo("\t- ".$this->playerOne->toString().PHP_EOL);
        //echo("\t- ".$this->playerTwo->toString().PHP_EOL);
    }

    /**
     * Check if one of the players is dead, if this is the case, the party is over
     * 
     * @return boolean
     */
    public function isOver() {
        return $this->playerOne->getLifeValue() < 1 || $this->playerTwo->getLifeValue() < 1;
    }

    /**
     * Init. the two players of the game with arbitrary given values
     */
    public function initThePlayers($firstPlayerName, $secondPlayerName) {
        $this->playerOne->setName($firstPlayerName);
        $this->playerOne->setLifeValue(100);
        //echo("Here is the player one:".PHP_EOL);
        //echo("\t- ".$this->playerOne->toString().PHP_EOL);

        $this->playerTwo->setName($secondPlayerName);
        $this->playerTwo->setLifeValue(100);
        //echo("Here is the player two:".PHP_EOL);
        //echo("\t- ".$this->playerTwo->toString().PHP_EOL);
    }

    /**
     * Simulate a party by playing successive turns until one of the player is dead
     */
    /* public function play(){
      $this->initThePlayers();
      //echo(PHP_EOL."3...2...1... GO!".PHP_EOL);

      while(!$this->isOver()){
      $this->playTurn();
      }
      //echo(PHP_EOL."GAME!".PHP_EOL);
      $winner = $this->playerOne->getName();
      if($this->playerTwo->getLifeValue() > $this->playerOne->getLifeValue())
      $winner = $this->playerTwo->getName();
      //echo("\tThe winner is: ".$winner."!".PHP_EOL);
      } */
}
