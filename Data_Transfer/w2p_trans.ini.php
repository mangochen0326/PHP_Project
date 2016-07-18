;=================== system 說明  ==================================================;
;; email          => 收件者                        (kuen@email.yfp.com.tw,herlonglong@email.yfp.com.tw,jennis@email.yfp.com.tw,junior@email.yfp.com.tw)
;; cc             => 副本寄送者                    (chu61@email.yfp.com.tw)
;; namecard       => 難字收件者                    (chu61@email.yfp.com.tw)
;; error          => 異常訂單通知收件者            (herlonglong@email.yfp.com.tw)
;; bcc            => 外包廠商轉檔清冊須            (herlonglong@email.yfp.com.tw)
;;                   要另外通知的密件收件人
;; sendmail       => 是否寄送Email                 (Y/N)
;; tran_type      => 主機是處理外包或自製          (IN / OUT)
;================== ftp 說明 =======================================================;
;; Transfer       => 是否啟用FTP上傳               (Y/N)
;; DEL_FILE       => 轉檔失敗是否刪除以上傳FTP檔案 (Y/N)
;================== biz talk 說明 ==================================================;
;; Transfer       => 是否啟用XML傳遞biz talk       (Y/N)
;; host           => biz talk位址                  (192.168.10.6)
;; port           => biz talk port                 (80)
;; timeout        => biz talk timeout set          (20) sec  
;; url            => biz talk url set              (O/Otran/xmlwtpcf.cfm)
;===================biz1 biztalk備用主機================================================================;
;; Transfer       => 是否啟用XML傳遞biz talk       (Y/N)
;; host           => biz talk位址                  (192.168.10.11)
;; port           => biz talk port                 (80)
;; timeout        => biz talk timeout set          (20) sec  
;; url            => biz talk url set              (O/Otran/xmlwtpcf.cfm)
;===================================================================================;

[system]
main           ="arvin.chen@email.yfp.com.tw"
email          ="jennis@email.yfp.com.tw"
cc             ="arvin.chen@email.yfp.com.tw,tsungyu@email.yfp.com.tw,chu61@email.yfp.com.tw,herlonglong@email.yfp.com.tw,jennis@email.yfp.com.tw"
namecard       ="arvin.chen@email.yfp.com.tw,yifen@e0in.com,tsungyu@email.yfp.com.tw,herlonglong@email.yfp.com.tw"
error           ="herlonglong@email.yfp.com.tw,tsungyu@email.yfp.com.tw,yun@email.yfp.com.tw,yuling@email.yfp.com.tw,chu61@email.yfp.com.tw"
bcc             ="junior@email.yfp.com.tw,herlonglong@email.yfp.com.tw"
sendmail       ="Y"
tran_type      ="IN"

[ftp]
transfer = "Y"
DEL_FILE = "N"

[biz1]
transfer = "Y"
mode     = "1"
host     = "http://192.168.10.6"
port     = "80"
timeout  = "20"
url      = "O/Otran/xmlwtpcf.cfm"

[biz]
transfer = "Y"
mode     = "1"
host     = "https://192.168.10.105"
port     = "80"
timeout  = "20"
url      = "O/Otran/xmlwtpcf.cfm"

