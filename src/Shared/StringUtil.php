<?php
declare(strict_types=1);

namespace Shared;

final class StringUtil
{
    public static function escapeHtml(string $string): string
    {
        return htmlentities($string, ENT_QUOTES);
    }
}
