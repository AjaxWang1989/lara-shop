@extends('store.distribution.member.index')
@section('member_content')
    <div class="row">
        <div class="col-md-12">
           <form class="form-horizontal" id="form_detail" autocomplete="off">

                <div class="form-group" style="line-height: 60px; text-valign:center;">
                    <label for="headimgurl" class="col-sm-2 control-label">头像</label>
                    <div class="col-sm-5" id="headimgurl">
                        <img src="" style="width:60px;height:60px;border-radius: 50%;padding:1px;"/>
                    </div>
                </div>

                 <div class="form-group">
                    <label for="nickname" class="col-sm-2 control-label">昵称</label>
                    <div class="col-sm-5" id="nickname" style="margin-top:6px;"></div>
                </div>

                 <div class="form-group">
                    <label for="fans_id" class="col-sm-2 control-label">ID</label>
                    <div class="col-sm-5" id="fans_id" style="margin-top:6px;"></div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">上线</label>
                    <div class="col-sm-2" id="father_nickname" style="margin-top:6px;">无</div>
                    <label class="col-sm-1 control-label">ID</label>
                    <div class="col-sm-2" id="father_id" style="margin-top:6px;">无</div>
                </div>


                <div class="form-group">
                    <label for="p-name" class="col-sm-2 control-label">姓名</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="p-name"
                                placeholder="分销商名称"
                                autocomplete="off"
                                name="full_name"
                                required>
                        <div class="help-block with-errors"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="p-mobile" class="col-sm-2 control-label">手机号</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="p-mobile"
                            placeholder="手机号"
                            autocomplete="off"
                            name="mobile"
                            required>
                    <div class="help-block with-errors"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="p-wechat" class="col-sm-2 control-label">微信</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control" id="p-wechat"
                            placeholder="微信号不得少于5位"
                            autocomplete="off"
                            name="wechat"
                            required>
                    <div class="help-block with-errors"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="total_commission_wait" class="col-sm-2 control-label">未结算佣金</label>
                    <div class="col-sm-5" id="total_commission_wait" style="margin-top:6px;">0</div>
                </div>

                <div class="form-group">
                    <label for="total_commission_amount" class="col-sm-2 control-label">已结算佣金</label>
                    <div class="col-sm-5" id="total_commission_amount" style="margin-top:6px;">0</div>
                </div>

                <div class="form-group">
                    <label for="total_cash_amount" class="col-sm-2 control-label">可提现佣金</label>
                    <div class="col-sm-5" id="total_cash_amount" style="margin-top:6px;">0</div>
                </div>

                <div class="form-group">
                    <label for="waitamount" class="col-sm-2 control-label">待审核佣金</label>
                    <div class="col-sm-5" id="waitamount" style="margin-top:6px;">0</div>
                </div>

                <div class="form-group">
                    <label for="payamount" class="col-sm-2 control-label">已打款佣金</label>
                    <div class="col-sm-5" id="payamount" style="margin-top:6px;">0</div>
                </div>

                <div class="form-group">
                    <label for="sales_amount" class="col-sm-2 control-label">销售额</label>
                    <div class="col-sm-5" id="sales_amount" style="margin-top:6px;">0</div>
                </div>

                <div class="form-group">
                    <label for="apply_time" class="col-sm-2 control-label">注册时间</label>
                    <div class="col-sm-5" id="apply_time" style="margin-top:6px;"></div>
                </div>

                <div class="form-group">
                    <label for="join_time" class="col-sm-2 control-label">加入时间</label>
                    <div class="col-sm-5" id="join_time" style="margin-top:6px;"></div>
                </div>

                <div class="form-group">
                    <label for="is_active" class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-5" id="is_active" style="margin-top:6px;">
                        <input type="radio" name="is_active" value="1"> 开启
                        <input type="radio" name="is_active" value="0"> 冻结
                    </div>
                </div>

                <div class="form-group" id="change-title">
                    <label for="change" class="col-sm-2 control-label"> 变更上级</label>
                    <div class="col-sm-5" id="change" style="margin-top:6px;">
                            <input type="radio" name="select" value="0" id="primary"> <label for="primary">总部</label>
                            <input type="radio" name="select" value="1" id="hierarchy"> <label for="hierarchy">上级</label>
                            <span id="selectFather" style="display: none;"><input type="text" name="ancestor_id" placeholder="请输入上级分销商ID"></span>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-1">
                        <button class="btn btn-primary" id="submit">保存</button>
                        <button class="btn btn-default" type="reset">重置</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script>
    window.pageInit = function () {
        //填充表单数据
        var id = {{ $id }};
        var data = '';
        $.getJSON('/ajax/fenxiao/member/detail/' + id,function (json) {
            if(json.code == 200){
                data = json.data[0];
                if(data.apply_status == 2){  //已是分销商
                     $('#change [name=select]').click(function(){
                        if($(this).val() == 1){
                            $('#selectFather').show()
                        } else {
                            $('#selectFather').hide();
                        }
                    });
                    $('#is_active [name=is_active][value='+data.is_active+']').attr('checked',true);
                } else {  
                    //还未成为分销商,隐藏状态和改变上级
                    $('#is_active').parent('div').hide();
                    $('#change').parent('div').hide();
                    if(data.apply_status == 0){  //未申请成为分销商，隐藏申请时间和加入时间
                        $('#apply_time').parent('div').hide();
                        $('#join_time').parent('div').hide();
                    } else if(data.apply_status == 1){  //已提交申请，处于待审核，隐藏加入时间
                        $('#join_time').parent('div').hide();
                    } else {  //已拒绝
                        $('#join_time').prev().text('拒绝时间');
                    } 
                }
                $('#headimgurl').find('img').attr('src',data.headimgurl);
                $('#form_detail input[name=full_name]').val(data.full_name);
                $('#form_detail input[name=mobile]').val(data.mobile);
                $('#form_detail input[name=wechat]').val(data.wechat);
                if(data.fans_id) {
                    $('#fans_id').text(data.fans_id);
                }
                if(data.nickname) {
                    $('#nickname').text(data.nickname);
                }
                if(data.father_id) {
                    $('#father_id').text(data.father_id);
                }
                if(data.father_nickname) {
                    $('#father_nickname').text(data.father_nickname);
                }
                $('#total_commission_wait').text(data.total_commission_wait);
                $('#total_commission_amount').text(data.total_commission_amount);
                $('#total_cash_amount').text(data.total_cash_amount);
                if(data.waitamount){
                    $('#waitamount').text(data.waitamount);
                }
                if(data.payamount){
                    $('#payamount').text(data.payamount);
                }
                $('#sales_amount').text(data.sales_amount);
                $('#apply_time').text(data.apply_time);
                $('#join_time').text(data.join_time);

                if(data.referrals >= 1) {  //如果存在下级  则不显示更改上级操作
                    $('#change-title').hide();
                }
            } else {
                bootbox.alert({
                    title: '<span class="text-danger">加载失败</span>',
                    message:json.error,
                    callback: function () {
                        if(data.apply_status == 1 || data.apply_status == 3){
                            location.pathname = '/fenxiao/member/apply';
                        } else {
                            location.pathname = '/fenxiao/member'
                        }
                    }
                });
            }

        });
       
        // 表单验证提交
        $('#form_detail').validator().on('submit', function (e) {
            if(!e.isDefaultPrevented()){
                e.preventDefault();
                var params = $('#form_detail').serialize();
                $('#submit').attr('disabled', 'disabled');
                $('#submit').text('保存中...');
                $.post('/ajax/fenxiao/member/save/' + id, params, function (json) {
                    if(json.code == 200 ){
                        bootbox.alert({
                            title: '<span class="text-success">成功</span>',
                            message:'更新成功',
                            callback: function () {
                                if(data.apply_status == 1 || data.apply_status == 3){
                                    location.pathname = '/fenxiao/member/apply';
                                } else {
                                    location.pathname = '/fenxiao/member';

                                }
                            }
                        });
                    } else if(json.code == 401){
                        location.pathname = '/login';
                    } else if(json.code == 406){
                        bootbox.alert({
                            title: '<span class="text-danger">更新失败</span>',
                            message:json.error,
                            callback: function () {
                                if(data.apply_status == 1 || data.apply_status == 3){
                                    location.pathname = '/fenxiao/member/apply';
                                } else {
                                    location.pathname = '/fenxiao/member';

                                }
                            }
                        });
                    } else {
                        bootbox.alert({
                            title: '<span class="text-danger">错误</span>',
                            message: json.error
                        });
                        $('#submit').removeAttr('disabled');
                        $('#submit').text('保存');
                    }
                }, 'json')
            }
        })
    };
</script>
@stop
