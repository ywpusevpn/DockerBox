<?php
namespace EasySwoole\EasySwoole;


use EasySwoole\EasySwoole\Swoole\EventRegister;
use EasySwoole\EasySwoole\AbstractInterface\Event;
use EasySwoole\Http\Request;
use EasySwoole\Http\Response;
use EasySwoole\Rpc\Config as RpcConfig;
use EasySwoole\Rpc\Rpc;
use App\RpcService\Common;
use App\RpcService\Goods;
use EasySwoole\Rpc\NodeManager\RedisManager;
use EasySwoole\RedisPool\RedisPool;
use EasySwoole\Redis\Config\RedisConfig;

class EasySwooleEvent implements Event
{

    public static function initialize()
    {
        date_default_timezone_set('Asia/Shanghai');
        //配置redis连接池
        $config = new \EasySwoole\Pool\Config();
        $redisConfig = new \EasySwoole\Redis\Config\RedisConfig(\EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS'));
        \EasySwoole\Pool\Manager::getInstance()->register(new \App\Pool\RedisPool($config,$redisConfig),'redis');
    }

    public static function mainServerCreate(EventRegister $register)
    {
        //redis连接池
        $redisPool = new RedisPool(new RedisConfig(\EasySwoole\EasySwoole\Config::getInstance()->getConf('REDIS')));
        $redisManager = new RedisManager($redisPool);
        //配置Rpc实例
        $config = new RpcConfig();
        $config->setServerIp('127.0.0.1');
        $config->setNodeManager($redisManager);
        Rpc::getInstance($config);
        //添加服务
        Rpc::getInstance()->add(new Goods());
        Rpc::getInstance()->add(new Common());
        Rpc::getInstance()->attachToServer(ServerManager::getInstance()->getSwooleServer());

        $server = ServerManager::getInstance()->getSwooleServer();
        // $list = swoole_get_local_ip();
        // var_dump($list);
        // go(function () {
        //     $client = new \Swoole\Client(SWOOLE_SOCK_TCP);
        //     $client->set(
        //         [
        //             'open_length_check'     => true,
        //             'package_max_length'    => 81920,
        //             'package_length_type'   => 'N',
        //             'package_length_offset' => 0,
        //             'package_body_offset'   => 4,
        //         ]
        //     );
        //     if (!$client->connect('172.18.5.62',9700, 0.5)) {
        //         exit("connect failed. Error: {$client->errCode}\n");
        //     }
        //     // $str = 'hello world';
        //     // $client->send(encode($str));
        //     // $data = $client->recv();//服务器已经做了pack处理
        //     // var_dump($data);//未处理数据,前面有4 (因为pack 类型为N)个字节的pack
        //     // $data = decode($data);//需要自己剪切解析数据
        //     // var_dump($data);
        // });

        $subPort1 = $server->addlistener('0.0.0.0', 9700, SWOOLE_TCP);
        $subPort1->set(
            [
                'open_length_check' => false,
            ]
        );
        $subPort1->on('connect', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "客户端:{$fd} 已连接".PHP_EOL;
            $str = "服务器:欢迎你{$fd}连接成功".PHP_EOL;
            $server->send($fd, $str);
        });
        $subPort1->on('close', function (\swoole_server $server, int $fd, int $reactor_id) {
            echo "客户端:{$fd} 已关闭".PHP_EOL;
        });
        $subPort1->on('receive', function (\swoole_server $server, int $fd, int $reactor_id, string $data) {
            echo "客户端:{$fd} 发送消息:{$data}".PHP_EOL;
            $message = "服务器:客户端->{$fd}说{$data}";
            $start_fd = 0;
            while(true)
            {
                $conn_list = $server->getClientList($start_fd, 10);
                if ($conn_list===false or count($conn_list) === 0)
                {
                    echo "finish\n";
                    break;
                }
                $start_fd = end($conn_list);
                foreach($conn_list as $fd)
                {
                    $server->send($fd,$message);
                }
            }
        });
    }

    public static function onRequest(Request $request, Response $response): bool
    {
        return true;
    }

    public static function afterRequest(Request $request, Response $response): void
    {
    }
}