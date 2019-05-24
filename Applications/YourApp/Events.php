<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

use \GatewayWorker\Lib\Gateway;

/**
 * 主逻辑
 * 主要是处理 onConnect onMessage onClose 三个方法
 * onConnect 和 onClose 如果不需要可以不用实现并删除
 */
class Events
{
    public static $user = [];
    public static $uuid = [];
    public static function onWorkerStart($businessWorker)
    {   //服务准备就绪
        echo "Worker_socket_ready\n";
    }


    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * 
     * @param int $client_id 连接id
     */
    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode(array(
            'type'      => 'login',
            'client_id' => $client_id
        )));

//        // 向当前client_id发送数据
//        Gateway::sendToClient($client_id, "Hello $client_id\r\n");
//        // 向所有人发送
//        Gateway::sendToAll("$client_id login\r\n");
    }
    
   /**
    * 当客户端发来消息时触发
    * @param int $client_id 连接id
    * @param mixed $message 具体消息
    */
   public static function onMessage($client_id, $message)
   {
       Gateway::sendToClient($client_id, json_encode(array(
           'type'      => 'heart',
           'client_id' => $client_id
       )));
//       Gateway::sendToAll("$client_id said $message\r\n");
//       /*监听事件，需要把客户端发来的json转为数组*/
//       $data = json_decode($message, true);
//       switch ($data['type']) {
//
//           //当有用户上线时
//           case 'reg':
//               //绑定uid 用于数据分发
//               Gateway::bindUid($client_id, $data['content']['uid']);
//               self::$user[$data['content']['uid']] = $client_id;
//               self::$uuid[$data['content']['uid']] = $data['content']['uid'];
//
//               //给当前客户端 发送当前在线人数，以及当前在线人的资料
//               $reg_data['uuser'] = self::$uuid;
//               $reg_data['num'] = count(self::$user);
//               $reg_data['type'] = "reguser";
//               Gateway::sendToClient($client_id, json_encode($reg_data));
//
//               //将当前在线用户数量，和新上线用户的资料发给所有人 但把排除自己，否则会出现重复好友
//               $all_data['type'] = "addList";
//               $all_data['content'] = $data['content'];
//               $all_data['content']['type'] = 'friend';
//               $all_data['content']['groupid'] = 2;
//               $all_data['num'] = count(self::$user);
//               Gateway::sendToAll(json_encode($all_data), '', $client_id);
//               break;
//
//
//           case 'chatMessage':
//               //处理聊天事件
//               $msg['username'] = $data['content']['mine']['username'];
//               $msg['avatar'] = $data['content']['mine']['avatar'];
//               $msg['id'] = $data['content']['mine']['id'];
//               $msg['content'] = $data['content']['mine']['content'];
//               $msg['type'] = $data['content']['to']['type'];
//               $chatMessage['type'] = 'getMessage';
//               $chatMessage['content'] = $msg;
//
//               //处理单聊
//               if ($data['content']['to']['type'] == 'friend') {
//
//                   if (isset(self::$uuid[$data['content']['to']['id']])) {
//                       Gateway::sendToUid(self::$uuid[$data['content']['to']['id']], json_encode($chatMessage));
//                   } else {
//                       //处理离线消息
//                       $noonline['type'] = 'noonline';
//                       Gateway::sendToClient($client_id, json_encode($noonline));
//                   }
//               } else {
//                   //处理群聊
//                   $chatMessage['content']['id'] = $data['content']['to']['id'];
//                   Gateway::sendToAll(json_encode($chatMessage), '', $client_id);
//               }
//               break;
//       }
        // 向所有人发送 
//        Gateway::sendToAll("$client_id said $message\r\n");
   }
   
   /**
    * 当用户断开连接时触发
    * @param int $client_id 连接id
    */
   public static function onClose($client_id)
   {
       Gateway::sendToAll(json_encode(array(
           'type'      => 'logout',
           'client_id' => $client_id
       )));
       // 向所有人发送 
//       GateWay::sendToAll("$client_id logout\r\n");
   }
}
