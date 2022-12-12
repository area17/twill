<?php

namespace A17\Twill\Services\Forms\Fields\Traits;

trait HasFieldNote
{
    protected ?string $fieldNote = null;

    /**
     * Adds a note.
     */
    public function fieldNote(string $fieldNote): self
    {
        $this->fieldNote = $fieldNote;
        return $this;
    }
}
