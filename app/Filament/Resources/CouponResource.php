<?php

namespace App\Filament\Resources;

use App\Models\Coupon;
use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;

class CouponResource extends Resource
{
    /**
     * 优惠券资源
     *
     * Purpose: manage coupons (coupon, discount, status, open, ret) and their relations to goods.
     */
    protected static ?string $model = Coupon::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-ticket';

    protected static UnitEnum|string|null $navigationGroup = '营销与优惠';

    /**
     * 表单定义
     *
     * Purpose: build create/edit form for coupons.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('基础信息')
                    ->schema([
                        Forms\Components\TextInput::make('coupon')
                            ->label('优惠码')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('discount')
                            ->label('优惠金额')
                            ->numeric()
                            ->step('0.01')
                            ->default(0.00)
                            ->required(),
                        Forms\Components\Select::make('is_use')
                            ->label('使用状态')
                            ->options(Coupon::getStatusUseMap())
                            ->default(Coupon::STATUS_UNUSED)
                            ->required(),
                        Forms\Components\Toggle::make('is_open')
                            ->label('是否开启')
                            ->default(true),
                        Forms\Components\TextInput::make('ret')
                            ->label('剩余使用次数')
                            ->numeric()
                            ->default(0),
                    ]),
                Section::make('关联商品')
                    ->schema([
                        // Multi-select goods via belongsToMany; Filament will sync pivot automatically.
                        Forms\Components\Select::make('goods')
                            ->label('适用商品')
                            ->relationship('goods', 'gd_name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
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
                Tables\Columns\TextColumn::make('coupon')->label('优惠码')->searchable(),
                Tables\Columns\TextColumn::make('discount')->label('优惠金额')->money('CNY', locale: 'zh_CN'),
                Tables\Columns\TextColumn::make('ret')->label('剩余次数')->sortable(),
                Tables\Columns\TextColumn::make('is_use')
                    ->label('状态')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Coupon::getStatusUseMap()[(int) $state] ?? (string) $state),
                Tables\Columns\IconColumn::make('is_open')->label('开启')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_use')->label('状态')->options(Coupon::getStatusUseMap()),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
                EditAction::make(),
                RestoreAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * 中文注释：注册页面类
     * English: Register CRUD pages for this resource.
     */
    public static function getPages(): array
    {
        return [
            'index' => CouponResource\Pages\ListCoupons::route('/'),
            'create' => CouponResource\Pages\CreateCoupon::route('/create'),
            'view' => CouponResource\Pages\ViewCoupon::route('/{record}'),
            'edit' => CouponResource\Pages\EditCoupon::route('/{record}/edit'),
        ];
    }
}