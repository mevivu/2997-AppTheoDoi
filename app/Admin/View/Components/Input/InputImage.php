<?php

namespace App\Admin\View\Components\Input;

use App\Traits\ImageSystem;

class InputImage extends Input
{
    public string $name;
    public ?string $value;
    public string $default;
    public ?string $label;
    public ?string $sub;
    public string $width;
    public string $height;
    public string $displayUrl;
    public bool $isDefault;

    public function __construct(
        string $name = 'image',
        ?string $value = null,
        ?string $default = null,
        ?string $label = null,
        ?string $sub = null,
        string $width = '105px',
        string $height = '105px',
        bool $required = false
    ) {
        parent::__construct('file', $required);

        $this->name = $name;
        $this->value = $value;
        $this->default = $default ?: ImageSystem::DEFAULT_IMAGE;
        $this->label = $label;
        $this->sub = $sub;
        $this->width = $width;
        $this->height = $height;

        $resolved = $this->resolveDisplayUrl($this->value, $this->default);
        $this->displayUrl = $resolved['url'];
        $this->isDefault = $resolved['is_default'];
    }

    protected function resolveDisplayUrl(?string $value, string $default): array
    {
        $cleanDefault = ltrim(preg_replace('#^/?public/#', '', $default), '/');
        $filePath = public_path($cleanDefault);
        $v = file_exists($filePath) ? '?v=' . filemtime($filePath) : '';
        $defaultUrl = asset(ltrim($default, '/')) . $v;

        if (empty($value) || $value === $default) {
            return ['url' => $defaultUrl, 'is_default' => true];
        }

        // Absolute URL
        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            return ['url' => $value, 'is_default' => false];
        }

        // Clean redundant duplicate slashes
        $normalizedPath = preg_replace('#/+#', '/', trim($value, '/'));

        // Check relative to public directory
        $relativePublic = str_starts_with($normalizedPath, 'public/')
            ? substr($normalizedPath, 7)
            : $normalizedPath;

        if (file_exists(public_path($relativePublic)) || file_exists(base_path($normalizedPath))) {
            return ['url' => asset($normalizedPath), 'is_default' => false];
        }

        // Check in uploads/ directory if only relative filename/path stored
        if (file_exists(public_path('uploads/' . $relativePublic))) {
            return ['url' => asset('public/uploads/' . $relativePublic), 'is_default' => false];
        }

        // Fallback to default if file is missing/invalid string
        return ['url' => $defaultUrl, 'is_default' => true];
    }

    public function render()
    {
        return view('components.input.image');
    }
}
