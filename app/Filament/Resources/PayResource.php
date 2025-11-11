<?php

namespace App\Filament\Resources;

use App\Models\Pay;
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

class PayResource extends Resource
{
    /**
     * 支付渠道资源
     *
     * Purpose: manage payment gateways with create/edit and read-only view.
     */
    protected static ?string $model = Pay::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-credit-card';

    protected static UnitEnum|string|null $navigationGroup = '订单与支付';

    /**
     * 表单定义
     *
     * Purpose: build create/edit form for payment gateways.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('基础信息')
                    ->schema([
                        Forms\Components\TextInput::make('pay_name')->label('渠道名称')->required(),
                        Forms\Components\TextInput::make('pay_check')->label('支付校验')->required(),
                        Forms\Components\Select::make('pay_method')
                            ->label('支付方式')
                            ->options(Pay::getMethodMap())
                            ->required(),
                        Forms\Components\Select::make('pay_client')
                            ->label('客户端')
                            ->options(Pay::getClientMap())
                            ->required(),
                        Forms\Components\TextInput::make('pay_handleroute')->label('处理路由')->required(),
                        Forms\Components\Toggle::make('is_open')->label('是否开启')->default(true),
                    ]),
                Section::make('商户配置')
                    ->schema([
                        Forms\Components\TextInput::make('merchant_id')->label('商户ID')->maxLength(255),
                        Forms\Components\Textarea::make('merchant_key')->label('商户密钥')->rows(3),
                        Forms\Components\Textarea::make('merchant_pem')->label('商户证书')->rows(3),
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
                Tables\Columns\TextColumn::make('pay_name')->label('渠道名称')->searchable(),
                Tables\Columns\TextColumn::make('pay_check')->label('支付校验')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pay_method')
                    ->label('方式')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Pay::getMethodMap()[(int) $state] ?? (string) $state),
                Tables\Columns\TextColumn::make('merchant_id')->label('商户ID')->limit(20)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('merchant_key')->label('商户密钥')->limit(20)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('merchant_pem')->label('商户证书')->limit(20)->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('pay_client')
                    ->label('客户端')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Pay::getClientMap()[(int) $state] ?? (string) $state),
                Tables\Columns\TextColumn::make('pay_handleroute')->label('处理路由')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\IconColumn::make('is_open')->label('开启')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('pay_method')->label('方式')->options(Pay::getMethodMap()),
                SelectFilter::make('pay_client')->label('客户端')->options(Pay::getClientMap()),
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
            ->defaultSort('id');
    }

    /**
     * 页面注册
     *
     * Purpose: hook up list/create/edit/view pages for pay gateways.
     */
    public static function getPages(): array
    {
        return [
            'index' => PayResource\Pages\ListPays::route('/'),
            'create' => PayResource\Pages\CreatePay::route('/create'),
            'edit' => PayResource\Pages\EditPay::route('/{record}/edit'),
            'view' => PayResource\Pages\ViewPay::route('/{record}'),
        ];
    }
}