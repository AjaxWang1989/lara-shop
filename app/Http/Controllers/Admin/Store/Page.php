<?php
namespace App\Http\Controllers\Admin\Store;

use App\Http\Controllers\Admin\Common\BasePage;
use App\Models\Store;
use App\Models\StoreManager;
use App\Models\User;
use App\Renders\Facades\SectionContent;
use App\Renders\Form;
use App\Renders\Grid;
use Illuminate\Database\Eloquent\Collection;

class Page extends BasePage
{
    public function __construct()
    {
        $this->setBoxTitle('店铺管理');
        parent::__construct();
    }

    protected function filter(Grid\Filter $filter)
    {
        parent::filter($filter); // TODO: Change the autogenerated stub
    }

    protected function buildGrid(Grid $grid)
    {
        $grid->disableCreation();
        $id = 0;
        $grid->id('ID');
        $grid->column('logo_url', 'logo图片')->display(function ($url){
            if($url){
                return <<<IMAGE
                <div style = "width:42px; height:42px; margin: auto;">
                    <img style = "width: 100%; vertical-align: middle;" src = "{$url}">
                </div>
IMAGE;
            }else{
                return '';
            }
        });

        $grid->name('店铺名称');
        $grid->wechat('联系人微信');
        $grid->qq('联系人QQ');

        $grid->owner('店主')->display(function ($storeOwner, $data) use(&$id){
            if($storeOwner){
                return <<<OWNER
<span class = "text-warning">{$storeOwner['user']['nickname']}</span><br>
<span><img style = "width: 32px;" src = "{$storeOwner['user']['head_image_url']}"></span><br>
<span class = "text-success">{$storeOwner['user']['mobile']}</span>
OWNER;
            }else{
                return <<<HTML
                        <a href="#" >--</a>
HTML;
            }
        });

        $grid->managers('店铺管理员')->display(function ($managers, $data) use (&$id){
            $str = !$managers ? '' : collect($managers)->map(function (array $manager, $index){
                return <<<OWNER
                    <span class = "text-danger">NO.{$index}:</span>&nbsp;&nbsp;&nbsp;&nbsp;<span class = "text-warning">{$manager['user']['nickname']}</span>
                    /<span class = "text-success">{$manager['user']['mobile']}</span><br>
OWNER;
            });

            return !$str ? "<a href='#' >--</a>" : $str->implode('') ;
        });
        $grid->column('status', '状态')->display(function ($status, $data) use(&$id){
            $statusZh = Store::STATUS_ZH_CN[$status];
            switch ($status){
                case Store::STATUS['APPLY']:{
                    return <<<STATUS
                            <a class = "btn btn-primary btn-xs store-agree-btn" data-url = "/ajax/store/{$data['id']}/agree" data-title = "是否同意通过此店铺开店申请？" data-id = "{$id}" href = "javascript:void(0)"><span >接受申请</span></a>
                            <a class = "btn btn-default btn-xs store-refuse-btn" data-url = "/ajax/store/{$data['id']}/refuse" data-title = "是否拒绝通过此店铺开店申请？" data-id = "{$id}" href = "javascript:void(0)"><span >拒绝申请</span></a>
STATUS;

                    break;
                }
                case Store::STATUS['PASS']:{
                    return <<<STATUS
                            <span class='btn label label-success'>{$statusZh}</span>
STATUS;
                    break;
                }
                case Store::STATUS['REFUSE']:{
                    return <<<STATUS
                            <span class='btn label label-default'>{$statusZh}</span>
STATUS;
                    break;
                }
            }
            return "";
        });
        $script = <<<SCRIPT
$('.store-agree-btn, .store-refuse-btn').unbind('click').click(function() {
    var id = $(this).data('id');
    var title = $(this).data('title');
    var url = $(this).data('url');
    swal({
      title: title,
      type: "warning",
      showCancelButton: true,
      confirmButtonColor: "#DD6B55",
      confirmButtonText: "确认",
      closeOnConfirm: false,
      cancelButtonText: "取消"
    },
    function(){
        $.ajax({
            method: 'put',
            url: url,
            data: {
                _token:LA.token,
            },
            success: function (data) {
                $.pjax.reload('#pjax-container');

                if (typeof data === 'object') {
                    if (data.success) {
                        swal(data.data.message, '', 'success');
                    } else {
                        swal(data.data.message, '', 'error');
                    }
                }
            }
        });
    });
});
SCRIPT;

        $grid->column('', '店铺管理')->display(function  ($managers, $data){
            return "
                <a class='btn btn-default btn-xs' href='/distribution?store_id={$data['id']}'>分销管理</a> 
                <a class='btn btn-default btn-xs' href='/merchandises?store_id={$data['id']}'>商品管理</a>";
        });

        SectionContent::script($script);
        return parent::buildGrid($grid); // TODO: Change the autogenerated stub
    }

    protected function buildForm(Form $form, $id = null): BasePage
    {
        SectionContent::link(asset('/vue/css/app.css'));
        SectionContent::jsLoad(asset('/vue/js/app.js'));
        if($id){
            $form->display('id', 'ID');
        }
        $form->vueUpload('logo_url', '店铺LOGO')->attribute(['url' => '/ajax/user/avatar', 'id' => 'app'])->rules('required');
        $form->text('name', '店铺名称')->rules('required');
        $form->text('wechat', '联系人微信')->rules('required');
        $form->text('qq', '联系人QQ')->rules('required');
        return parent::buildForm($form, $id); // TODO: Change the autogenerated stub
    }
}