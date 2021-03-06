<?php
namespace App\Http\Controllers\Admin\Permission;

use App\Http\Controllers\Admin\Common\BasePage;
use App\Models\Role;
use App\Models\User;
use App\Renders\Form;
use App\Renders\Grid;

class Page extends BasePage
{
    public function __construct()
    {
        $this->setBoxTitle('权限管理');
        parent::__construct();
    }

    public function buildForm(Form $form, $id = null): BasePage
    {
        if($id){
            $form->display('id', 'ID');
        }
        $form->text('name', '标识')->rules('required');
        $form->text('display_name', '名称');
        $form->textarea('description', '描述');
        $form->multipleSelect('roles', '角色')->options(Role::where('id', '!=', User::SUPER_ADMIN_ID)->get()->pluck('display_name', 'id'));
        return parent::buildForm($form, $id); // TODO: Change the autogenerated stub
    }

    public function buildGrid(Grid $grid)
    {
        $grid->id('ID')->sortable();
        $grid->name('标识');
        $grid->column('display_name', '名称')->display(function ($value, $data){
            return ($data['menu'] ? '菜单-':'').$value;
        });
        $grid->roles('授权角色')->display(function ($roles){
            $roleStr = '';
            if($roles){
                foreach ($roles as $role){
                    $roleStr .= "<span class='label label-success'>{$role['display_name']}</span>";
                }
                return $roleStr;
            }else{
                return "<span class='label label-default'>未开放</span>";
            }

        });
        $grid->with(['id' => 'permission-table']);
        return parent::buildGrid($grid); // TODO: Change the autogenerated stub
    }
}