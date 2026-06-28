<?php
// src/Pipeline.php

class Pipeline {
    private array $stages = [];

    // Pipeline'a yeni bir adım ekler
    public function pipe(StageInterface $stage): self {
        $this->stages[] = $stage;
        return $this;
    }

    // Süreci başlatır ve akan veriyi yönetir
    public function process($initialPayload) {
        $payload = $initialPayload;

        foreach ($this->stages as $stage) {
            $payload = $stage->handle($payload);
        }

        return $payload;
    }
}