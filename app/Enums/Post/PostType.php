<?php

namespace App\Enums\Post;

use App\Admin\Support\Enum;

enum PostType: int
{
    use Enum;

    case Post = 1;       // Bài viết thông thường
    case Knowledge = 2;  // Kiến thức chăm con

    public const Default = self::Post;
}
