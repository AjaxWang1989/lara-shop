<?php
namespace App\Http\Controllers\Admin\Order;
use App\Models\Order;
use App\Models\OrderItem;
use App\Renders\Form;
use App\Http\Controllers\Admin\Common\BasePage;
use App\Renders\Grid\Filter;

class Page extends BasePage
{
    public function __construct()
    {
        parent::__construct();
        $this->setBoxTitle('订单管理');
    }

    public function grid()
    {
        return new Grid(new Order(), function (Grid $grid){
            if ($this->conditionCallback) {
                $grid->model()->addConditions(call_user_func($this->conditionCallback));
            }
            $grid->filter(function (Filter $filter){
                $filter->disableIdFilter();
                $filter->like('code', '订单号');
                $filter->between('created_at', '下单时间')->datetime();
                $filter->like('receiver_name', '微信昵称')->placeholder('请填写微信昵称');
                $filter->equal('orderItems.status', '订单状态')->select(OrderItem::STATUS_ZH_CN);
            });
        });
    }

    protected function buildForm(Form $form, $id = null): BasePage
    {
        return parent::buildForm($form, $id); // TODO: Change the autogenerated stub
    }
}
