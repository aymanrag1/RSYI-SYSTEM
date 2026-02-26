<?php

namespace App\Services;

use App\Models\WpUser;

/**
 * WordPress Password Authentication Service
 * يتحقق من كلمة المرور باستخدام خوارزمية WordPress phpass.
 */
class WpAuthService
{
    /**
     * التحقق من بيانات تسجيل الدخول.
     * يدعم كلمات المرور المشفّرة بـ phpass (WordPress) وبـ bcrypt.
     */
    public static function attempt(string $login, string $password): WpUser|false
    {
        // البحث بالـ username أو الإيميل
        $user = WpUser::where('user_login', $login)
            ->orWhere('user_email', $login)
            ->first();

        if (! $user) {
            return false;
        }

        if (self::checkPassword($password, $user->user_pass)) {
            return $user;
        }

        return false;
    }

    /**
     * التحقق من كلمة المرور مع دعم phpass و bcrypt و MD5.
     */
    public static function checkPassword(string $password, string $hash): bool
    {
        // bcrypt (Laravel / WordPress الحديث)
        if (str_starts_with($hash, '$2y$') || str_starts_with($hash, '$2a$')) {
            return password_verify($password, $hash);
        }

        // phpass (WordPress الكلاسيكي $P$ أو $H$)
        if (str_starts_with($hash, '$P$') || str_starts_with($hash, '$H$')) {
            return self::checkPhpass($password, $hash);
        }

        // MD5 قديم
        return md5($password) === $hash;
    }

    /**
     * تحقق phpass — تطبيق مبسّط متوافق مع WordPress.
     */
    private static function checkPhpass(string $password, string $hash): bool
    {
        $itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        if (strlen($hash) !== 34) {
            return false;
        }

        $count_log2 = strpos($itoa64, $hash[3]);
        if ($count_log2 < 7 || $count_log2 > 30) {
            return false;
        }

        $count = 1 << $count_log2;
        $salt  = substr($hash, 4, 8);

        if (strlen($salt) !== 8) {
            return false;
        }

        $checkHash = md5($salt . $password, true);
        do {
            $checkHash = md5($checkHash . $password, true);
        } while (--$count);

        $output = self::encode64($checkHash, 16, $itoa64);

        return substr($hash, 12) === $output;
    }

    private static function encode64(string $input, int $count, string $itoa64): string
    {
        $output = '';
        $i      = 0;

        do {
            $value   = ord($input[$i++]);
            $output .= $itoa64[$value & 0x3f];
            if ($i < $count) {
                $value |= ord($input[$i]) << 8;
            }
            $output .= $itoa64[($value >> 6) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            if ($i < $count) {
                $value |= ord($input[$i]) << 16;
            }
            $output .= $itoa64[($value >> 12) & 0x3f];
            if ($i++ >= $count) {
                break;
            }
            $output .= $itoa64[($value >> 18) & 0x3f];
        } while ($i < $count);

        return $output;
    }
}
