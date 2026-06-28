<?php
// src/StageInterface.php

interface StageInterface {
    /**
     * Her aşama bir önceki aşamadan gelen veriyi (payload) alır,
     * işler ve bir sonraki aşamaya aktarır.
     */
    public function handle($payload);
}