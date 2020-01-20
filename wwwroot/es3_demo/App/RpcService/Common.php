<?php
namespace App\RpcService;


use EasySwoole\Rpc\AbstractService;

class Common extends AbstractService
{
    public function serviceName(): string
    {
        return 'common';
    }

    public function mailBox()
    {
        $this->response()->setResult([
            [
                'mailId'=>'100001',
                'mailTitle'=>'系统消息1',
            ],
            [
                'mailId'=>'100001',
                'mailTitle'=>'系统消息1',
            ],
        ]);
        $this->response()->setMsg('get mail list success');
    }

    public function serverTime()
    {
        $this->response()->setResult(time());
        $this->response()->setMsg('get server time success');
    }
}