<?php namespace App\Cleaners;

trait Sanitizer {
    public function sanitize(string $dirtyString, bool $stripTags = false): string
    {
        if(!$stripTags){
            return htmlspecialchars(strip_tags($dirtyString), ENT_QUOTES, 'UTF-8');
        } else {
            return htmlspecialchars($dirtyString, ENT_QUOTES, 'UTF-8');
        }
    }
}