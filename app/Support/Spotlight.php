<?php

namespace App\Support;

use App\Models\Order;
use App\Models\Product;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Blade;

class Spotlight
{
    public function search(Request $request)
    {
        return collect()
            ->merge($this->actions($request->search))
            ->merge($this->orders($request->search))
            ->merge($this->products($request->search))
            ->merge($this->users($request->search))
            ->merge($this->tasks($request->search))
            ->merge($this->projects($request->search));
    }

    // Database search
    public function orders(string $search = ''): Collection
    {
        $icon = Blade::render("<x-icon name='o-gift' class='p-2 rounded-full w-11 h-11 bg-yellow-50' />");

        return Order::query()
            ->with('user')
            ->where('id', 'like', "%$search%")
            ->take(3)
            ->get()
            ->map(function (Order $order) use ($icon) {
                return [
                    'name' => "Order #{$order->id}",
                    'description' => "{$order->user->name} / {$order->date_human} / {$order->total}",
                    'link' => "/orders/{$order->id}/edit",
                    'icon' => $icon
                ];
            });
    }

    // Database search
    public function products(string $search = ''): Collection
    {
        return Product::query()
            ->with(['category', 'brand'])
            ->where('name', 'like', "%$search%")
            ->orWhereHas('category', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->orWhereHas('brand', function ($query) use ($search) {
                $query->where('name', 'like', "%$search%");
            })
            ->take(3)
            ->get()
            ->map(function (Product $product) {
                return [
                    'name' => $product->name,
                    'description' => "{$product->category->name}, {$product->brand->name}",
                    'link' => "/products/{$product->id}/edit",
                    'avatar' => $product->cover
                ];
            });
    }

    // Database search
    public function users(string $search = ''): Collection
    {
        return User::query()
            ->where('name', 'like', "%$search%")
            ->take(3)
            ->get()
            ->map(function (User $user) {
                return [
                    'name' => $user->name,
                    'description' => 'Customer',
                    'link' => "/users/{$user->id}",
                    'avatar' => $user->avatar
                ];
            });
    }

    // Add methods for searching tasks and projects in the Spotlight class
    public function tasks(string $search = ''): Collection
    {
        return Task::query()
            ->where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->take(3)
            ->get()
            ->map(function (Task $task) {
                return [
                    'name' => $task->name,
                    'description' => $task->description,
                    'link' => "/tasks/{$task->id}/edit",
                    'icon' => Blade::render("<x-icon name='o-tasks' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
                ];
            });
    }

    public function projects(string $search = ''): Collection
    {
        return Project::query()
            ->where('name', 'like', "%$search%")
            ->orWhere('description', 'like', "%$search%")
            ->take(3)
            ->get()
            ->map(function (Project $project) {
                return [
                    'name' => $project->name,
                    'description' => $project->description,
                    'link' => "/projects/{$project->id}/edit",
                    'icon' => Blade::render("<x-icon name='o-project' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
                ];
            });
    }

    // Static search
    public function actions(string $search = ''): Collection
    {
        return collect([
            [
                'name' => 'Dashboard',
                'description' => 'Go to dashboard',
                'link' => "/",
                'icon' => Blade::render("<x-icon name='o-chart-pie' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
            ],
            [
                'name' => 'Categories',
                'description' => 'Manage categories',
                'link' => "/categories",
                'icon' => Blade::render("<x-icon name='o-hashtag' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
            ],
            [
                'name' => 'Brands',
                'description' => 'Manage brands',
                'link' => "/brands",
                'icon' => Blade::render("<x-icon name='o-tag' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
            ],
            [
                'name' => 'Projects',
                'description' => 'Manage projects',
                'link' => "/projects",
                'icon' => Blade::render("<x-icon name='o-tag' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
            ],
            [
                'name' => 'Tasks',
                'description' => 'Manage tasks',
                'link' => "/tasks",
                'icon' => Blade::render("<x-icon name='o-cube' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
            ],
            [
                'name' => 'Users',
                'description' => 'Manage users & customers',
                'link' => "/users",
                'icon' => Blade::render("<x-icon name='o-user' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
            ],
            [
                'name' => 'Orders',
                'description' => 'Manage orders',
                'link' => "/orders",
                'icon' => Blade::render("<x-icon name='o-gift' class='p-2 rounded-full w-11 h-11 bg-primary/20' />")
            ],
            [
                'name' => 'Users',
                'description' => 'Create a new user/customer',
                'link' => "/users/create",
                'icon' => Blade::render("<x-icon name='o-bolt' class='p-2 rounded-full w-11 h-11 bg-warning/20' />")
            ],
            [
                'name' => 'Order',
                'description' => 'Create a new order',
                'link' => "/orders/create",
                'icon' => Blade::render("<x-icon name='o-bolt' class='p-2 rounded-full w-11 h-11 bg-warning/20' />")
            ],
            [
                'name' => 'Product',
                'description' => 'Create a new product',
                'link' => "/products/create",
                'icon' => Blade::render("<x-icon name='o-bolt' class='p-2 rounded-full w-11 h-11 bg-warning/20' />")
            ],
        ])
            ->filter(fn(array $item) => str_contains($item['name'] . $item['description'], $search))
            ->take(3);
    }
}
