<?php
/**
 * The file was created by Assimon.
 *
 * @author    assimon<ashang@utf8.hk>
 * @copyright assimon<ashang@utf8.hk>
 * @link      http://utf8.hk/
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    /**
     * 全局取消批量赋值保护
     * 
     * Purpose: allow Filament forms to mass-assign model attributes safely.
     */
    protected $guarded = [];

    const STATUS_OPEN = 1; // 状态开启
    const STATUS_CLOSE = 0; // 状态关闭

    const AUTOMATIC_DELIVERY = 1; // 自动发货
    const MANUAL_PROCESSING = 2; // 人工处理

    /**
     * map
     *
     * @return array
     *
     * @author    assimon<ashang@utf8.hk>
     * @copyright assimon<ashang@utf8.hk>
     * @link      http://utf8.hk/
     */
    public static function getIsOpenMap()
    {
        return [
            self::STATUS_OPEN => __('dujiaoka.status_open'),
            self::STATUS_CLOSE => __('dujiaoka.status_close')
        ];
    }

}
