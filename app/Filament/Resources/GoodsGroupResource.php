<?php

namespace App\Filament\Resources;

use App\Models\GoodsGroup;
use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;

class GoodsGroupResource extends Resource
{
    /**
     * 商品分组资源
     *
     * Purpose: manage product groups (gp_name, is_open, ord) in Filament.
     */
    protected static ?string $model = GoodsGroup::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static UnitEnum|string|null $navigationGroup = '商品管理';

    /**
     * 表单定义
     *
     * Purpose: build create/edit form for goods groups.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('分组信息')
                    ->schema([
                        Forms\Components\TextInput::make('gp_name')
                            ->label('分组名称')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\Toggle::make('is_open')
                            ->label('是否开启')
                            ->default(true),
                        Forms\Components\TextInput::make('ord')
                            ->label('排序')
                            ->numeric()
                            ->default(0),
                    ]),
            ]);
    }

    /**
     * 列表定义
     *
     * Purpose: build index table with filters and actions.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('gp_name')->label('分组名称')->searchable(),
                Tables\Columns\IconColumn::make('is_open')
                    ->label('开启')
                    ->boolean(),
                Tables\Columns\TextColumn::make('ord')->label('排序')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
                ForceDeleteBulkAction::make(),
                RestoreBulkAction::make(),
            ])
            ->defaultSort('ord');
    }

    /**
     * 页面注册
     *
     * Purpose: hook up list/create/edit pages for the resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => GoodsGroupResource\Pages\ListGoodsGroups::route('/'),
            'create' => GoodsGroupResource\Pages\CreateGoodsGroup::route('/create'),
            'edit' => GoodsGroupResource\Pages\EditGoodsGroup::route('/{record}/edit'),
            'view' => GoodsGroupResource\Pages\ViewGoodsGroup::route('/{record}'),
        ];
    }
}