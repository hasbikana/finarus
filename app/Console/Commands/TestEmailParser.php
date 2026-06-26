<?php

namespace App\Console\Commands;

use App\Parsers\BcaParser;
use App\Parsers\MandiriParser;
use App\Parsers\BniParser;
use App\Parsers\BriParser;
use App\Parsers\GopayParser;
use App\Parsers\OvoParser;
use App\Parsers\DanaParser;
use App\Services\EmailParserService;
use Illuminate\Console\Command;

class TestEmailParser extends Command
{
    protected $signature = 'test:email-parser 
                            {provider=dana : dana|gopay|ovo|bca|mandiri|bni|bri|all}
                            {--save : Simpan hasil parsing ke database}
                            {--user=1 : User ID}';

    protected $description = 'Test email parser dengan sample data';

    protected array $samples = [
        'dana' => [
            'from' => 'no-reply@dana.id',
            'subject' => 'Notifikasi Transaksi DANA',
            'body' => 'Pembayaran DANA berhasil. Anda telah melakukan pembayaran kepada GoPay sebesar Rp 50.000 pada 22 Juni 2026. Saldo DANA Anda saat ini Rp 1.250.000. Terima kasih telah menggunakan DANA.',
        ],
        'gopay' => [
            'from' => 'notification@gopay.co.id',
            'subject' => 'GoPay - Notifikasi Pembayaran',
            'body' => 'Transaksi GoPay berhasil. Pembelian di Alfamart sebesar Rp 125.500 pada 21/06/2026. Saldo GoPay Anda saat ini Rp 2.100.000.',
        ],
        'ovo' => [
            'from' => 'notification@ovo.id',
            'subject' => 'Notifikasi Transaksi OVO',
            'body' => 'OVO Notification: Pembayaran ke Tokopedia sebesar Rp 350.000 berhasil. Tanggal: 20/06/2026. Sisa saldo OVO: Rp 4.500.000.',
        ],
        'bca' => [
            'from' => 'info@bca.co.id',
            'subject' => 'BCA - Notifikasi Transaksi Debit',
            'body' => 'Transaksi debit BCA berhasil. Pembelian di Tokopedia sebesar Rp 250.000 pada 22 Juni 2026. Saldo Anda saat ini Rp 15.000.000. Terima kasih.',
        ],
        'mandiri' => [
            'from' => 'mandiri@email.mandiri.co.id',
            'subject' => 'Mandiri Notifikasi Transaksi',
            'body' => 'Transaksi debit Mandiri. Pembayaran listrik sebesar Rp 1.250.000 pada 21/06/2026. Rekening 1234567890. Saldo Rp 8.750.000.',
        ],
        'bni' => [
            'from' => 'bnicustomer@bni.co.id',
            'subject' => 'Notifikasi Transaksi BNI',
            'body' => 'BNI: Transaksi debit berhasil. Rp 500.000 ke BCA pada 20/06/2026. No. Ref: 123456. Saldo: Rp 3.200.000.',
        ],
        'bri' => [
            'from' => 'bri-info@bri.co.id',
            'subject' => 'BRI - Info Transaksi',
            'body' => 'BRI Info: Debit Rp 175.000 di Indomaret pada 22/06/2026. Rekening 0987654321. Saldo akhir: Rp 6.500.000.',
        ],
    ];

    public function handle(): int
    {
        $provider = $this->argument('provider');
        $shouldSave = $this->option('save');
        $userId = (int) $this->option('user');
        $parserService = app(EmailParserService::class);

        $providers = $provider === 'all'
            ? array_keys($this->samples)
            : [$provider];

        if (!isset($this->samples[$provider]) && $provider !== 'all') {
            $this->error("Provider '$provider' tidak dikenal. Pilih: dana, gopay, ovo, bca, mandiri, bni, bri, all");
            return 1;
        }

        foreach ($providers as $prov) {
            $this->testProvider($prov, $this->samples[$prov], $parserService, $shouldSave, $userId);
        }

        return 0;
    }

    protected function testProvider(string $name, array $sample, EmailParserService $service, bool $save, int $userId): void
    {
        $this->info(str_repeat('─', 60));
        $this->info("🔄 Testing: " . strtoupper($name));
        $this->info("   From: {$sample['from']}");
        $this->info("   Subject: {$sample['subject']}");
        $this->line("   Body: {$sample['body']}");

        $result = $service->parseEmail($sample['from'], $sample['subject'], $sample['body']);

        if (!$result) {
            $this->error("   ❌ GAGAL — parser tidak bisa membaca email ini");
            return;
        }

        $this->info('   ✅ BERHASIL');
        $this->table(
            ['Field', 'Value'],
            [
                ['Type', $result->type],
                ['Amount', 'Rp ' . number_format($result->amount, 0, ',', '.')],
                ['Description', $result->description],
                ['Date', $result->transactionDate->format('d M Y')],
                ['Message ID', $result->messageId],
                ['Provider', $result->provider ?? 'null'],
            ]
        );

        if ($save) {
            $pn = $service->saveAsPendingNotification($result, $userId, $result->messageId, $sample['body'], $sample['from']);
            if ($pn) {
                $this->info("   💾 Disimpan ke pending_notifications — ID: {$pn->id}");
            } else {
                $this->warn("   ⚠️ Tidak disimpan (mungkin duplikat)");
            }
        }
    }
}
