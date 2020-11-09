<?php

namespace Utils\Helper;

class Player {

    private $name = "";
    private $lifeValue = 0;

    /**
     * Set name's value
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Get name's value
     * 
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set life's value
     */
    public function setLifeValue($lifeValue) {
        $this->lifeValue = $lifeValue;
    }

    /**
     * Get life's value
     * 
     * @return int
     */
    public function getLifeValue() {
        return $this->lifeValue;
    }

    /**
     * Return a string representation of a player
     * 
     * @return string
     */
    public function toString() {
        return $this->name . " (Live value:" . $this->lifeValue . ")";
    }

}
