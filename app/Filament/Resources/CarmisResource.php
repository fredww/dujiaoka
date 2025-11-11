<?php

namespace App\Filament\Resources;

use App\Models\Carmis;
use App\Models\Goods;
use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\Action as PageAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;

class CarmisResource extends Resource
{
    /**
     * 卡密管理资源
     *
     * Purpose: manage carmis (redeemable codes) with import capability.
     */
    protected static ?string $model = Carmis::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-key';

    protected static UnitEnum|string|null $navigationGroup = '商品管理';

    /**
     * 表单定义
     *
     * Purpose: build create/edit form for carmis.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('卡密信息')
                    ->schema([
                        Forms\Components\Select::make('goods_id')
                            ->label('商品')
                            ->relationship('goods', 'gd_name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\Radio::make('status')
                            ->label('状态')
                            ->options(Carmis::getStatusMap())
                            ->default(Carmis::STATUS_UNSOLD)
                            ->required(),
                        Forms\Components\Toggle::make('is_loop')
                            ->label('循环使用')
                            ->default(false),
                        Forms\Components\Textarea::make('carmi')
                            ->label('卡密内容')
                            ->rows(6)
                            ->required(),
                    ]),
            ]);
    }

    /**
     * 列表定义
     *
     * Purpose: build index table with filters and import header action.
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('ID')->sortable(),
                Tables\Columns\TextColumn::make('goods.gd_name')->label('商品')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('状态')
                    ->badge()
                    ->formatStateUsing(fn ($state) => Carmis::getStatusMap()[(int) $state] ?? (string) $state),
                Tables\Columns\IconColumn::make('is_loop')->label('循环')->boolean(),
                Tables\Columns\TextColumn::make('carmi')->label('卡密')->limit(30)->copyable(),
                Tables\Columns\TextColumn::make('created_at')->label('创建时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('更新时间')->dateTime()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->headerActions([
                /**
                 * 导入卡密动作
                 *
                 * Purpose: import multiple carmis lines for a selected goods.
                 */
                PageAction::make('importCarmis')
                    ->label('导入卡密')
                    ->form([
                        Forms\Components\Select::make('goods_id')
                            ->label('商品')
                            ->options(Goods::query()->pluck('gd_name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                        Forms\Components\Toggle::make('is_loop')
                            ->label('循环使用')
                            ->default(false),
                        Forms\Components\Textarea::make('carmis')
                            ->label('卡密内容（每行一个）')
                            ->rows(10)
                            ->placeholder("one\nTwo\nThree")
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        // Parse lines and trim
                        $lines = collect(preg_split('/\r\n|\r|\n/', (string)($data['carmis'] ?? '')))
                            ->map(fn ($l) => trim((string)$l))
                            ->filter(fn ($l) => $l !== '');

                        $count = 0;
                        foreach ($lines as $line) {
                            Carmis::query()->create([
                                'goods_id' => (int) $data['goods_id'],
                                'status' => Carmis::STATUS_UNSOLD,
                                'is_loop' => (bool) ($data['is_loop'] ?? false),
                                'carmi' => $line,
                            ]);
                            $count++;
                        }

                        // Notify success using Filament Notifications
                        Notification::make()
                            ->title("成功导入 {$count} 条卡密")
                            ->success()
                            ->send();
                    }),
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
                RestoreBulkAction::make(),
                ForceDeleteBulkAction::make(),
            ])
            ->defaultSort('id', 'desc');
    }

    /**
     * 页面注册
     *
     * Purpose: hook up list/create/edit pages for carmis.
     */
    public static function getPages(): array
    {
        return [
            'index' => CarmisResource\Pages\ListCarmis::route('/'),
            'create' => CarmisResource\Pages\CreateCarmis::route('/create'),
            'edit' => CarmisResource\Pages\EditCarmis::route('/{record}/edit'),
            'view' => CarmisResource\Pages\ViewCarmis::route('/{record}'),
        ];
    }
}