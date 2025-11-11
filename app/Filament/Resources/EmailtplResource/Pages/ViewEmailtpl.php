<?php

namespace App\Filament\Resources\EmailtplResource\Pages;

use App\Filament\Resources\EmailtplResource;
use Filament\Resources\Pages\ViewRecord;

class ViewEmailtpl extends ViewRecord
{
    /**
     * 邮件模板查看页
     *
     * Purpose: view Emailtpl record details (read-only).
     */
    protected static string $resource = EmailtplResource::class;
}