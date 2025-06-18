<?php

namespace Fixit\Support;

class NotificationEmailResolver
{
    /**
     * Resolve and validate notification email recipients.
     *
     * @return array
     */
    public static function resolve(): array
    {
        $raw = config('fixit.notifications.email');
        $allowMultiple = config('fixit.notifications.allow_multiple', false);

        if (is_string($raw)) {
            $emails = array_map('trim', explode(',', $raw));
        } elseif (is_array($raw)) {
            $emails = array_map('trim', $raw);
        } else {
            $emails = [];
        }

        $emails = array_filter($emails, fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL));

        if (!$allowMultiple && count($emails) > 1) {
            logger()->warning('Fixit: Multiple emails provided but allow_multiple is disabled. Only the first will be used.');
            $emails = [reset($emails)];
        }

        return $emails;
    }
}
