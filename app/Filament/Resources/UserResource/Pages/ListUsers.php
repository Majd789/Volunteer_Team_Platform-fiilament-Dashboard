<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Widgets\CustomInfoWidget;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    // ✅ هذه هي الطريقة الصحيحة لعرض الودجت
    protected function getHeaderWidgets(): array
    {
        return [
            CustomInfoWidget::class,
        ];
    }
}
