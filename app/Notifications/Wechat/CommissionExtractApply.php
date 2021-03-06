<?php

namespace App\Notifications\Wechat;

use App\Channels\Messages\WechatTemplateMessage;
use App\Channels\WxMessageTemplateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\Wechat\TemplateMessageSender as Notification;


class CommissionExtractApply extends Notification
{

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        parent::__construct();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [WxMessageTemplateChannel::class];
    }

    /**
     * @param Notifiable $notifiable
     * @param string $driver
     * @return WechatTemplateMessage
     * @throws
     * */
    public function toWxMsgTemplate(Notifiable $notifiable, string  $driver = 'wechat')
    {
        //模版消息设置代码写在这里
        if(isset($notifiable->miniProgramUser) && isset($notifiable->miniProgramUser->open_id)){
            $this->templateMessage
                ->setTemplateId($this->templateMessageId)
                ->setTo($notifiable->miniProgramUser->open_id)
                ->setMessageData([]);
            return parent::toWxMsgTemplate($notifiable); // TODO: Change the autogenerated stub
        }else{
            throw new \Exception('没有微信open id');
        }
    }
}
