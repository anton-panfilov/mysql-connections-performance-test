<?php

namespace AP\PerformanceTest\Encryption;

enum Algo: string
{
    case aes_128_cbc       = 'aes-128-cbc';
    case aes_128_ccm       = 'aes-128-ccm';
    case aes_128_cfb       = 'aes-128-cfb';
    case aes_128_cfb1      = 'aes-128-cfb1';
    case aes_128_cfb8      = 'aes-128-cfb8';
    case aes_128_ctr       = 'aes-128-ctr';
    case aes_128_ecb       = 'aes-128-ecb';
    case aes_128_gcm       = 'aes-128-gcm';
    case aes_128_ocb       = 'aes-128-ocb';
    case aes_128_ofb       = 'aes-128-ofb';
    case aes_192_cbc       = 'aes-192-cbc';
    case aes_192_ccm       = 'aes-192-ccm';
    case aes_192_cfb       = 'aes-192-cfb';
    case aes_192_cfb1      = 'aes-192-cfb1';
    case aes_192_cfb8      = 'aes-192-cfb8';
    case aes_192_ctr       = 'aes-192-ctr';
    case aes_192_ecb       = 'aes-192-ecb';
    case aes_192_gcm       = 'aes-192-gcm';
    case aes_192_ocb       = 'aes-192-ocb';
    case aes_192_ofb       = 'aes-192-ofb';
    case aes_256_cbc       = 'aes-256-cbc';
    case aes_256_ccm       = 'aes-256-ccm';
    case aes_256_cfb       = 'aes-256-cfb';
    case aes_256_cfb1      = 'aes-256-cfb1';
    case aes_256_cfb8      = 'aes-256-cfb8';
    case aes_256_ctr       = 'aes-256-ctr';
    case aes_256_ecb       = 'aes-256-ecb';
    case aes_256_gcm       = 'aes-256-gcm';
    case aes_256_ocb       = 'aes-256-ocb';
    case aes_256_ofb       = 'aes-256-ofb';
    case aria_128_cbc      = 'aria-128-cbc';
    case aria_128_ccm      = 'aria-128-ccm';
    case aria_128_cfb      = 'aria-128-cfb';
    case aria_128_cfb1     = 'aria-128-cfb1';
    case aria_128_cfb8     = 'aria-128-cfb8';
    case aria_128_ctr      = 'aria-128-ctr';
    case aria_128_ecb      = 'aria-128-ecb';
    case aria_128_gcm      = 'aria-128-gcm';
    case aria_128_ofb      = 'aria-128-ofb';
    case aria_192_cbc      = 'aria-192-cbc';
    case aria_192_ccm      = 'aria-192-ccm';
    case aria_192_cfb      = 'aria-192-cfb';
    case aria_192_cfb1     = 'aria-192-cfb1';
    case aria_192_cfb8     = 'aria-192-cfb8';
    case aria_192_ctr      = 'aria-192-ctr';
    case aria_192_ecb      = 'aria-192-ecb';
    case aria_192_gcm      = 'aria-192-gcm';
    case aria_192_ofb      = 'aria-192-ofb';
    case aria_256_cbc      = 'aria-256-cbc';
    case aria_256_ccm      = 'aria-256-ccm';
    case aria_256_cfb      = 'aria-256-cfb';
    case aria_256_cfb1     = 'aria-256-cfb1';
    case aria_256_cfb8     = 'aria-256-cfb8';
    case aria_256_ctr      = 'aria-256-ctr';
    case aria_256_ecb      = 'aria-256-ecb';
    case aria_256_gcm      = 'aria-256-gcm';
    case aria_256_ofb      = 'aria-256-ofb';
    case camellia_128_cbc  = 'camellia-128-cbc';
    case camellia_128_cfb  = 'camellia-128-cfb';
    case camellia_128_cfb1 = 'camellia-128-cfb1';
    case camellia_128_cfb8 = 'camellia-128-cfb8';
    case camellia_128_ctr  = 'camellia-128-ctr';
    case camellia_128_ecb  = 'camellia-128-ecb';
    case camellia_128_ofb  = 'camellia-128-ofb';
    case camellia_192_cbc  = 'camellia-192-cbc';
    case camellia_192_cfb  = 'camellia-192-cfb';
    case camellia_192_cfb1 = 'camellia-192-cfb1';
    case camellia_192_cfb8 = 'camellia-192-cfb8';
    case camellia_192_ctr  = 'camellia-192-ctr';
    case camellia_192_ecb  = 'camellia-192-ecb';
    case camellia_192_ofb  = 'camellia-192-ofb';
    case camellia_256_cbc  = 'camellia-256-cbc';
    case camellia_256_cfb  = 'camellia-256-cfb';
    case camellia_256_cfb1 = 'camellia-256-cfb1';
    case camellia_256_cfb8 = 'camellia-256-cfb8';
    case camellia_256_ctr  = 'camellia-256-ctr';
    case camellia_256_ecb  = 'camellia-256-ecb';
    case camellia_256_ofb  = 'camellia-256-ofb';
    case chacha20          = 'chacha20';
    case chacha20_poly1305 = 'chacha20-poly1305';
    case des_ede_cbc       = 'des-ede-cbc';
    case des_ede_cfb       = 'des-ede-cfb';
    case des_ede_ecb       = 'des-ede-ecb';
    case des_ede_ofb       = 'des-ede-ofb';
    case des_ede3_cbc      = 'des-ede3-cbc';
    case des_ede3_cfb      = 'des-ede3-cfb';
    case des_ede3_cfb1     = 'des-ede3-cfb1';
    case des_ede3_cfb8     = 'des-ede3-cfb8';
    case des_ede3_ecb      = 'des-ede3-ecb';
    case des_ede3_ofb      = 'des-ede3-ofb';
    case sm4_cbc           = 'sm4-cbc';
    case sm4_cfb           = 'sm4-cfb';
    case sm4_ctr           = 'sm4-ctr';
    case sm4_ecb           = 'sm4-ecb';
    case sm4_ofb           = 'sm4-ofb';

    public function iv_length(): int
    {
        return match ($this) {
            self::aes_128_cbc, self::aes_128_cfb, self::aes_128_cfb1, self::aes_128_cfb8, self::aes_128_ctr,
            self::aes_128_ofb, self::aes_192_cbc, self::aes_192_cfb, self::aes_192_cfb1, self::aes_192_cfb8,
            self::aes_192_ctr, self::aes_192_ofb, self::aes_256_cbc, self::aes_256_cfb, self::aes_256_cfb1,
            self::aes_256_cfb8, self::aes_256_ctr, self::aes_256_ofb, self::aria_128_cbc, self::aria_128_cfb,
            self::aria_128_cfb1, self::aria_128_cfb8, self::aria_128_ctr, self::aria_128_ofb, self::aria_192_cbc,
            self::aria_192_cfb, self::aria_192_cfb1, self::aria_192_cfb8, self::aria_192_ctr, self::aria_192_ofb,
            self::aria_256_cbc, self::aria_256_cfb, self::aria_256_cfb1, self::aria_256_cfb8, self::aria_256_ctr,
            self::aria_256_ofb, self::camellia_128_cbc, self::camellia_128_cfb, self::camellia_128_cfb1,
            self::camellia_128_cfb8, self::camellia_128_ctr, self::camellia_128_ofb, self::camellia_192_cbc,
            self::camellia_192_cfb, self::camellia_192_cfb1, self::camellia_192_cfb8, self::camellia_192_ctr,
            self::camellia_192_ofb, self::camellia_256_cbc, self::camellia_256_cfb, self::camellia_256_cfb1,
            self::camellia_256_cfb8, self::camellia_256_ctr, self::camellia_256_ofb, self::sm4_cbc, self::sm4_cfb,
            self::sm4_ctr, self::sm4_ofb, self::chacha20 => 16,

            self::aes_128_ccm, self::aes_128_gcm, self::aes_128_ocb, self::aes_192_ccm, self::aes_192_gcm,
            self::aes_192_ocb, self::aes_256_ccm, self::aes_256_gcm, self::aes_256_ocb, self::aria_128_ccm,
            self::aria_128_gcm, self::aria_192_ccm, self::aria_192_gcm, self::aria_256_ccm, self::aria_256_gcm,
            self::chacha20_poly1305 => 12,

            self::aes_128_ecb, self::aes_192_ecb, self::aes_256_ecb, self::aria_128_ecb, self::aria_192_ecb,
            self::aria_256_ecb, self::camellia_128_ecb, self::camellia_192_ecb, self::camellia_256_ecb,
            self::des_ede_ecb, self::des_ede3_ecb, self::sm4_ecb => 0,

            self::des_ede_cbc, self::des_ede_cfb, self::des_ede_ofb, self::des_ede3_cbc, self::des_ede3_cfb,
            self::des_ede3_cfb1, self::des_ede3_cfb8, self::des_ede3_ofb => 8,
        };
    }

    public function getAllowedTagLength(): array
    {
        return match ($this) {
            self::aes_128_ccm, self::aes_192_ccm, self::aes_256_ccm,
            self::aria_128_ccm, self::aria_192_ccm, self::aria_256_ccm
            => [4, 6, 8, 10, 12, 14, 16],

            self::aes_128_gcm, self::aes_192_gcm, self::aes_256_gcm, self::aria_128_gcm, self::aria_192_gcm,
            self::aria_256_gcm, self::aes_128_ocb, self::aes_192_ocb, self::aes_256_ocb, self::chacha20_poly1305
            => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16],

            default => throw new TagNotUsedException(),
        };
    }

    public function isAllowedTagLength(int $tagLength): bool
    {
        return match ($this) {
            self::aes_128_ccm, self::aes_192_ccm, self::aes_256_ccm,
            self::aria_128_ccm, self::aria_192_ccm, self::aria_256_ccm
            => $tagLength >= 4 && $tagLength <= 16 && $tagLength % 2 === 0,

            self::aes_128_gcm, self::aes_192_gcm, self::aes_256_gcm, self::aria_128_gcm, self::aria_192_gcm,
            self::aria_256_gcm, self::aes_128_ocb, self::aes_192_ocb, self::aes_256_ocb, self::chacha20_poly1305
            => $tagLength >= 1 && $tagLength <= 16,

            default => throw new TagNotUsedException(),
        };
    }
}