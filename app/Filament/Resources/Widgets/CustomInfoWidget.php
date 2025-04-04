<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User; 
use App\Models\Activity; 
use App\Models\Post; 

class CustomInfoWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('User Count ', User::count())
                ->description('Total User Counts ')
                ->descriptionIcon('heroicon-m-user-group'),

            Stat::make('Activites Count', Activity::count())  
                ->description('Total Activiteis Count ')
                ->descriptionIcon('heroicon-m-clipboard'),

            Stat::make('Post Count ', Post::count())  
                ->description('Total Posts Count ')
                ->descriptionIcon('heroicon-m-pencil'),
        ];
    }
}
