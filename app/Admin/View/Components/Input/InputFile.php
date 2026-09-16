<?php

namespace App\Admin\View\Components\Input;

class InputFile extends Input
{
    public string $name;
    public ?string $value;
    public string $accept;
    public ?string $label;
    public ?string $sub;
    public bool $isAudio;
    public ?string $fileUrl;
    public ?string $fileName;

    public function __construct(
        string $name = 'file',
        ?string $value = null,
        string $accept = '*/*',
        ?string $label = null,
        ?string $sub = null,
        ?bool $isAudio = null,
        bool $required = false
    ) {
        parent::__construct('file', $required);

        $this->name = $name;
        $this->value = $value;
        $this->accept = $accept;
        $this->label = $label;
        $this->sub = $sub;

        if ($isAudio !== null) {
            $this->isAudio = $isAudio;
        } else {
            $this->isAudio = str_contains($this->accept, 'audio') ||
                in_array(pathinfo($this->value ?? '', PATHINFO_EXTENSION), ['mp3', 'wav', 'ogg', 'm4a']);
        }

        if (!empty($this->value)) {
            $this->fileUrl = asset($this->value);
            $this->fileName = basename($this->value);
        } else {
            $this->fileUrl = null;
            $this->fileName = null;
        }
    }

    public function render()
    {
        return view('components.input.file');
    }
}
