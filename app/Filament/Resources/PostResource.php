<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\PostResource\Widgets\PostImageWidget;
use App\Models\Post;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Stystem Managment';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {

        return $form

            ->schema([


                // user_id Done insert in Model Post With booted Function


                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('content')
                    ->required()
                    ->maxLength(65535)
                    ->columnSpanFull(),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->label('Media File')
                    ->disk('public')
                    ->directory('post-photo')


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->numeric()
                    ,

                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('content')
                    ->formatStateUsing(function ($state) {
                        return substr($state, 0, 50) . '...'; // اقتصاص النص
                    }),


                ImageColumn::make('image')
                    ->disk('public')
                    ->height(40)
                    ->circular()
                    ->visibility('private')
                    ->extraImgAttributes(['loading' => 'lazy'])
                    ->getStateUsing(fn ($record) => asset('storage/' . $record->image))
                    ->defaultImageUrl(url('/images/placeholder.png')),


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

                    ImageEntry::make('image')
                        ->label(' Post Icon')
                        ->disk('public')
                        ->visibility('private')
                        ->extraImgAttributes(['loading' => 'lazy', 'style' => 'width: 100%; height: auto;'])
                        ->getStateUsing(fn ($record) => asset('storage/' . $record->image))
                        ->columnSpan('full'),

            Section::make("Post Information")
            ->schema([
                TextEntry::make('title')
                    ->label('Title'),
                TextEntry::make('content')
                    ->label('content'),
            ]),
            Section::make("Post Author Information")
                ->schema([
                    TextEntry::make('user.name')
                        ->label('Author Name'),
                    TextEntry::make('user.email')
                        ->label('Author email'),
                ])->columns(2),
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'view' => Pages\ViewPost::route('/{record}'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
    public static function getWidgets(): array
    {
        return [
            PostImageWidget::class,
        ];
    }
}
