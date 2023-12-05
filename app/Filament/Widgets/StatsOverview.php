<?php

namespace App\Filament\Widgets;

use App\Models\Branch;
use App\Models\Card;
use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Customer::count())
            ->icon('heroicon-o-arrow-trending-up')
            ->description('Total number of customers')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->chart([7, 2, 10, 3, 15, 4, 9])
            ->url(route("filament.admin.resources.customers.index"))
            ->extraAttributes([
                'class' => 'text-white text-lg cursor-pointer',
            ]),
        Stat::make('Total Transactions', Transaction::count())
            ->icon('heroicon-o-arrow-trending-up')
            ->description('Total number of transactions')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->chart([7, 2, 10, 3, 15, 4, 9])
            ->url(route("filament.admin.resources.transactions.index"))
            ->extraAttributes([
                'class' => 'text-white text-lg cursor-pointer',
            ]),
        Stat::make('Total Users', User::count())
            ->icon('heroicon-o-arrow-trending-up')
            ->description('Total number of users')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->chart([7, 2, 10, 3, 15, 4, 9])
            ->url(route("filament.admin.resources.users.index"))
            ->extraAttributes([
                'class' => 'text-white text-lg cursor-pointer',
            ]),
        Stat::make('Total Branches', Branch::count())
            ->icon('heroicon-o-arrow-trending-up')
            ->description('Total number of branches')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            ->chart([7, 2, 10, 3, 15, 4, 9])
            ->url(route("filament.admin.resources.branches.index"))
            ->extraAttributes([
                'class' => 'text-white text-lg cursor-pointer',
            ]),
        Stat::make('Total Cards', Card::count())
            ->icon('heroicon-o-arrow-trending-up')
            ->description('Total number of cards')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success')
            // ->chart([7, 2, 10, 3, 15, 4, 9])
            ->url(route("filament.admin.resources.cards.index"))
            ->extraAttributes([
                'class' => 'text-white text-lg cursor-pointer',
            ]),
        ];
    }
}
