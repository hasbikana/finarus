<?php

namespace Database\Factories;

use App\Models\Account;
use Illuminate\Database\Eloquent\Factories\Factory;

class AccountFactory extends Factory
{
    protected $model = Account::class;

    public function definition(): array
    {
        $accountTypes = [
            ['name' => 'Dompet Digital', 'provider' => 'GoPay', 'type' => 'ewallet', 'logo' => 'gopay'],
            ['name' => 'E-Wallet OVO', 'provider' => 'OVO', 'type' => 'ewallet', 'logo' => 'ovo'],
            ['name' => 'DANA', 'provider' => 'DANA', 'type' => 'ewallet', 'logo' => 'dana'],
            ['name' => 'LinkAja', 'provider' => 'LinkAja', 'type' => 'ewallet', 'logo' => 'linkaja'],
            ['name' => 'Rekening Utama', 'provider' => 'Bank BCA', 'type' => 'bank', 'logo' => 'bca'],
            ['name' => 'Tabungan', 'provider' => 'Bank BNI', 'type' => 'bank', 'logo' => 'bni'],
            ['name' => 'Rekening Giro', 'provider' => 'Bank Mandiri', 'type' => 'bank', 'logo' => 'mandiri'],
            ['name' => 'Tabungan BRI', 'provider' => 'Bank BRI', 'type' => 'bank', 'logo' => 'bri'],
            ['name' => 'Kartu Kredit', 'provider' => 'BCA Card', 'type' => 'credit_card', 'logo' => 'bca'],
        ];

        $account = fake()->randomElement($accountTypes);

        return [
            'name' => $account['name'],
            'provider' => $account['provider'],
            'type' => $account['type'],
            'account_number' => fake()->numerify('################'),
            'balance' => fake()->randomFloat(2, 100000, 50000000),
            'logo' => $account['logo'],
        ];
    }
}