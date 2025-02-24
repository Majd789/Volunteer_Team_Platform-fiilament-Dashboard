<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload; // تعديل هنا لاستخدام FileUpload
use Filament\Tables\Columns\ImageColumn;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
               
                // استخدام FileUpload بدلاً من ImageUpload
                FileUpload::make('photo')  // مكون تحميل الصورة
                    ->image()  // التأكد من تحديد أن الملف هو صورة
                    ->disk('public')  // تحديد التخزين المحلي في `storage/app/public`
                    ->directory('activity_photos')  // تحديد المجلد الذي سيتم تخزين الصور فيه
                    ->required()  // إذا كنت ترغب في جعل تحميل الصورة إلزاميًا
                    ->maxSize(1024)  // تعيين الحد الأقصى لحجم الصورة (بالبايت)
                    ->columnSpanFull(),  // جعل المكون يأخذ كامل العرض في النموذج
                Forms\Components\DatePicker::make('start_date')
                    ->required(),
                Forms\Components\DatePicker::make('end_date')
                    ->required(),
                Forms\Components\TextInput::make('location')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(function ($state) {
                        return substr($state, 0, 50) . '...'; // اقتصاص النص
                    }),
                // إضافة عمود عرض الصورة هنا
                ImageColumn::make('photo')  // إضافة عمود لعرض الصورة
                    ->disk('public')  // تحديد مكان تخزين الصور (public)
                    ->width(100)  // تحديد عرض الصورة (اختياري)
                    ->height(100)  // تحديد ارتفاع الصورة (اختياري)
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivities::route('/'),
            'create' => Pages\CreateActivity::route('/create'),
            'view' => Pages\ViewActivity::route('/{record}'),
            'edit' => Pages\EditActivity::route('/{record}/edit'),
        ];
    }
}
