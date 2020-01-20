<?php


namespace App\HttpController;


use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\Rpc\Response;
use EasySwoole\Rpc\Rpc;

class Index extends Controller
{

    function index()
    {
        $this->response()->write("<h2>hello easyswoole http</h2>");
    }

    protected function actionNotFound(?string $action)
    {
        $this->response()->withStatus(404);
        $file = EASYSWOOLE_ROOT.'/vendor/easyswoole/easyswoole/src/Resource/Http/404.html';
        if(!is_file($file)){
            $file = EASYSWOOLE_ROOT.'/src/Resource/Http/404.html';
        }
        $this->response()->write(file_get_contents($file));
    }

    function test(){
        
        $ret = [];
        $client = Rpc::getInstance()->client();

        //调用商品服务
        $client->addCall('goods','list',['page'=>1])
            ->setOnSuccess(function (Response $response)use(&$ret){
                $ret['goods'] = $response->toArray();
            })->setOnFail(function (Response $response)use(&$ret){
                $ret['goods'] = $response->toArray();
            });
        
        $client->exec(2.0);
        $this->writeJson(200,$ret);
    }
}