<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Budget;
use App\Models\Category;
use App\Models\SavingGoal;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'demo@finarus.com'],
            ['name' => 'Demo User', 'password' => bcrypt('password'), 'email_verified_at' => now()]
        );

        UserSetting::firstOrCreate(['user_id' => $user->id]);

        if ($user->categories()->exists()) {
            return;
        }

        $categories = collect([
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Makanan', 'type' => 'expense', 'icon' => '🍔', 'color' => 'orange']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Transportasi', 'type' => 'expense', 'icon' => '🚗', 'color' => 'blue']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Belanja', 'type' => 'expense', 'icon' => '🛍️', 'color' => 'pink']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Hiburan', 'type' => 'expense', 'icon' => '🎬', 'color' => 'purple']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Kesehatan', 'type' => 'expense', 'icon' => '⚕️', 'color' => 'red']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Pendidikan', 'type' => 'expense', 'icon' => '📚', 'color' => 'green']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Utilitas', 'type' => 'expense', 'icon' => '⚡', 'color' => 'yellow']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Gaji', 'type' => 'income', 'icon' => '💰', 'color' => 'green']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Freelance', 'type' => 'income', 'icon' => '💵', 'color' => 'blue']),
            Category::factory()->create(['user_id' => $user->id, 'name' => 'Investasi', 'type' => 'income', 'icon' => '📈', 'color' => 'purple']),
        ]);

        $accounts = collect([
            Account::create(['user_id' => $user->id, 'name' => 'Rekening Utama', 'provider' => 'Bank BCA', 'type' => 'bank', 'account_number' => '1234567890', 'balance' => 25000000, 'logo' => 'bca']),
            Account::create(['user_id' => $user->id, 'name' => 'Dompet Digital', 'provider' => 'GoPay', 'type' => 'ewallet', 'account_number' => '087812345678', 'balance' => 5000000, 'logo' => 'gopay']),
            Account::create(['user_id' => $user->id, 'name' => 'Kartu Kredit', 'provider' => 'BCA Card', 'type' => 'credit_card', 'account_number' => '4532****1234', 'balance' => 8500000, 'logo' => 'bca']),
        ]);

        $expenseCats = $categories->where('type', 'expense');
        $incomeCats = $categories->where('type', 'income');
        $now = now();

        for ($i = 0; $i < 30; $i++) {
            $daysAgo = rand(0, 60);
            $date = $now->copy()->subDays($daysAgo);

            if (rand(0, 1)) {
                $cat = $expenseCats->random();
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $cat->id,
                    'account_id' => $accounts->random()->id,
                    'type' => 'expense',
                    'amount' => rand(10, 500) * 1000,
                    'description' => fake()->randomElement(['Kopi Starbucks', 'Belanja Online', 'Tagihan Listrik', 'Makan di Restoran', 'Bensin', 'Pulsa', 'Obat', 'Buku']),
                    'transaction_date' => $date->format('Y-m-d'),
                ]);
            } else {
                $cat = $incomeCats->random();
                Transaction::create([
                    'user_id' => $user->id,
                    'category_id' => $cat->id,
                    'account_id' => $accounts->random()->id,
                    'type' => 'income',
                    'amount' => rand(50, 5000) * 1000,
                    'description' => fake()->randomElement(['Gaji Bulanan', 'Proyek Freelance', 'Dividen Investasi', 'Bonus']),
                    'transaction_date' => $date->format('Y-m-d'),
                ]);
            }
        }

        foreach ($accounts as $account) {
            $account->recalculateBalance();
        }

        $budgetMonth = $now->month;
        $budgetYear = $now->year;

        foreach ($expenseCats as $cat) {
            Budget::create([
                'user_id' => $user->id,
                'category_id' => $cat->id,
                'amount' => rand(5, 50) * 100000,
                'month' => $budgetMonth,
                'year' => $budgetYear,
            ]);
        }

        SavingGoal::create(['user_id' => $user->id, 'name' => 'Liburan ke Bali', 'target_amount' => 10000000, 'current_amount' => 6400000, 'deadline' => '2024-06-30', 'icon' => '✈️']);
        SavingGoal::create(['user_id' => $user->id, 'name' => 'Beli Laptop Baru', 'target_amount' => 15000000, 'current_amount' => 11250000, 'deadline' => '2024-09-30', 'icon' => '💻']);
        SavingGoal::create(['user_id' => $user->id, 'name' => 'Dana Darurat', 'target_amount' => 20000000, 'current_amount' => 15600000, 'deadline' => '2024-12-31', 'icon' => '🛡️']);
    }
}