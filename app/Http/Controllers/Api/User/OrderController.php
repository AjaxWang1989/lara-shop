<?php

namespace App\Http\Controllers\Api\User;

use App\Models\Merchandise;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShoppingCart;
use function foo\func;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\Controller;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    //
    public function list(Request $request)
    {
       $user = $this->user();
       $query = $user->orders()->with(['orderItems.refund']);
       $status = $request->input('status', null);
       if(array_search($status, array_keys(Order::REFUND_STATUS))){
           $query->whereHas('orderItems.refund', function (Builder $query) use($status){
               $query->where('status', Order::REFUND_STATUS[$status]);
           });
       }elseif (array_search($status, array_keys(Order::STATUS))) {
           $query->where('status', Order::STATUS[$status]);
           $query->whereHas('orderItems',function (Builder $query) use ($status){
               $query->where('status', Order::STATUS[$status]);
           });
       }elseif ($status != null){
           return \Response::errorApi('订单状态无效');
       }
       return \Response::api($this->buildList($query));
    }

    public function detail(int $id)
    {
        $data = Order::with('orderItems')->find($id);
        return \Response::api($data);
    }

    /*
     *
     * */
    public function createOrderFromRequest(array  $orderData) : Order
    {
        $this->validateOrder($orderData);
        $merchandise = Merchandise::find($orderData['merchandise_id']);
        $product = null;
        if(isset($orderData['product_id'])){
            $product = Product::find($orderData['product_id']);
        }
        $orderData['post_fee'] = 0;
        $orderData['discount_fee'] = 0;
        $orderItem = $this->createOrderItem($merchandise, $product, $orderData['num']);
        $order = $this->createOrder($orderData, $orderItem);
        $orderItem->order_code = $order->code;
        $order->orderItems()->save($orderItem);
        return $order;
    }

    public function createOrderFromShoppingCart(array $shoppingCarts)
    {
        $this->validateShoppingCarts($shoppingCarts);
        $items = [];
        $orderData = [];
        $ids = array_unique($shoppingCarts['ids']);
        $orderData['receiver_name'] = $shoppingCarts['receiver_name'];
        $orderData['receiver_mobile'] = $shoppingCarts['receiver_mobile'];
        $orderData['receiver_city'] = $shoppingCarts['receiver_city'];
        $orderData['receiver_district'] = $shoppingCarts['receiver_district'];
        $orderData['receiver_address'] = $shoppingCarts['receiver_address'];
        $orderData['post_code'] = $shoppingCarts['post_code'];
        $orderData['total_fee'] = 0;
        $orderData['discount_fee'] = 0;
        $orderData['post_fee'] = 0;
        $orderData['num'] = 0;
        $orderData['payment_fee'] = 0;
        $orderData['status'] = Order::STATUS['WAIT'];
        $orderData['buyer_user_id'] = $this->user()->id;
        $order = Order::create($orderData);
        foreach ($ids as $id){
            $shoppingCart = ShoppingCart::find($id);
            $orderItem = $shoppingCart->buildOrderItem();
            $orderItem->order_code = $order->code;
            $orderItem['post_fee'] = 0;
            $items[] = $orderItem;
            $order->total_fee += $orderItem->total_fee;
            $order->post_fee += $orderItem->post_fee;

            $order->num += $orderItem->num;
        }
        $order->payment_fee = $order->post_fee + $order->total_fee - $order->discount_fee;
        $order->save();

        $order->orderItems()->saveMany($items);
        ShoppingCart::destroy($ids);
        return $order;
    }
    /**
     * @param Request $request 分销时添加（distribution_user_id指定分销人员）
     * @return \Response
     * @throws
     * */
    public function create(Request $request)
    {
        $orderData = $request->input('order', null);
        $shoppingCarts = $request->input('shopping_carts', null);

        return dbTransaction(function () use(&$orderData, &$shoppingCarts){
            $order = null;
            if($orderData) {
                $order = $this->createOrderFromRequest($orderData);
            }else if ($shoppingCarts){
                $order = $this->createOrderFromShoppingCart($shoppingCarts);
            }else{
                return \Response::errorApi('缺少订单信息');
            }
            \DB::commit();
            return \Response::api($order);
        }, function ($exception){
            \DB::rollBack();
            if($exception instanceof ValidationException){
                return \Response::errorApi($exception->errors());
            }
            return \Response::exceptionApi($exception);
        });
    }

    protected function createOrder(array $order, OrderItem $orderItem) : Order
    {
        $data['post_fee'] = $orderItem['post_fee'];
        $data['total_fee'] = $orderItem['total_fee'] ;
        $data['payment_fee'] = $orderItem['total_fee'] + $data['post_fee'] - $order['discount_fee'];//可以加上post_fee
        $data['buyer_user_id'] = $this->user()->id;
        $data['num'] = $order['num'];
        $data['receiver_name'] = $order['receiver_name'];
        $data['receiver_mobile'] = $order['receiver_mobile'];
        $data['receiver_city'] = $order['receiver_city'];
        $data['receiver_district'] = $order['receiver_district'];
        $data['receiver_address'] = $order['receiver_address'];
        $data['post_code'] = $order['post_code'];
        $data['status'] = Order::STATUS['WAIT'];
        return Order::create($data);
    }

    protected function createOrderItem(Merchandise $merchandise, Product $product, int $num) : OrderItem
    {
        $data['store_id'] = $merchandise['store_id'];
        $data['store_code'] = $merchandise['store_code'];
        $data['buyer_user_id'] = $this->user()->id;
        if($product){
            $data['product_id'] = $product->id;
            $data['product_code'] = $product->code;
        }
        $data['name'] = $merchandise->name;
        $data['merchandise_code'] = $merchandise->code;
        $data['merchandise_id'] = $merchandise->id;
        $price = $merchandise->sell_price;
        if($product){
            $price = $product->sell_price;
        }
        $data['price'] = $price;
        $data['num'] = $num;
        $data['merchandise_main_image_url'] = $merchandise->main_image_url;
        $data['total_fee'] = $price * $num;
        $data['status'] = OrderItem::STATUS['WAIT'];
        if($product && $product['spec_array']){
            $data['sku_properties_name'] = '';
            foreach ($product['spec_array'] as $item){
                $data['sku_properties_name'] .= "{$item['name']}:{$item['value']};";
            }
        }
        $data['post_fee'] = 0;
        return new OrderItem($data);
    }

    protected function validateOrder(array $order)
    {
        return $this->valid($order, [
            'merchandise_id' => 'required|integer|exists:merchandise,id',
            'product_id' => 'integer|exists:product,id',
            'num'        => 'integer|min:1|order_num_limit',
            'receiver_name' => 'required',
            'receiver_mobile' => 'required|mobile',
            'receiver_city'   => 'required',
            'receiver_district' => 'required',
            'receiver_address'  => 'required',
            'post_code'         => 'required',
            'distribution_user_id' => [
                'integer',
                Rule::exists('user', 'id')
            ]
        ], [
            'merchandise_id.required' => '缺少产品id',
            'merchandise_id.integer' => '产品id必须是整数',
            'merchandise_id.exists' => '产品不存在',
            'product_id.integer' => '规格产品id必须是整数',
            'product_id.exists' => '规格产品不存在',
            'num.integer'       => '缺少购买数量',
            'num.min'           => '购买数量必须大于1',
            'num.order_num_limit' => '购买数量不能大于库存',
            'receiver_name.required' => '缺少收货人',
            'receiver_mobile.required' => '缺少收货人手机号',
            'receiver_mobile.mobile'   => '收货人手机号格式不对',
            'receiver_city.required'   => '缺少收货城市',
            'receiver_district.required' => '缺少收货县区',
            'receiver_address.required'  => '缺少收货地址',
            'post_code.required'         => '缺少邮编',
            'distribution_user_id.integer' => '分销人员ID数据格式错误',
            'distribution_user_id.exist' => '分销人员不存在',
        ]);
    }

    protected function validateShoppingCarts(array $shoppingCarts)
    {
        return $this->valid($shoppingCarts, [
            'ids' => 'required|shopping_carts',
            'receiver_name' => 'required',
            'receiver_mobile' => 'required|mobile',
            'receiver_city'   => 'required',
            'receiver_district' => 'required',
            'receiver_address'  => 'required',
            'post_code'         => 'required'
        ], [
            'ids.required' => '缺少产品id',
            'ids.shopping_carts' => '所选购物车id有错误',
            'receiver_name.required' => '缺少收货人',
            'receiver_mobile.required' => '缺少收货人手机号',
            'receiver_mobile.mobile'   => '收货人手机号格式不对',
            'receiver_city.required'   => '缺少收货城市',
            'receiver_district.required' => '缺少收货县区',
            'receiver_address.required'  => '缺少收货地址',
            'post_code.required'         => '缺少邮编'
        ]);
    }

    public function sign($orderId)
    {
        $order = Order::find($orderId);

        if($order){
            return \Response::errorApi('签收的订单不存在');
        }

        if($order->status != Order::STATUS['SEND']){
            return \Response::errorApi('订单未发货无法签收');
        }
        \DB::beginTransaction();
        try{
            $order->status = Order::STATUS['COMPLETED'];
            $order->completed_at = time();
            $order->orderItems->map(function (OrderItem $orderItem){
                $orderItem->status = OrderItem::STATUS['COMPLETED'];
                $orderItem->save();
            });
            $order->save();
            \DB::commit();
        }catch (\Exception $exception){
            \DB::rollBack();
            throw  $exception;
        }
        return \Response::api('签收成功');
    }
}
