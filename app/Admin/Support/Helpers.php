<?php

use Illuminate\Support\Str;

if (! function_exists('generate_text_depth_tree')) {
    /**
     * Tạo text theo độ sâu.
     *
     * @param integer $depth
     */
    function generate_text_depth_tree($depth, $word = '-'): string
    {
        $text = '';
        if($depth > 0){
            for($i = 0; $i < $depth; $i++){
                $text .= $word;
            }
        }
        return $text;
    }
}
if (! function_exists('uniqid_real')) {
    function uniqid_real($lenght = 13): string
    {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new \Exception("no cryptographically secure random function available");
        }
        return Str::upper(substr(bin2hex($bytes), 0, $lenght));
    }
}

if (!function_exists('format_price')) {
    function format_price($price, $positionCurrent = ''): string
    {
        if ($positionCurrent == '') {
            $positionCurrent = config('custom.format.position_currency');
        }
        return $positionCurrent == 'left' ? config('custom.currency') . number_format($price) : number_format($price) . config('custom.currency');
    }
}

if (!function_exists('format_date')) {
    /*
     * @param string|\DateTimeInterface $date
     * @param string $format
     * @return string
     */
    /**
     * @throws Exception
     */
    function format_date($date, $format = 'Y-m-d'): string
    {
        if (is_string($date)) {
            $date = new DateTime($date);
        }
        return $date->format($format);
    }
}


if (!function_exists('format_datetime')) {
    function format_datetime($datetime, $format = null): ?string
    {
        if ($datetime) {
            $format = $format ?: config('custom.format.datetime');
            return date($format, strtotime($datetime));
        }
        return null;
    }
}
if (!function_exists('format_time')) {
    /**
     * Formats the time portion of a datetime string.
     *
     * @param string|null $datetime The datetime string to format.
     * @param string|null $format The time format to use, defaults to a configuration or 'H:i:s'.
     * @return string|null Formatted time or null if input is null.
     */
    function format_time($datetime, $format = null): ?string
    {
        if ($datetime) {
            // Set the default format from configuration or use 'H:i:s' if not configured
            $format = $format ?: config('custom.format.time', 'H:i:s');
            return date($format, strtotime($datetime));
        }
        return null;
    }
}


if (!function_exists('formatImageUrl')) {
    function formatImageUrl($url)
    {
        if ($url) {
            $url = preg_replace('/\/+/', '/', $url);

            if (!preg_match('/^\/public\//', $url)) {
                $url = '/' . $url;
            }

            return $url;
        }
        return null;
    }
}


if (!function_exists('statusBadge')) {
    /**
     * Tạo HTML cho badge trạng thái sử dụng Enum.
     *
     * @return string HTML cho badge.
     */
    function statusBadge($statusEnum): string
    {
        $description = $statusEnum->description();
        $badgeClass = $statusEnum->badge();

        return "<span class='badge $badgeClass'>$description</span>";
    }
}

if (!function_exists('statusStringBadge')) {
    function statusStringBadge($statusEnum): string
    {
        $description = $statusEnum->description();
        $badgeClass = $statusEnum->badge();

        return "<span class='badge $badgeClass'>$description</span>";
    }
}
