<?php

namespace App\Filament\Resources\EmailtplResource\Pages;

use App\Filament\Resources\EmailtplResource;
use Filament\Resources\Pages\ListRecords;

class ListEmailtpls extends ListRecords
{
    /**
     * 邮件模板列表页
     *
     * Purpose: render Emailtpl index with table and actions.
     */
    protected static string $resource = EmailtplResource::class;
}