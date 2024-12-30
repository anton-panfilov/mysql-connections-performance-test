<?php

namespace AP\PerformanceTest\Encryption;

use UnexpectedValueException;

readonly class Cryptographer
{
    public int $required_iv_length;

    public Algo $algo;

    public function __construct(
        Algo|string                          $algo,
        #[\SensitiveParameter] public string $passphrase,
        public int                           $options = OPENSSL_RAW_DATA,
        public int                           $tag_length = 16
    )
    {
        if (is_string($algo)) {
            $algo = Algo::from(strtolower($algo));
        }
        $this->algo = $algo;

        if (!in_array($algo->value, openssl_get_cipher_methods())) {
            throw new UnsupportedAlgorithmException(
                'The cipher algorithm "' . $algo->value .
                '" is not available. Ensure the algorithm is supported by your OpenSSL installation.'
            );
        }

        try {

        } catch (TagNotUsedException) {
        }
//        if ($this->tag_length < 4 || $this->tag_length > 16 || $this->tag_length % 2 != 0) {
//            throw new UnexpectedValueException('Invalid $tag_length, must be 4, 6, 8, 10, 12, 14 or 16');
//        }


        $this->required_iv_length = $this->algo->iv_length();
    }

    public static function iv_length(string $algo): int
    {
        $iv_len = openssl_cipher_iv_length($algo);
        if (is_int($iv_len)) {
            return $iv_len;
        }

        return 64;
    }

    public function random_iv(): string
    {
        return $this->required_iv_length == 0 ? "" : openssl_random_pseudo_bytes($this->required_iv_length);
    }

    /**
     * @param string $body The plaintext data to be encrypted.
     * @param string|null $iv The Initialization Vector (IV) for the encryption process.
     *                        Highly recommended to set this parameter to null to automatically
     *                        generate a unique IV for each encryption operation, ensuring maximum
     *                        security. Providing a custom IV is not recommended unless you have
     *                        specific requirements and fully understand the security implications.
     * @param string $aad Additional authenticated data (AAD).
     * *                    AAD is not encrypted but is included in the authentication process to ensure its integrity.
     * *                    It serves as metadata or auxiliary information tied to the ciphertext and must be stored in clear text.
     * *                    The same AAD must be provided during decryption to verify the authenticity and integrity
     * *                    of both the encrypted data and the associated AAD.
     *
     * @return EncryptedPayload Returns an instance of Result containing the encrypted data and the used IV.
     * @throws UnexpectedValueException
     */
    public function encrypt(
        #[\SensitiveParameter] string $body,
        ?string                       $iv = null,
        string                        $aad = "",
    ): EncryptedPayload
    {
        if (is_null($iv)) {
            $iv = $this->random_iv();
        }

        if (strlen($iv) != $this->required_iv_length) {
            throw new UnexpectedValueException("for algo `$this->algo` iv length must be {$this->required_iv_length}");
        }

        $tag            = "";
        $encrypted_body = openssl_encrypt(
            data: $body,
            cipher_algo: $this->algo->value,
            passphrase: $this->passphrase,
            options: $this->options,
            iv: $iv,
            tag: $tag,
            aad: $aad,
            tag_length: $this->tag_length,
        );

        if (false === $encrypted_body) {
            throw new UnexpectedValueException('Encrypting failed: ' . openssl_error_string());
        }

        return new EncryptedPayload(
            encrypted_body: $encrypted_body,
            iv: $iv,
            tag: $tag,
            aad: $aad,
        );
    }

    /**
     * @param string $encrypted_body
     * @param string $iv
     * @param string|null $tag The authentication tag generated during encryption (required for AEAD ciphers like GCM).
     * @param string $aad Additional authenticated data (AAD).
     *                     AAD isn't encrypted but is included in the authentication process to ensure its integrity.
     *                     It can be a metadata or any auxiliary information tied to the ciphertext,
     *                     but it must match the AAD used during encryption.
     * @return string
     *
     * @throws UnexpectedValueException
     */
    public function decrypt(
        string  $encrypted_body,
        string  $iv,
        ?string $tag = null,
        string  $aad = "",
    ): string
    {
        $res = openssl_decrypt(
            data: $encrypted_body,
            cipher_algo: $this->algo->value,
            passphrase: $this->passphrase,
            options: $this->options,
            iv: $iv,
            tag: $tag,
            aad: $aad,
        );

        if (false === $res) {
            throw new UnexpectedValueException('Decrypting failed');
        }

        return $res;
    }

    /**
     * @param EncryptedPayload $payload
     * @return string
     * @throws UnexpectedValueException
     */
    public function decryptPayload(EncryptedPayload $payload): string
    {
        return $this->decrypt(
            $payload->encrypted_body,
            $payload->iv,
            $payload->tag,
            $payload->aad
        );
    }
}
