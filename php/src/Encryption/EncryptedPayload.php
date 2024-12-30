<?php

namespace AP\PerformanceTest\Encryption;

readonly class EncryptedPayload
{
    public function __construct(
        public string  $encrypted_body,
        public string  $iv,
        public ?string $tag = null,
        public string  $aad = "",
    )
    {
    }

    public function serialize(): string
    {
        return $this->encrypted_body . $this->iv;
    }

    public static function deserialize(string $algo, string $serialized_payload): EncryptedPayload
    {
        $iv_len = openssl_cipher_iv_length($algo);
        return new EncryptedPayload(
            encrypted_body: substr($serialized_payload, 0, -$iv_len),
            iv: substr($serialized_payload, -$iv_len),
        );
    }
}
