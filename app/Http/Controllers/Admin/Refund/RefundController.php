<?php

namespace App\Http\Controllers\Admin\Refund;

use App\Models\Refund;
use EasyWeChat\Payment\Application as PaymentApplication;
use \EasyWeChat\MiniProgram\Application as MiniProgramApplication;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends Controller
{
    //
    /**
     * @var PaymentApplication
     * */
    protected $paymentApp = null;

    /**
     * @var
     * */
    protected $miniProgramApp = null;
    public function __construct(PaymentApplication $paymentApplication, MiniProgramApplication $miniProgramApplication)
    {
        $this->paymentApp = $paymentApplication;

        $this->miniProgramApp = $miniProgramApplication;
    }

    public function refund($id)
    {
        $refund = Refund::find($id);

        if(!$refund){
            return response()->errorApi('没有退款申请记录');
        }else{
            $result = $this->paymentApp->refund->byOutTradeNumber(
                $refund->order_code,
                $refund->code,
                $refund->total_fee,
                $refund->refund_fee,
                [
                    'refund_account'  => $refund->refund_account,
                    'refund_fee_type' => $refund->fee_type,
                    'refund_desc'     => $refund->refund_reason,
                    ''
                ]
            );

            if($result['return_code'] == 'SUCCESS'){
                if($result['result_code'] == 'SUCCESS'){
                    $refund->status = Refund::STATUS['REFUNDED'];
                    $refund->save();
                }else{
                    $refund->error_code = $result['err_code'];
                    $refund->save();

                }
            }elseif($result['return_code'] == 'FAIL'){
                $refund->error_code = "SIGN_ERROR";
                $refund->save();
                return response()->errorApi('签名失败');
            }
        }
        return response()->api('退款成功');
    }
}
