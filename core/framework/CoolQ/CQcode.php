<?php
/**
 * CQ Code
 * 2019/10/01
 */

class CQCode {

 /**
  * 图片
  * @param string $url 图片URL
  * @return string $output
  */
 public static function image($url) {
  $code = '[CQ:image,file=%s]';
  $output = sprintf($code, $url);
  return $output;
 }

 /**
  * 语音
  * @param string $url 语音文件URL
  * @param bool $magic 是否标记为变声
  * @return string $output
  */
 public static function record($url, bool $magic = false) {
  $code = '[CQ:record,file=%s,magic=%s]';
  $output = sprintf($code, $url, $magic);
  return $output;
 }

 /**
  * 分享卡片
  * @param string $title 分享标题
  * @param string $content 概要
  * @param string $image 封面图
  * @param string $url 分享链接
  * @return string $output
  */
 public static function card($title, $content, $image, $url) {
  $code = '[CQ:share,url=%s,title=%s,content=%s,image=%s]';
  $output = sprintf($code, $url, $title, $content, $image);
  return $output;
 }

 /**
  * 自定义音乐卡片
  * @param string $title 分享标题
  * @param string $content 概要
  * @param string $image 封面图
  * @param string $url 分享链接
  * @param string $audioUrl 音乐播放链接
  * @return string $output
  */
 public static function DIYmusic($title, $content, $image, $url, $audioUrl) {
  $code = '[CQ:music,type=custom,url=%s,audio=%s,title=%s,content=%s,image=%s]';
  $output = sprintf($code, $url, $audioUrl, $title, $content, $image);
  return $output;
 }

 /**
  * 音乐
  * @param string $type 音乐类型（1 QQ音乐 | 2 网易云音乐 | 3 虾米音乐）
  * @param int $id 音乐UID
  * @return string $output
  */
 public static function music($type, $id) {
  if ($type == '1') { $type = 'qq'; }elseif ($type == '2') { $type = '163'; }elseif ($type == '3') { $type = 'xiami'; }else{ $type = '163'; }
  $code = '[CQ:music,type=%s,id=%s]';
  $output = sprintf($code, $type, $id);
  return $output;
 }
 
 /**
  * AT
  * @param int $qq 被艾特者 QQ 号
  * @return string $output
  */
 public static function at($qq) {
  $code = '[CQ:at,qq=%s]';
  $output = sprintf($code, $qq);
  return $output;
 }

 /**
  * Emoji
  * @param int $id emoji ID
  * @return string $output
  */
 public static function emoji($id) {
  $code = '[CQ:emoji,id=%s]';
  $output = sprintf($code, $id);
  return $output;
 }

 /**
  * 系统表情
  * @param int $id ID
  * @return string $output
  */
 public static function face($id) {
  $code = '[CQ:face,id=%s]';
  $output = sprintf($code, $id);
  return $output;
 }

 /**
  * 小表情
  * @param int $id ID
  * @return string $output
  */
 public static function sface($id) {
  $code = '[CQ:sface,id=%s]';
  $output = sprintf($code, $id);
  return $output;
 }

 /**
  * 原创表情
  * @param int $id ID
  * @return string $output
  */
 public static function bface($id) {
  $code = '[CQ:bface,id=%s]';
  $output = sprintf($code, $id);
  return $output;
 }

 /**
  * 抖动窗口（戳一戳）
  * ※仅好友私聊时有效
  * @return string $code
  */
 public static function shake() {
  $code = '[CQ:shake]';
  return $code;
 }
}