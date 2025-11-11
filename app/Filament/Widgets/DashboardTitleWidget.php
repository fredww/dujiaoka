<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class DashboardTitleWidget extends Widget
{
    /**
     * 仪表盘标题组件
     *
     * Purpose: Display project logo, version, and helpful links
     * on the Filament dashboard, replicating the legacy dashboard title.
     */
    protected string $view = 'filament.widgets.dashboard-title-widget';

    /**
     * 布局占位
     *
     * Purpose: Make the widget span full width on dashboard grid.
     */
    protected array|string|int $columnSpan = 'full';
}