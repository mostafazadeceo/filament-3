<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

final class PermissionLabels
{
    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'iam.view' => 'مشاهده مدیریت دسترسی',
            'iam.manage' => 'مدیریت کامل دسترسی',
            'organization.view' => 'مشاهده سازمان',
            'organization.manage' => 'مدیریت سازمان',
            'tenant.view' => 'مشاهده فضای کاری',
            'tenant.manage' => 'مدیریت فضای کاری',
            'user.view' => 'مشاهده کاربران',
            'user.manage' => 'مدیریت کاربران',
            'user.invite' => 'دعوت کاربر',
            'user.suspend' => 'تعلیق کاربر',
            'user.activate' => 'فعال‌سازی کاربر',
            'user.impersonate' => 'امپرسونیشن کاربر',
            'user.reset_otp' => 'ریست OTP کاربر',
            'role.view' => 'مشاهده نقش‌ها',
            'role.manage' => 'مدیریت نقش‌ها',
            'permission.view' => 'مشاهده مجوزها',
            'permission.manage' => 'مدیریت مجوزها',
            'group.view' => 'مشاهده گروه‌ها',
            'group.manage' => 'مدیریت گروه‌ها',
            'permission_template.view' => 'مشاهده قالب‌های مجوز',
            'permission_template.manage' => 'مدیریت قالب‌های مجوز',
            'permission_override.view' => 'مشاهده بازنویسی مجوز',
            'permission_override.manage' => 'مدیریت بازنویسی مجوز',
            'access_request.view' => 'مشاهده درخواست دسترسی',
            'access_request.manage' => 'مدیریت درخواست دسترسی',
            'access_request.approve' => 'تایید درخواست دسترسی',
            'access_request.deny' => 'رد درخواست دسترسی',
            'permission_snapshot.view' => 'مشاهده اسنپ‌شات دسترسی',
            'permission_snapshot.capture' => 'ثبت اسنپ‌شات دسترسی',
            'permission_snapshot.diff' => 'مقایسه اسنپ‌شات دسترسی',
            'delegated_admin.view' => 'مشاهده ادمین تفویض‌شده',
            'delegated_admin.manage' => 'مدیریت ادمین تفویض‌شده',
            'wallet.view' => 'مشاهده کیف پول',
            'wallet.manage' => 'مدیریت کیف پول',
            'wallet.credit' => 'افزایش موجودی کیف پول',
            'wallet.debit' => 'کاهش موجودی کیف پول',
            'wallet.hold' => 'هولد موجودی کیف پول',
            'wallet.capture' => 'تسویه هولد کیف پول',
            'wallet.release' => 'آزادسازی هولد کیف پول',
            'wallet.transfer' => 'انتقال کیف پول',
            'wallet.export' => 'خروجی کیف پول',
            'wallet_transaction.view' => 'مشاهده تراکنش‌های کیف پول',
            'wallet_transaction.export' => 'خروجی تراکنش‌های کیف پول',
            'wallet_hold.view' => 'مشاهده هولدهای کیف پول',
            'wallet_hold.manage' => 'مدیریت هولدهای کیف پول',
            'subscription.view' => 'مشاهده اشتراک‌ها',
            'subscription.manage' => 'مدیریت اشتراک‌ها',
            'subscription.cancel' => 'لغو اشتراک',
            'subscription.renew' => 'تمدید اشتراک',
            'subscription_plan.view' => 'مشاهده پلن‌ها',
            'subscription_plan.manage' => 'مدیریت پلن‌ها',
            'notification.view' => 'مشاهده اعلان‌ها',
            'notification.send' => 'ارسال اعلان',
            'notification.manage' => 'مدیریت اعلان‌ها',
            'webhook.view' => 'مشاهده وبهوک‌ها',
            'webhook.manage' => 'مدیریت وبهوک‌ها',
            'api.view' => 'مشاهده API',
            'api.manage' => 'مدیریت API',
            'api.key.manage' => 'مدیریت کلید API',
            'api.docs.view' => 'مشاهده مستندات API',
            'api.docs.manage' => 'مدیریت مستندات API',
            'security.view' => 'مشاهده امنیت',
            'security.manage' => 'مدیریت امنیت',
            'audit.view' => 'مشاهده ممیزی',
            'settings.manage' => 'مدیریت تنظیمات',
        ];
    }

    public static function label(string $permissionKey): string
    {
        $labels = self::labels();
        if (isset($labels[$permissionKey])) {
            return $labels[$permissionKey];
        }

        $fallback = self::fallbackLabel($permissionKey);

        return $fallback ?: $permissionKey;
    }

    public static function labelWithKey(string $permissionKey): string
    {
        $label = self::label($permissionKey);

        if ($label === $permissionKey) {
            return $permissionKey;
        }

        return $label.' ('.$permissionKey.')';
    }

    /**
     * @param  array<int, string>  $permissions
     * @return array<string, string>
     */
    public static function optionLabels(array $permissions): array
    {
        $options = [];

        foreach ($permissions as $permissionKey) {
            $options[$permissionKey] = self::labelWithKey($permissionKey);
        }

        return $options;
    }

    protected static function fallbackLabel(string $permissionKey): ?string
    {
        $parts = explode('.', $permissionKey);
        if (count($parts) < 2) {
            return null;
        }

        $action = array_pop($parts);
        $domainKey = implode('.', $parts);

        $domainLabel = self::domainLabel($domainKey) ?? self::domainLabel($parts[0]);
        $actionLabel = self::actionLabel($action);

        if (! $domainLabel || ! $actionLabel) {
            return null;
        }

        return trim($actionLabel.' '.$domainLabel);
    }

    protected static function domainLabel(string $domainKey): ?string
    {
        return match ($domainKey) {
            'api.docs' => 'مستندات API',
            'api.key' => 'کلید API',
            'iam' => 'مدیریت دسترسی',
            'organization' => 'سازمان',
            'tenant' => 'فضای کاری',
            'user' => 'کاربر',
            'role' => 'نقش',
            'permission' => 'مجوز',
            'group' => 'گروه',
            'permission_template' => 'قالب مجوز',
            'permission_override' => 'بازنویسی مجوز',
            'access_request' => 'درخواست دسترسی',
            'permission_snapshot' => 'اسنپ‌شات دسترسی',
            'delegated_admin' => 'ادمین تفویض‌شده',
            'wallet' => 'کیف پول',
            'wallet_transaction' => 'تراکنش کیف پول',
            'wallet_hold' => 'هولد کیف پول',
            'subscription' => 'اشتراک',
            'subscription_plan' => 'پلن اشتراک',
            'notification' => 'اعلان',
            'webhook' => 'وبهوک',
            'api' => 'API',
            'security' => 'امنیت',
            'audit' => 'ممیزی',
            'settings' => 'تنظیمات',
            default => null,
        };
    }

    protected static function actionLabel(string $action): ?string
    {
        return match ($action) {
            'view' => 'مشاهده',
            'manage' => 'مدیریت',
            'invite' => 'دعوت',
            'suspend' => 'تعلیق',
            'activate' => 'فعال‌سازی',
            'impersonate' => 'امپرسونیشن',
            'reset_otp' => 'ریست OTP',
            'approve' => 'تایید',
            'deny' => 'رد',
            'capture' => 'تسویه',
            'release' => 'آزادسازی',
            'transfer' => 'انتقال',
            'export' => 'خروجی',
            'send' => 'ارسال',
            'credit' => 'افزایش موجودی',
            'debit' => 'کاهش موجودی',
            'hold' => 'هولد',
            'diff' => 'مقایسه',
            default => null,
        };
    }
}
