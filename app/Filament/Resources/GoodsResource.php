<?php

namespace App\Filament\Resources;

use App\Models\Goods;
use App\Models\GoodsGroup;
use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Image;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ViewAction;

class GoodsResource extends Resource
{
    /**
     * 商品管理资源
     *
     * Purpose: manage products with rich form and relations.
     */
    protected static ?string $model = Goods::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cube';

    protected static UnitEnum|string|null $navigationGroup = '商品管理';

    /**
     * 表单定义
     *
     * Purpose: build create/edit form for goods.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('基本信息')
                    ->schema([
                        Forms\Components\TextInput::make('gd_name')->label('商品名称')->required()->maxLength(200),
                        Forms\Components\TextInput::make('gd_description')->label('简述')->maxLength(255),
                        Forms\Components\TextInput::make('gd_keywords')->label('关键词')->maxLength(255),
                        Forms\Components\Select::make('group_id')
                            ->label('商品分组')
                            ->relationship('group', 'gp_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\FileUpload::make('picture')
                            ->label('图片')
                            ->image()
                            ->directory('goods')
                            ->visibility('public'),
                        Forms\Components\Select::make('type')
                            ->label('类型')
                            ->options(Goods::getGoodsTypeMap())
                            ->required(),
                        Forms\Components\Toggle::make('is_open')->label('是否开启')->default(true),
                        Forms\Components\TextInput::make('ord')->label('排序')->numeric()->default(0),
                    ]),

                Section::make('价格与库存')
                    ->schema([
                        Forms\Components\TextInput::make('retail_price')->label('零售价格')->numeric()->step('0.01')->required(),
                        Forms\Components\TextInput::make('actual_price')->label('实际价格')->numeric()->step('0.01')->required(),
                        Forms\Components\TextInput::make('in_stock')->label('库存')->numeric()->default(0),
                        Forms\Components\TextInput::make('sales_volume')->label('销量')->numeric()->default(0),
                        Forms\Components\TextInput::make('buy_limit_num')->label('限购数量')->numeric()->default(0),
                    ]),

                Section::make('内容与配置')
                    ->schema([
                        Forms\Components\Textarea::make('buy_prompt')->label('购买提示')->rows(3),
                        Forms\Components\MarkdownEditor::make('description')->label('详细描述')->columns(1),
                        Forms\Components\Textarea::make('other_ipu_cnf')->label('代充输入框配置(JSON或文本)')->rows(4),
                        Forms\Components\Textarea::make('wholesale_price_cnf')->label('批发价配置(JSON或文本)')->rows(4),
                        Forms\Components\TextInput::make('api_hook')->label('回调Hook')->maxLength(255),
                    ]),
            ]);
    }

    /**
     * 列表定义
     *
     * Purpose: build index table with relations and filters.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\ImageColumn::make('picture')->label('图片')->square(),
                Tables\Columns\TextColumn::make('gd_name')->label('名称')->searchable(),
                Tables\Columns\TextColumn::make('gd_description')->label('简述')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('gd_keywords')->label('关键词')->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('group.gp_name')->label('分组')->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('类型')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Goods::getGoodsTypeMap()[(int) $state] ?? (string) $state),
                Tables\Columns\TextColumn::make('retail_price')->label('零售价')->money('CNY', locale: 'zh_CN'),
                Tables\Columns\TextColumn::make('actual_price')->label('实际价')->money('CNY', locale: 'zh_CN'),
                Tables\Columns\TextColumn::make('in_stock')->label('库存')->sortable(),
                Tables\Columns\TextColumn::make('sales_volume')->label('销量')->sortable(),
                Tables\Columns\TextColumn::make('ord')->label('排序')->sortable(),
                Tables\Columns\IconColumn::make('is_open')->label('开启')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')->label('类型')->options(Goods::getGoodsTypeMap()),
                SelectFilter::make('group_id')->label('分组')->options(GoodsGroup::query()->pluck('gp_name', 'id')->toArray()),
                TrashedFilter::make(),
            ])
            // 行与批量操作（统一风格，兼容软删除）
            ->recordActions([
                /**
                 * 预览（弹窗）
                 *
                 * Purpose: show key product info in a modal without leaving the list.
                 */
                ViewAction::make('preview')
                    ->label('预览（弹窗）')
                    ->modal()
                    ->modalWidth('2xl')
                    ->disabledSchema(false)
                    ->schema([
                        Section::make('基本信息')
                            ->schema([
                                Image::make(
                                    fn ($record) => $record->picture ? asset('storage/' . $record->picture) : '',
                                    '商品图片'
                                )
                                    ->imageWidth(96)
                                    ->imageHeight(96)
                                    ->hidden(fn ($record) => empty($record->picture)),
                                Text::make(fn ($record) => '名称：' . (string) $record->gd_name),
                                Text::make(fn ($record) => '分组：' . (optional($record->group)->gp_name ?? '—')),
                                Text::make(fn ($record) => '类型：' . ((Goods::getGoodsTypeMap()[(int) $record->type] ?? (string) $record->type))),
                                Text::make(fn ($record) => '是否开启：' . ($record->is_open ? '是' : '否')),
                                Text::make(fn ($record) => '排序：' . (string) $record->ord),
                            ]),
                        Section::make('价格与库存')
                            ->schema([
                                Text::make(fn ($record) => '零售价：￥' . number_format((float) $record->retail_price, 2)),
                                Text::make(fn ($record) => '实际价：￥' . number_format((float) $record->actual_price, 2)),
                                Text::make(fn ($record) => '库存：' . (string) $record->in_stock),
                                Text::make(fn ($record) => '销量：' . (string) $record->sales_volume),
                                Text::make(fn ($record) => '限购数量：' . (string) $record->buy_limit_num),
                            ]),
                        Section::make('内容摘要')
                            ->schema([
                                Text::make(fn ($record) => '简述：' . (string) ($record->gd_description ?? '')),
                                Text::make(fn ($record) => '关键词：' . (string) ($record->gd_keywords ?? '')),
                                Text::make(fn ($record) => '购买提示：' . (string) ($record->buy_prompt ?? '')),
                            ]),
                    ]),
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
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * 页面注册
     *
     * Purpose: hook up list/create/edit pages for goods.
     */
    public static function getPages(): array
    {
        return [
            'index' => GoodsResource\Pages\ListGoods::route('/'),
            'create' => GoodsResource\Pages\CreateGoods::route('/create'),
            'edit' => GoodsResource\Pages\EditGoods::route('/{record}/edit'),
            'view' => GoodsResource\Pages\ViewGoods::route('/{record}'),
        ];
    }
}