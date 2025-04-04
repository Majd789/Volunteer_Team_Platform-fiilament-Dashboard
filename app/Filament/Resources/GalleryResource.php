<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GalleryResource\Pages;
use App\Filament\Resources\GalleryResource\RelationManagers;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn; // لإضافة عمود النص

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'Stystem Managment';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('activity_id')
                    ->label('Activity')
                    ->options(\App\Models\Activity::all()->pluck('title', 'id'))
                    ->required(),

                Forms\Components\Select::make('media_type')
                    ->label('Media Type')
                    ->options([
                        'image' => 'Image',
                        'video' => 'Video',
                    ])
                    ->required(),

                FileUpload::make('media_url')
                    ->label('photos')
//                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('gallery_media') // حفظ الصور في storage/app/public/gallery_media
                    ->reorderable()
                    ->maxFiles(10)
                    ->afterStateUpdated(function ($state, callable $get) {
                        if (is_array($state)) {
                            foreach ($state as $image) {
                                Gallery::create([
                                    'activity_id' => $get('activity_id'), // جلب الـ activity_id الصحيح
                                    'media_type' => $get('media_type'), // جلب نوع الوسائط الصحيح
                                    'media_url' => $image, // حفظ كل صورة في سجل منفصل
                                ]);
                            }
                        }
                    }),

// FileUpload::make('media_url')
//     ->label('photos')
//     ->image()
//     ->disk('public')
//     ->directory('gallery_media') // حفظ الصور في storage/app/public/gallery_media
//     ->reorderable()
//     ->maxFiles(10)
//     ->afterStateUpdated(function ($state, callable $get) {
//         if (is_array($state)) {
//             foreach ($state as $image) {
//                 // حفظ فقط اسم الصورة في قاعدة البيانات
//                 $imageName = basename($image); // إزالة المجلد من المسار

//                 // إنشاء سجل جديد في قاعدة البيانات مع اسم الصورة فقط
//                 Gallery::create([
//                     'activity_id' => $get('activity_id'), // جلب الـ activity_id الصحيح
//                     'media_type' => $get('media_type'), // جلب نوع الوسائط الصحيح
//                     'media_url' => $imageName, // حفظ اسم الصورة فقط
//                 ]);
//             }
//         }
//     }),




            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('activity.title')
                    ->label('Activity')
                    ->searchable(),

                TextColumn::make('media_type')
                    ->label('Media Type'),

                // عرض الوسائط (إذا كانت صورة أو فيديو)
                ImageColumn::make('media_url') // تأكد من أن الحقل في الـ DB هو media_url وليس photo
                ->disk('public')
                ->getStateUsing(fn ($record) => asset('storage/gallery_media/' . $record->media_url)) // تأكد من استخدام الاسم الصحيح للحقل
                ->height(40)
                ->circular()
                ->visibility('private') // يمكن تغييره حسب الحاجة
                ->extraImgAttributes(['loading' => 'lazy']),
            

                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
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
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'view' => Pages\ViewGallery::route('/{record}'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
