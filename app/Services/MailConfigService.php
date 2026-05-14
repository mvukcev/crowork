<?php

namespace App\Services;

use App\Models\Setting;

class MailConfigService
{
    /**
     * Get mail configuration from settings or environment
     * Returns configuration array for Mail facade config
     */
    public static function getConfig(): array
    {
        return [
            'driver' => self::getDriver(),
            'host' => Setting::getString('mail_host') ?? env('MAIL_HOST'),
            'port' => Setting::getInt('mail_port', 587),
            'from' => [
                'address' => Setting::getString('mail_from_address') ?? env('MAIL_FROM_ADDRESS', 'noreply@crowork.local'),
                'name' => Setting::getString('mail_from_name') ?? env('MAIL_FROM_NAME', 'CroWork'),
            ],
            'encryption' => self::getEncryption(),
            'username' => Setting::getString('mail_username') ?? env('MAIL_USERNAME'),
            'password' => self::getPassword(),
        ];
    }

    private static function getDriver(): string
    {
        $driver = Setting::getString('mail_mailer');
        
        // If not configured in settings, use env with 'log' as fallback for dev
        if (! $driver) {
            return env('MAIL_MAILER', 'log');
        }

        return $driver;
    }

    private static function getEncryption(): ?string
    {
        $encryption = Setting::getString('mail_encryption');
        
        if (! $encryption || $encryption === 'null') {
            return null;
        }

        return $encryption;
    }

    private static function getPassword(): ?string
    {
        $password = Setting::getValue('mail_password');
        
        // If password is empty/null in settings, try environment
        if (! $password) {
            return env('MAIL_PASSWORD');
        }

        return (string) $password;
    }

    /**
     * Validate SMTP connection
     */
    public static function validateConnection(): bool
    {
        try {
            $config = self::getConfig();
            
            // Only validate if SMTP is configured
            if ($config['driver'] !== 'smtp' || ! $config['host']) {
                return true;
            }

            $socket = @fsockopen(
                $config['host'],
                $config['port'],
                $errno,
                $errstr,
                5
            );

            if (! $socket) {
                return false;
            }

            fclose($socket);
            return true;
        } catch (\Exception) {
            return false;
        }
    }
}
