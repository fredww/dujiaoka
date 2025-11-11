<?php

namespace App\Filament\Resources;

use App\Models\Order;
use App\Models\Goods;
use App\Models\Coupon;
use App\Models\Pay;
use UnitEnum;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Filters\Filter as TableFilter;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Actions\ViewAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;

class OrderResource extends Resource
{
    /**
     * 订单管理资源
     *
     * Purpose: manage orders, provide list, filters, and detail view.
     */
    protected static ?string $model = Order::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static UnitEnum|string|null $navigationGroup = '订单与支付';

    /**
     * 表单定义（订单通常只读，这里不启用创建/编辑页面）
     *
     * Purpose: placeholder form to satisfy resource API; not used for create/edit.
     */
    public static function form(Schema $schema): Schema
    {
        // No create/edit fields for orders; keep empty.
        return $schema->schema([]);
    }

    /**
     * 列表定义
     *
     * Purpose: build index table with relations, filters, and view action.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('order_sn')->label('订单号')->searchable(),
                Tables\Columns\TextColumn::make('title')->label('标题')->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::getTypeMap()[(int) $state] ?? (string) $state),
                Tables\Columns\TextColumn::make('email')->label('邮箱')->searchable(),
                Tables\Columns\TextColumn::make('goods.gd_name')->label('商品')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('goods_price')->label('商品单价')->money('CNY', locale: 'zh_CN'),
                Tables\Columns\TextColumn::make('buy_amount')->label('购买数量')->sortable(),
                Tables\Columns\TextColumn::make('total_price')->label('总价')->money('CNY', locale: 'zh_CN'),
                Tables\Columns\TextColumn::make('coupon.coupon')->label('优惠券')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('coupon_discount_price')->label('优惠减免')->money('CNY', locale: 'zh_CN')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('wholesale_discount_price')->label('批发减免')->money('CNY', locale: 'zh_CN')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('actual_price')->label('实际支付')->money('CNY', locale: 'zh_CN'),
                Tables\Columns\TextColumn::make('pay.pay_name')->label('支付渠道')->sortable(),
                Tables\Columns\TextColumn::make('buy_ip')->label('下单IP')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('search_pwd')->label('查询密码')->copyable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('trade_no')->label('交易号')->copyable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Order::getStatusMap()[(int) $state] ?? (string) $state),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')->label('状态')->options(Order::getStatusMap()),
                SelectFilter::make('type')->label('类型')->options(Order::getTypeMap()),
                SelectFilter::make('goods_id')->label('商品')->options(Goods::query()->pluck('gd_name', 'id')->toArray()),
                SelectFilter::make('coupon_id')->label('优惠券')->options(Coupon::query()->pluck('coupon', 'id')->toArray()),
                SelectFilter::make('pay_id')->label('支付渠道')->options(Pay::query()->pluck('pay_name', 'id')->toArray()),
                TrashedFilter::make(),
                TableFilter::make('created_at')
                    ->form([
                        Forms\Components\DateTimePicker::make('start')->label('开始时间'),
                        Forms\Components\DateTimePicker::make('end')->label('结束时间'),
                    ])
                    ->query(function ($query, array $data) {
                        // Apply a between filter on created_at
                        return $query
                            ->when($data['start'] ?? null, fn ($q, $start) => $q->where('created_at', '>=', $start))
                            ->when($data['end'] ?? null, fn ($q, $end) => $q->where('created_at', '<=', $end));
                    }),
            ])
            ->recordActions([
                ViewAction::make()
                    ->url(fn ($record) => static::getUrl('view', ['record' => $record]))
                    ->openUrlInNewTab(),
                RestoreAction::make(),
                ForceDeleteAction::make(),
            ])
            ->groupedBulkActions([
                DeleteBulkAction::make(),
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * 查看页详情 Schema 定义
     *
     * Purpose: build the record detail view with Schemas to avoid blank page.
     */
    public static function infolist(Schema $schema): Schema
    {
        // Build sections with label-value texts to display order details clearly.
        return $schema->components([
            Section::make('基本信息')
                ->columns(3)
                ->components([
                    Text::make(fn (Order $record) => "ID：{$record->id}"),
                    Text::make(fn (Order $record) => "订单号：{$record->order_sn}")->copyable(),
                    Text::make(fn (Order $record) => "标题：{$record->title}"),
                    Text::make(fn (Order $record) => '类型：' . (Order::getTypeMap()[(int) $record->type] ?? (string) $record->type))
                        ->badge(),
                    Text::make(fn (Order $record) => "邮箱：{$record->email}"),
                    Text::make(fn (Order $record) => '商品：' . ($record->goods->gd_name ?? '')),
                    Text::make(fn (Order $record) => '商品单价：' . static::fmtCurrency($record->goods_price)),
                    Text::make(fn (Order $record) => "购买数量：{$record->buy_amount}"),
                    Text::make(fn (Order $record) => '总价：' . static::fmtCurrency($record->total_price)),
                ]),

            Section::make('优惠与支付')
                ->columns(3)
                ->components([
                    Text::make(fn (Order $record) => '优惠券：' . ($record->coupon->coupon ?? '')),
                    Text::make(fn (Order $record) => '优惠减免：' . static::fmtCurrency($record->coupon_discount_price)),
                    Text::make(fn (Order $record) => '批发减免：' . static::fmtCurrency($record->wholesale_discount_price)),
                    Text::make(fn (Order $record) => '实际支付：' . static::fmtCurrency($record->actual_price)),
                    Text::make(fn (Order $record) => '支付渠道：' . ($record->pay->pay_name ?? '')),
                    Text::make(fn (Order $record) => "交易号：{$record->trade_no}")->copyable(),
                ]),

            Section::make('状态与时间')
                ->columns(3)
                ->components([
                    Text::make(fn (Order $record) => '状态：' . (Order::getStatusMap()[(int) $record->status] ?? (string) $record->status))
                        ->badge(),
                    Text::make(fn (Order $record) => "下单IP：{$record->buy_ip}"),
                    Text::make(fn (Order $record) => "查询密码：{$record->search_pwd}")->copyable(),
                    Text::make(fn (Order $record) => '创建时间：' . optional($record->created_at)->format('Y-m-d H:i:s')),
                    Text::make(fn (Order $record) => '更新时间：' . optional($record->updated_at)->format('Y-m-d H:i:s')),
                ]),

            Section::make('订单信息')
                ->components([
                    Text::make(fn (Order $record) => "订单信息：\n" . (string) $record->info)
                        ->color('gray')
                        ->size('sm'),
                ]),
        ])->columns(1);
    }

    /**
     * 金额格式化工具方法
     *
     * Purpose: format numeric price to currency string like ¥12.34.
     */
    private static function fmtCurrency(null|int|float|string $value): string
    {
        return is_numeric($value) ? ('¥' . number_format((float) $value, 2)) : (string) $value;
    }

    /**
     * 页面注册
     *
     * Purpose: hook up list and view pages for orders.
     */
    public static function getPages(): array
    {
        return [
            'index' => OrderResource\Pages\ListOrders::route('/'),
            'view' => OrderResource\Pages\ViewOrder::route('/{record}'),
        ];
    }
}