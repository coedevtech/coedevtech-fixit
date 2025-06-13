<?php

namespace Fixit\Support;

use RuntimeException;

class SecureEncryptor
{
    protected string $key;

    public function __construct()
    {
        $base64Key = env('FIXIT_ENCRYPTION_KEY');

        if (!$base64Key) {
            throw new RuntimeException('FIXIT_ENCRYPTION_KEY is not set.');
        }

        $this->key = base64_decode($base64Key);

        if (strlen($this->key) !== 32) {
            throw new RuntimeException('Encryption key must be 256 bits (32 bytes).');
        }
    }

    public function encrypt(mixed $input): string
    {
        if (is_string($input)) {
            $encoded = '__string__' . $input;
        } else {
            $encoded = json_encode($input);
        }

        $iv = random_bytes(16);
        $ciphertext = openssl_encrypt($encoded, 'aes-256-cbc', $this->key, 0, $iv);

        return base64_encode($iv . $ciphertext);
    }

    public function decrypt(string $encoded, bool $asArray = true): mixed
    {
        $raw = base64_decode($encoded);
        $iv = substr($raw, 0, 16);
        $ciphertext = substr($raw, 16);

        $plaintext = openssl_decrypt($ciphertext, 'aes-256-cbc', $this->key, 0, $iv);

        if ($plaintext === false) {
            throw new RuntimeException('Unable to decrypt data.');
        }

        // If we marked it as a string, return raw
        if (str_starts_with($plaintext, '__string__')) {
            return substr($plaintext, 10); // remove marker
        }

        // Otherwise try to decode JSON
        return json_decode($plaintext, $asArray);
    }
}

