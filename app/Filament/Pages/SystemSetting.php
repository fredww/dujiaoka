<?php

namespace App\Filament\Pages;

use UnitEnum;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Actions;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Cache;
use Filament\Notifications\Notification;

class SystemSetting extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    /**
     * 系统配置页
     *
     * Purpose: provide admin settings with tabs and save to cache.
     */
    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-cog';

    protected static ?string $navigationLabel = '系统配置';

    protected static UnitEnum|string|null $navigationGroup = '系统配置';

    protected static ?string $title = '系统配置';

    public ?array $data = [];

    /**
     * 中文注释：挂载时填充表单，使用缓存中的系统配置。
     * English: Fill the form state from cached system settings on mount.
     */
    public function mount(): void
    {
        $defaults = Cache::get('system-setting', []);
        $this->form->fill($defaults);
    }

    /**
     * 中文注释：定义设置表单，包含基础、推送、邮件与 Geetest 四个标签页。
     * English: Define the settings form with four tabs.
     */
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('settings')
                    ->tabs([
                        Tab::make('基础设置')
                            ->schema([
                                Forms\Components\TextInput::make('title')->label('站点标题')->required(),
                                Forms\Components\FileUpload::make('img_logo')->label('图片 Logo')->image()->directory('system')->visibility('public'),
                                Forms\Components\TextInput::make('text_logo')->label('文字 Logo'),
                                Forms\Components\TextInput::make('keywords')->label('SEO 关键词'),
                                Forms\Components\Textarea::make('description')->label('站点描述')->rows(3),
                                Forms\Components\Select::make('template')->label('模板')->options(config('dujiaoka.templates', []))->required(),
                                Forms\Components\Select::make('language')->label('语言')->options(config('dujiaoka.language', []))->required(),
                                Forms\Components\TextInput::make('manage_email')->label('管理员邮箱'),
                                Forms\Components\TextInput::make('order_expire_time')->label('订单过期时间(分钟)')->numeric()->default(5)->required(),
                                Forms\Components\Toggle::make('is_open_anti_red')->label('开启防红')->default(false),
                                Forms\Components\Toggle::make('is_open_img_code')->label('开启图像验证码')->default(false),
                                Forms\Components\Toggle::make('is_open_search_pwd')->label('开启查询密码')->default(false),
                                Forms\Components\Toggle::make('is_open_google_translate')->label('开启谷歌翻译')->default(false),
                                Forms\Components\MarkdownEditor::make('notice')->label('公告')->columnSpanFull(),
                                Forms\Components\Textarea::make('footer')->label('页脚')->rows(2)->columnSpanFull(),
                            ]),
                        Tab::make('订单推送')
                            ->schema([
                                Forms\Components\Toggle::make('is_open_server_jiang')->label('开启 Server 酱推送')->default(false),
                                Forms\Components\TextInput::make('server_jiang_token')->label('Server 酱 Token'),
                                Forms\Components\Toggle::make('is_open_telegram_push')->label('开启 Telegram 推送')->default(false),
                                Forms\Components\TextInput::make('telegram_bot_token')->label('Telegram Bot Token'),
                                Forms\Components\TextInput::make('telegram_userid')->label('Telegram User ID'),
                                Forms\Components\Toggle::make('is_open_bark_push')->label('开启 Bark 推送')->default(false),
                                Forms\Components\Toggle::make('is_open_bark_push_url')->label('Bark 使用 URL 推送')->default(false),
                                Forms\Components\TextInput::make('bark_server')->label('Bark Server'),
                                Forms\Components\TextInput::make('bark_token')->label('Bark Token'),
                                Forms\Components\Toggle::make('is_open_qywxbot_push')->label('开启企业微信机器人')->default(false),
                                Forms\Components\TextInput::make('qywxbot_key')->label('企业微信机器人 Key'),
                            ]),
                        Tab::make('邮件设置')
                            ->schema([
                                Forms\Components\TextInput::make('driver')->label('驱动')->default('smtp')->required(),
                                Forms\Components\TextInput::make('host')->label('SMTP Host'),
                                Forms\Components\TextInput::make('port')->label('SMTP Port')->default(587),
                                Forms\Components\TextInput::make('username')->label('SMTP 用户名'),
                                Forms\Components\TextInput::make('password')->label('SMTP 密码')->password(),
                                Forms\Components\TextInput::make('encryption')->label('加密方式'),
                                Forms\Components\TextInput::make('from_address')->label('发件邮箱'),
                                Forms\Components\TextInput::make('from_name')->label('发件人名称'),
                            ]),
                        Tab::make('Geetest')
                            ->schema([
                                Forms\Components\TextInput::make('geetest_id')->label('Geetest ID'),
                                Forms\Components\TextInput::make('geetest_key')->label('Geetest Key'),
                                Forms\Components\Toggle::make('is_open_geetest')->label('开启 Geetest')->default(false),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    /**
     * 页面内容：将默认表单嵌入页面并添加底部保存动作。
     * English: Render the page content by embedding the 'form' schema inside a Form wrapper and attach Save actions in the footer.
     */
    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Wrap the embedded 'form' schema with a Form container for proper submission handling.
                Form::make([
                    EmbeddedSchema::make('form'),
                ])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        // Render Save button(s) below the form.
                        Actions::make($this->getFormActions())
                            ->alignment($this->getFormActionsAlignment())
                            ->fullWidth(false)
                            ->sticky($this->areFormActionsSticky())
                            ->key('form-actions'),
                    ]),
            ]);
    }

    /**
     * 中文注释：保存表单，布尔开关转换为 0/1，并写入缓存。
     * English: Save the form; cast toggles to 0/1, write to cache.
     */
    public function save(): void
    {
        $data = $this->form->getState();

        // Normalize toggle booleans to integer 0/1 for legacy compatibility
        $toggleKeys = [
            'is_open_anti_red', 'is_open_img_code', 'is_open_search_pwd', 'is_open_google_translate',
            'is_open_server_jiang', 'is_open_telegram_push', 'is_open_bark_push', 'is_open_bark_push_url',
            'is_open_qywxbot_push', 'is_open_geetest',
        ];
        foreach ($toggleKeys as $key) {
            if (array_key_exists($key, $data)) {
                $data[$key] = (int) ((bool) $data[$key]);
            }
        }

        Cache::put('system-setting', $data);

        Notification::make()
            ->title('系统配置已保存')
            ->success()
            ->send();
    }

    /**
     * 中文注释：表单动作，提供“保存设置”按钮并二次确认。
     * English: Provide a Save button with confirmation modal.
     */
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('保存设置')
                ->submit('save')
                ->requiresConfirmation()
                ->modalHeading('警告')
                ->modalDescription('修改配置后请重启 PHP worker（如使用队列）。'),
        ];
    }
}