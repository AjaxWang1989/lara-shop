<?php

namespace App\Models;

use App\Models\Traits\ModelTrait;
use App\Models\Traits\WechatUserRelationTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;

/**
 * App\Models\Store
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StoreManager[] $managerRelations
 * @property-read \App\Models\StoreOwner $ownerRelation
 * @mixin \Eloquent
 * @property int $id
 * @property string $name 店铺名称
 * @property string $logo_url 店铺logo图片
 * @property float $amount 店铺余额
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereLogoUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereUpdatedAt($value)
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store deleteByIds($ids)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store search($where)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store updateById($id, $data)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\StoreManager[] $managers
 * @property-read \App\Models\StoreOwner $owner
 * @property string|null $wechat
 * @property string|null $qq
 * @property string $status 店铺状态
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereQq($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Store whereWechat($value)
 */
class Store extends Model
{
    use ModelTrait;

    use Notifiable;

    protected $table = 'store';

    const STATUS = [
        'APPLY' => 'APPLY',
        'PASS' => 'PASS',
        'REFUSE' => 'REFUSE'
    ];

    const STATUS_ZH_CN = [
        'APPLY' => '申请中',
        'PASS' => '通过',
        'REFUSE' => '拒绝'
    ];

    protected $fillable = [
        'id',
        'name',
        'logo_url',
        'amount',
        'status',
        'wechat',
        'qq'
    ];

    public function owner() : HasOne
    {
        return $this->hasOne('App\Models\StoreOwner', 'store_id', 'id');
    }

    public function managers() : HasMany
    {
        return $this->hasMany('App\Models\StoreManager', 'store_id', 'id');
    }
}
