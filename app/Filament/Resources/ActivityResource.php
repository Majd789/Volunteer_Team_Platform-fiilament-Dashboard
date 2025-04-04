<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages;
use App\Filament\Resources\ActivityResource\RelationManagers;
use App\Models\Activity;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\FileUpload; // تعديل هنا لاستخدام FileUpload
use Filament\Tables\Columns\ImageColumn;

class ActivityResource extends Resource
{
    protected static ?string $model = Activity::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string $navigationGroup = 'Stystem Managment';
    protected static ?int $navigationSort = 1;


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

                FileUpload::make('photo')  // مكون تحميل الصورة
                    ->image()  // التأكد من تحديد أن الملف هو صورة
                    ->disk('public')  // تحديد التخزين المحلي في `storage/app/public`
                    ->directory('activity_photos')  // تحديد المجلد الذي سيتم تخزين الصور فيه
                    ->required()  
                    ->maxSize(1024)  
                    ->columnSpanFull(), 
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

                Tables\Columns\TextColumn::make('description')
                    ->formatStateUsing(function ($state) {
                        return substr($state, 0, 50) . '...'; // اقتصاص النص
                    }),

                Tables\Columns\TextColumn::make('NO Team Member')
                    ->counts('registrations')
                ->label('NO Team Member'),

                ImageColumn::make('photo')
                    ->disk('public')
                    ->getStateUsing(fn ($record) => asset('storage/' . $record->photo))// تأكد من أن المسار صحيح
                    ->height(40)
                    ->circular()
                    ->visibility('private')
                    ->extraImgAttributes(['loading' => 'lazy']),


                Tables\Columns\TextColumn::make('start_date')
                    ->date()
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
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                ImageEntry::make('photo')
                    ->label('Activity Icon')
                    ->disk('public')
                    ->visibility('private')
                    ->extraImgAttributes(['loading' => 'lazy', 'style' => 'width: 100%; height: auto;'])
                    ->getStateUsing(fn ($record) => asset('storage/' . $record->photo))
                    ->columnSpan('full'),

                Section::make("Activity Public Information")
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('description')
                            ->label('Description'),

                    ]) ,
                Section::make("Activity Date Information")
                    ->schema([
                        TextEntry::make('start_date')
                            ->label('Start Date '),
                        TextEntry::make('end_date')
                            ->label('End Date'),
                    ])->columns(2) ,

                    Section::make("Activity Gallery")
                    ->schema([
                        // عرض الصور باستخدام ImageEntry مع رابط الصورة
                        \Filament\Infolists\Components\Grid::make()
                            ->columns(3) // تحديد عدد الأعمدة التي تظهر فيها الصور
                            ->schema(fn ($record) => $record->gallery->map(fn ($image) => ImageEntry::make('image')
                                ->getStateUsing(fn () => asset('storage/gallery_media/' . $image->media_url))  // المسار الصحيح للصور
                                ->extraImgAttributes(['style' => 'width: 100%; height: auto;'])  // تخصيص أسلوب الصورة
                                ->columnSpan('full')
                            )->toArray()), // تحويل الـ Collection إلى مصفوفة
                    ])
                    ->columns(2),
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
