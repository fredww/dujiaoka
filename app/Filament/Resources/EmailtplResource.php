<?php

namespace App\Filament\Resources;

use App\Models\Emailtpl;
use UnitEnum;
use BackedEnum;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ForceDeleteBulkAction;

class EmailtplResource extends Resource
{
    /**
     * 邮件模板资源
     *
     * Purpose: manage email templates (name, token, content) used by system notifications.
     */
    protected static ?string $model = Emailtpl::class;

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-envelope';

    protected static UnitEnum|string|null $navigationGroup = '系统配置';

    /**
     * 表单定义
     *
     * Purpose: build create/edit form for email templates.
     */
    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('模板信息')
                    ->schema([
                        Forms\Components\TextInput::make('tpl_name')
                            ->label('模板名称')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\TextInput::make('tpl_token')
                            ->label('模板标识')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\MarkdownEditor::make('tpl_content')
                            ->label('模板内容')
                            ->columnSpanFull()
                            ->required(),
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
                Tables\Columns\TextColumn::make('tpl_name')->label('模板名称')->searchable(),
                Tables\Columns\TextColumn::make('tpl_token')->label('标识')->copyable()->searchable(),
                Tables\Columns\TextColumn::make('tpl_content')->label('内容')->limit(80)->toggleable(isToggledHiddenByDefault: true),
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
            'index' => EmailtplResource\Pages\ListEmailtpls::route('/'),
            'create' => EmailtplResource\Pages\CreateEmailtpl::route('/create'),
            'view' => EmailtplResource\Pages\ViewEmailtpl::route('/{record}'),
            'edit' => EmailtplResource\Pages\EditEmailtpl::route('/{record}/edit'),
        ];
    }
}