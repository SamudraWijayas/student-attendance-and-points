<?php

namespace App\Services;

use InvalidArgumentException;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QRCodeService
{
    /**
     * Generate QR Code SVG directly from student NIS.
     */
    public function getSvg(string $nis, int $size = 200): string
    {
        $nis = trim($nis);

        if ($nis === '') {
            throw new InvalidArgumentException(
                'NIS siswa tidak boleh kosong untuk membuat QR Code.'
            );
        }

        return QrCode::size($size)
            ->format('svg')
            ->margin(1)
            ->errorCorrection('H')
            ->generate($nis);
    }
}
