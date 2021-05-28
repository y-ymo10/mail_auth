<?php

     class Auth{
        private $code = null;
        private $mail = null;
        /**
         * randam関数 {integer}:乱数を
         * @param integer $n:乱数の桁数
         * @return string :引数$n整数の桁の乱数を表示する
         * 
         */
        private function randam($n){
            $val = null;
            for($i=0; $i <= $n; $i++){
                $val .= mt_rand(0, 9);
            }
            return $val;
        }
        /**
         * Authクラス_コンストラクター{void}
         * @param integer $n:乱数の桁数randam関数の引数へ渡される
         */
        function __construct($n){
            $auth_number=[$this->randam($n),$this->randam($n),$this->randam($n),$this->randam($n)];
            $rand = mt_rand(0,3);
            $_SESSION['keys']=$rand;
            $_SESSION['num']=$auth_number;
            $this->code = $auth_number[$rand];
            
        }
        /**
         * send関数 {void} :確認コードを本文に記載し他メールの送信を行う
         * @param string $to:送信先メールアドレス
         * @param string $from:送信元のメールアドレス
         * @param string $subject:メールのタイトル
         */
        function send($to="to@extends.com",$from = "from@extends.com",$subject="確認コード"){
            $body="確認コード：".$this->code;
            $header = "From:".$from;
            mb_language("Japanese"); 
            mb_internal_encoding("UTF-8");
            mb_send_mail($to, $subject, $body, $header);
        }
        
        /**
         * csrf関数{string}:csrf対策のトークンを返す関数
         * @return string :CSRF対策用トークン
         */
        public static function csrf(){
            if (session_status() === PHP_SESSION_NONE) {
                throw new \BadMethodCallException('Session is not active.');
            }

            return hash('sha256', session_id());
        }
        /**
         * Create_CSRF関数 {void}:CSRF対策のトークンを生成しinputタグを自動生成する。
         */
        public static function Create_CSRF(){
            $hidden = "<input type='hidden' name='token' value='{$this->csrf()}'>";
            echo $hidden;
        }
        /**
         * validate関数 {boolean}:CSRF対策のトークンの認証を実施する関数　
         */
        protected function validate($token,$throw = false){
            $csrf = false;
                if($this->csrf() === $token){
                    $csrf = true;
                }
                if (!$success && $throw) {
                    throw new \RuntimeException('CSRF validation failed.', 400);
                }
                return $csrf;
            }
        }
        
    final class Check extends Auth{
        function __construct(){

        }
        /**
         * Auth関数{boolean} :CSRF対策トークンと確認コードの認証を行う関数
         * @return boolean :CSRFと確認コードの認証が成功すればtrue,失敗すればfalseを返す
         * @param integer $get:確認コードの挿入を行う。
         * @param string $token:CSRF対策のトークンの認証を実施する。
         * 
         */
        public function Auth($get,$token){
            if($this->validate($token)===TRUE){
                if($get === $_SESSION['num'][$_SESSION['keys']]){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }