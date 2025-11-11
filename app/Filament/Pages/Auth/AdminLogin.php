<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Component;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Blade;
use SensitiveParameter;

class AdminLogin extends BaseLogin
{
    /**
     * 中文注释：覆盖登录页的“用户名”输入组件，改为使用 username 字段。
     * English: Override the login form input to use the username field.
     */
    protected function getEmailFormComponent(): Component
    {
        // Use username instead of email for authentication
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autocomplete('username')
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1]);
    }

    /**
     * 中文注释：覆盖获取凭据的方法，使其从表单数据中读取 username 与 password。
     * English: Build the credentials array using username/password instead of email.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function getCredentialsFromFormData(#[SensitiveParameter] array $data): array
    {
        // Map form state to EloquentUserProvider expectations
        return [
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
        ];
    }

    /**
     * 中文注释：覆盖失败异常的抛出，确保错误焦点在 username 字段。
     * English: Throw a validation error on the username field for failed auth.
     */
    protected function throwFailureValidationException(): never
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'data.username' => __('filament-panels::auth/pages/login.messages.failed'),
        ]);
    }
}