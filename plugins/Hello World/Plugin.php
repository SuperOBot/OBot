<?php
/**
 * Hello World
 * 
 * @package Hello World
 * @author ohmyga
 * @version 0.1.0
 * @link https://github.com/ohmyga233
 */

class Hello_Plugin implements OBot_Plugin_Interface { 

 public static function handle($data) {
  OBot_Plugin::regCommand('fuzzy', '0', ['[!|！]Hello World' => ['Hello_Plugin', 'send']]);
 }

 public static function send($data) {
  CQ::sendMsg($data, 'Hello World', true);
 }
}