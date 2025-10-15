<?php

namespace Database\Seeders;

use App\Jobs\CreateRandomFeedback;
use App\Jobs\CreateRandomOrder;
use App\Jobs\CreateRandomUser;
use App\Jobs\CreateSpecificProduct;
use App\Models\Category;
use App\Models\Feature;
use App\Models\Order;
use App\Services\Demo\DemoFeedbackCreator;
use App\Services\Demo\DemoOrderCreator;
use App\Services\Demo\DemoProductCreator;
use App\Services\Demo\DemoProductList;
use App\Services\Demo\DemoUserCreator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function __construct(
        private readonly DemoUserCreator $userCreator,
        private readonly DemoProductList $productList,
        private readonly DemoProductCreator $productCreator,
        private readonly DemoOrderCreator $orderCreator,
        private readonly DemoFeedbackCreator $feedbackCreator,
    ) {
    }

    public function run(): void
    {
        $this->command->info('Регистрируем пользователей..');
        $mainUser = $this->userCreator->createMainUser();
        $users = new Collection();

        for ($i = 0; $i < config('demo.random_users_seed_count'); ++$i) {
            $users->push($this->userCreator->createRandomUser());
        }

        $this->command->info('Ставим доп. пользователей для регистрации в очереди..');

        for ($i = 0; $i < config('demo.random_users_seed_queue_count'); ++$i) {
            CreateRandomUser::dispatch();
        }

        $this->command->info('Создаем категории...');
        $this->recursiveCreateCategory(
            include resource_path('data/demo_categories.php'),
        );

        // Подгружаем получившийся список категорий
        $categories = Category::query()->get();

        $this->command->info('Создаем динамические характеристики...');

        // Создам характеристики
        foreach (include resource_path('data/demo_features.php') as $featureData) {
            $feature = Feature::factory()
                ->withOptions($featureData['options'] ?? null)
                ->withType($featureData['type'])
                ->create([
                    'name' => $featureData['name'],
                ]);

            // Привязываем характеристику к категориям
            foreach ($featureData['categories'] as $categorySlug) {
                $category = $categories->where('full_path', $categorySlug)->first();

                if ($category) {
                    $category->features()->attach($feature->id);
                }
            }
        }

        $this->command->info('Создаем товары...');

        // Товары для основного пользователя
        for ($i = 0; $i < config('demo.products_seed_count_for_main'); ++$i) {
            $this->productCreator->create($mainUser, $this->productList->random());
        }

        // Товары для случайных пользователей
        for ($i = 0; $i < config('demo.products_seed_count'); ++$i) {
            $this->productCreator->create($users->random(), $this->productList->random());
        }

        // Ставим весь список товаров в очередь, каждого по 2 вариации
        foreach ($this->productList->raw() as $item) {
            for ($i = 0; $i < 2; ++$i) {
                CreateSpecificProduct::dispatch($this->productList->toData($item));
            }
        }

        $this->command->info('Делаем заказы...');
        $orders = new Collection();

        // Завершенные заказы
        for ($i = 0; $i < config('demo.orders_seed_count_for_main_user'); ++$i) {
            $orders->push($this->orderCreator->createAndComplete($mainUser));
        }

        // Незавершенные заказы для основного пользователя
        for ($i = 0; $i < config('demo.orders_pending_seed_count_for_main_user'); ++$i) {
            $this->orderCreator->create($mainUser);
        }

        // Завершенные заказы для случайных пользователей
        for ($i = 0; $i < config('demo.orders_seed_count_for_random_users'); ++$i) {
            $orders->push($this->orderCreator->createAndComplete($users->random()));
        }

        for ($i = 0; $i < config('demo.random_orders_count_in_queue_from_seeder'); ++$i) {
            CreateRandomOrder::dispatch();
        }

        $this->command->info('Оставляем отзывы...');

        $orders->each(function (Order $order) {
            $this->feedbackCreator->createForOrder($order);
        });

        for ($i = 0; $i < config('demo.random_orders_count_in_queue_from_seeder'); ++$i) {
            CreateRandomFeedback::dispatch();
        }
    }

    // Рекурсивное создание категорий
    private function recursiveCreateCategory(array $data, ?Category $parent = null): void
    {
        foreach ($data as $fullPath => $categoryData) {
            $category = Category::create([
                'name' => $categoryData['name'],
                'title' => $categoryData['title'] ?? $categoryData['name'],
                'slug' => $categoryData['slug'],
                'full_path' => $fullPath,
                'parent_id' => $parent?->id,
            ]);

            if (isset($categoryData['children'])) {
                $this->recursiveCreateCategory($categoryData['children'], $category);
            }
        }
    }
}
