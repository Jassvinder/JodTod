<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * "Support JodTod" content: the free-for-now note (config/site.php) plus the
 * UPI details the admin sets in Settings. Shared by the web page and the API.
 */
class SupportService
{
    public const QR_SETTING = 'support_qr_path';

    public static function details(): array
    {
        $upiId = (string) Setting::get('support_upi_id', '');
        $payeeName = (string) Setting::get('support_payee_name', config('site.app.name'));
        $qrPath = Setting::get(self::QR_SETTING);

        return [
            'title' => config('site.support.title'),
            'paragraphs' => config('site.support.paragraphs'),
            'early_users_note' => config('site.support.early_users_note'),
            'pay_steps' => config('site.support.pay_steps'),
            'upi_id' => $upiId ?: null,
            'payee_name' => $payeeName,
            'qr_url' => $qrPath && Storage::disk('public')->exists($qrPath)
                ? Storage::disk('public')->url($qrPath)
                : null,
            // The UPI ID is kept unencoded ("@" as is): some UPI apps do not decode "%40".
            'upi_link' => $upiId
                ? "upi://pay?pa={$upiId}&pn=" . rawurlencode($payeeName) . '&cu=INR'
                : null,
        ];
    }

    public static function storeQr(UploadedFile $file): void
    {
        self::removeQr();

        // A new file name on every upload so browsers and the app never show a cached old QR.
        $path = $file->storeAs('support', 'upi-qr-' . time() . '.' . $file->extension(), 'public');

        Setting::set(self::QR_SETTING, $path);
    }

    public static function removeQr(): void
    {
        $oldPath = Setting::get(self::QR_SETTING);

        if ($oldPath) {
            Storage::disk('public')->delete($oldPath);
        }

        Setting::set(self::QR_SETTING, null);
    }
}
