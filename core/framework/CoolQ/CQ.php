<?php
/**
 * CQ Core
 * 2019/10/01
 */

class CQ {

 /**
  * 根据消息来源发送消息
  *
  * @param array $data
  * @param string $message
  * @param bool $return
  */
 public static function sendMsg($data, $message, $return = false) {
  $type = $data['message_type'];
  switch($type) {
   case 'group':
    return OBot_HTTP_API::Group($data['group_id'], $message, $return);
    break;
   case 'private':
    return OBot_HTTP_API::Privates($data['sender']['user_id'], $message, $return);
    break;
   case 'discuss':
    return OBot_HTTP_API::Privates($data['discuss_id'], $message, $return);
    break;
   default:
    OBot_Console::diy("{red}无法判断消息来源.{r}", "[{red}Send Message Error{r}] ");
    break;
  }
 }
}