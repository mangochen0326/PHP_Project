<?php
    /***********************************************************************************************
    *  WTP 批次轉檔主程式 w2p_trans.php
    *  設定檔：       w2p_trans.ini.php
    *  合板產品分支： w2p_array.php
    *  photo產品分支：vow_array.php
    *  XML 樣版：     xml_template.php
    *  額外 XML 樣版: extra_xml_template.php
    *  轉檔清冊信件： w2p_mail.php
    *  物件         : class.php
    *  2010/07/05 Code by Arvin
    *  2010/11/18 Arvin 修改T-shrit轉檔清冊
    *  2010/12/8  Arvin BOPTION欄位從PORDERDATA移至 PORDER 內
    *  2010/12/23 Arvin 配合非合板產品的拼版需用 ERP 訂單編號，修改相關程式(改成一開始就傳 XML 要VB訂單號碼)
    *  2011/03/1  Arvin 為避免同一人歸屬多群組造成轉檔清冊錯誤，撈訂單SQL語法修正
    *  2011/03/11 Arvin PGPRICE 架構異動，所以在Join 時不在多判斷 BPAGE (03/23取消，連PGPRICE都不join)
    *  2011/03/23 Arvin 因應PGPRICE架構異動，作品集頁數不固定無法Join PGPRICE資料表，導致品名(BMEMO)要另外抓，增加findname function
    *  2011/04/01 Arvin 增加兌換碼判斷，若有對應訂單號碼則寫入XML
    *  2011/04/08 Arvin 增加直接先抓取裝訂方式及紙別存入VOW_ARRAY內
    *  2011/04/26 Arvin 移除抓不到業務就預設文正的設定
    *  2011/05/18 Arvin 增加判斷訂購數量大於 0 才轉檔
    *  2011/05/24 Arvin 將 w2p_mail內的工作單、轉檔清冊表頭移入，避免重複include w2p_mail造成function重複宣告
    *  2011/05/25 Arvin 修改抓取實際生產檔案資料抓取資料表 PORDERDATA 改成 W2POP
    *  2011/05/30 Arvin 增加search_cus_no來抓訂單的客戶代號
    *  2011/06/17 Arvin 增加加工條件資料於轉檔清冊備註顯示
    *  2011/06/23 Arvin 增加抓取紙別綁定加工條件資料
    *  2011/07/05 Arvin 修改抓cus_no function，增加抓出GGID用來查詢難字登記表用
    *  2011/07/20 Arvin 增加判斷BOINVTYPE 發票型態，若為21則在BOMEMO內加上二聯式捐贈，25為電子發票
    *  2011/07/26 Arvin 增加抓取CPBONUS欄位提供後續判斷兌換券
    *  2011/08/01 Arvin 修改SQL抓中文的方式
    *  2011/08/04 Arvin 修改opday抓加工日期的方式，修改區分為工廠約交日與消費者約交日，工作單、轉檔清冊是工廠約交日
    *  2011/09/01 Arvin 作品集封面Indigo可印產品若找不到封面紙別，預設帶122
    *  2011/09/01 Arvin 永豐消費金融處歸屬業務強制改成 70095 林逸銘
    *  2011/09/14 Arvin 包裝盒不開VBB訂單，用MANUF_COMP = YFP2來給琇雲判斷
    *  2011/10/05 Arvin 增加判斷折價券的業務歸屬判斷
    *  2011/11/04 Arvin A5直不管任何裝訂方式都改indigo印，且紙別換銅板貼紙
    *  2011/11/07 Arvin iphone殼轉檔程式更新
    *  2011/12/14 Arvin 移除轉檔時生產工廠判斷，直接抓PORDER.FACID出來分辨此筆訂單要轉哪個生產工廠
    *  2012/02/06 Arvin 判斷平裝A4直式、B5直式、A5直式、A5橫式、21正方時才帶入料號
    *  2012/03/09 Arvin 增加MEMO2存放包裝紙盒數量資訊
    *  2012/03/12 Arvin 增加工作單條碼，並移除欄位發票號碼、平台編號
    *  2012/03/30 Arvin 增加抓取Coupon碼出來判斷神腦試作
    *  2012/03/31 Arvin 新增getsize 來抓取貼紙的規格及計算出的車數
    *  2012/04/25 Arvin 增加異常訂單通知
    *  2012/05/02 Arvin 抓取合併交寄主訂單資訊改用get_mainboid func 處理
    *  2012/05/22 Arvin 增加PDFtoJPG Func廣告面紙PDF轉JPG用
    *  2012/05/28 Arvin 修改除了用indigo印的封面帶封面料號給MIS外，其餘需要大圖噴的料號不帶入XML，由MIS那邊固定寫入
    *  2012/06/14 Arvin 修改作品集、明信片、貼紙、DM散客的業務歸屬改為文正 00587
    *  2012/06/21 Arvin 判斷有自製及外包可能的產品，若該筆訂單是外包在@後面多增加800這個代號給MIS
    *  2012/07/02 Arvin 判斷蝴蝶裝產品在轉檔時多帶127紙別過去開訂單
    *  2012/07/05 Arvin 拼圖直接從前台就變更為 FACID=10 自製
    *  2012/07/05 Arvin 貼紙刀模增加 E 郵票規格，相關命名同矩形
    *  2012/07/06 Arvin 目前編輯器的外包產品不存ITEM=NAME的資料，抓取資料時做轉換
    *  2012/07/16 Arvin 紙袋轉檔修正
    *  2012/08/22 Arvin A4直,A5直橫,21,B5直平裝封面Indigo印的料號由123(特銅250) 變更為112(銅西250P)
    *  2012/08/24 Arvin get_mainboid func 增加購物車代號，抓出主訂單訂單編號 (移除)
    *  2012/08/28 Arvin 拼圖木框793 轉換
    *  2012/08/29 Arvin 吸水杯墊會依據數量條件變更生產單位
    *  2012/09/04 Arvin 移除XML W2P CODE中若有自製及外包可能的產品，若該筆訂單是外包在@後面多增加800這個代號的判斷
    *  2012/09/10 Arvin 增加抓取設計師代號及分紅金額
    *  2012/10/04 Arvin 增加INI設定判斷轉檔是要轉外包還是自製
    *  2012/10/11 Arvin 拼圖木盒要多帶積木紙盒的料號進去
    *  2012/10/11 Arvin 複寫聯單處理上正式機
    *  2012/10/12 Arvin 便利貼要多帶封面料號過去
    *  2012/10/16 Arvin 吸水杯墊全部轉回永豐處理
    *  2012/11/29 Arvin 貼紙落版檔案位置改到BID下面不覆蓋001.pdf
    *  2013/02/07 Arvin 修正抓歸屬業務的步驟
    *  2013/03/18 Arvin 調整原先的檔案處理流程，增加複寫聯單的流水號位置座標及起號；自製名片轉版號給ERP;依您印自製名片處理
    *  2013/04/17 Arvin 增加鏡盒檔案名稱 001設定
    *  2013/05/07 Arvin search_sales增加抓取GGID用來判斷台中商業銀行發票隨貨送
    *  2013/06/19 Arvin 增加處理BTYPE=40的自製企業DM
    *  2013/06/20 Arvin 名片增加對色資訊，在class.php裡面判斷需要對色的模板
    *  2013/07/09 Arvin 增加多配送點資訊
    *  2013/07/30 Arvin 增加向前興業FACID=26，新竹貨運開始轉檔
    *  2013/08/06 Arvin INI增加設定密件副本
    *  2013/08/07 Arvin 增加設定部分企業的名片要做合併寄送
    *  2013/08/20 Arvin 判斷另開訂單時若沒有合併交寄資訊，要塞資料進去讓ERP那邊去做關聯
    *  2013/09/12 Arvin 增加抓取PORDER.BOTYPE來判斷內部自用的種類 'B1'自用訂單(客訴) 'B2'自用訂單(打樣) 'B3'自用訂單(試作) 'B4'自用訂單(贈品)
    *  2013/09/14 Arvin 增加抓PORDER.BOMEMO2帳務備註欄位
    *  2013/11/25 Arvin 判斷非企業DM產品才列入筆數限制
    *  2013/12/03 Arvin 增加判斷若XML回傳完全空白則重複傳送，若傳送10次還是沒過寄發訂單主機異常通知，排除單次連線中斷就認定訂單開立失敗的因素
    *  2013/12/04 Arvin 外包桌曆增加包裝紙盒資訊
    *  2013/12/01 Arvin 增加顯示明信片不要藍色封套訊息帶入XML EXTRA_SHOW額外資訊讓PACKING_KIND=Z 去顯示在三合一發票備註
    *  2014/01/03 Arvin 增加貼紙心型刀模檔案名稱
    *  2014/01/13 Arvin 曾加桌曆底板燙金處理
    *  2014/02/26 Arvin 修改明信片不要藍色封套的資訊，改放到MEMO2，避免同時間有買包裝盒又不要藍色封套造成判斷錯誤
    *  2014/04/07 Arvin 燙金加工PPID=745為喜帖與名片共用，在轉檔清冊顯示加工要增加判斷甚麼產品，才不會再次發生名片出現喜帖的燙金訊息
    *  2014/05/07 Arvin 增加記錄拼圖的加工讓xml去判斷要帶甚麼料號
    *  2014/05/23 Arvin 3D公仔處理並增加get_3Dobjid func 抓取3D公仔物件ID
    *  2014/06/17 Arvin 搜尋處理訂單增加判斷PORDEREG.FLAGPO=N時也不再抓出來，避免當前一次大量轉單時下一次轉單又抓出來處理
    *  2014/07/15 Arvin 增加特定帳號訂單業務歸屬額外處理
    *  2014/07/21 Arvin 增加吸水杯墊腳架加工條件設定
    *  2014/08/25 Arvin 判斷工研院活動桌曆排除不轉單，並記錄BOID
    *  2014/09/18 Arvin 對色判斷改判斷子群組，避免模版修改ID變化。(PS如果群組ID也會變那就沒擇了)
    *  2014/09/22 Arvin 修改對色function 改為check_template，用來判斷template的相關資訊。增加了復航正背互換的判斷
    *  2014/10/28 Arvin 移除計算約交日公式，讓無BOSHIPTIME的訂單不要轉過
    *  2014/10/30 Arvin 增加企業產品訂單數量轉換
    *  2014/11/13 Arvin VDP處理，增加抓VDP筆數get_vdp_count func
    *  2014/11/27 Arvin 移除筆記本與蝴蝶裝額外帶高單白桐T
    *  2014/12/02 Arvin 增加制式工商日誌處理
    *  2015/03/09 Arvin icash卡處理
		*  2015/03/20 Arvin FTP檔案失敗將檔案存放在本機
		*  2015/04/30 Arvin 基本版外包商轉檔清冊增加訂購單位的資訊，非企業用戶該欄位就是空白的
		*  2015/05/05 Arvin 外商商轉檔清冊單位抓w2product40內的PRTUNIT來顯示
		*  2015/08/24 Arvin 保溫瓶強制轉換成版號211341MZ9P000
		*  2015/09/15 Arvin 富邦識別證版號轉換成200527M005000
		*  2016/02/05 Arvin 配合農曆新年活動，凡購買設計師專區產品送燈籠
		*  2016/02/17 Arvin 複製畫轉檔處理
		*  2016/04/12 Arvin 增加備註說明【富邦識別證、上亮P】
    ***********************************************************************************************/
    class search_data {

        /***********************************************************************************************/
        /*  函式名稱：connect()
        /*  函式參數：
        /*  回傳值  ：
        /*  函式功能：連結資料庫
        /***********************************************************************************************/
        function connect() {
            global $GB_dblk, $ROOT_FOLDER,$GB_BOOKPATH_PDF,$GB_W2PWEB,$GB_W2PPDF,$GB_BOOKPATH;
            /***************************************************************************************
            * 設定根目錄
            ****************************************************************************************/
            $ROOT_FOLDER = dirname(dirname(realpath( __FILE__ ))).DIRECTORY_SEPARATOR;
            /***************************************************************************************
            * 連結資料庫
            ****************************************************************************************/
            include_once($ROOT_FOLDER."./inc/dblk.php");
            include_once($ROOT_FOLDER."./inc/GBVars.php");

        }
        /***********************************************************************************************/
        /*  函式名稱：query($_orderid)
        /*  函式參數：$_orderid  : 陣列 (指定只轉某些訂單)
        /*  回傳值  ：WTP_ARRAY，VOW_ARRAY
        /*  函式功能：查詢出預轉檔的訂單
        /***********************************************************************************************/
        function query($_orderid=array(),$_mode=true) {
            global $GB_dblk, $ROOT_FOLDER,$WTP_ARRAY,$VOW_ARRAY,$Final,$INI_SET,$GB_BOOKPATH;
            /********************************************************************
            * 抓取預計轉檔訂單筆數
            ********************************************************************/
            $SQL = "SELECT G.DBGRANTUSER,A.BOID,F.GROUPID,A.BID,A.BOTYPE,convert(varbinary(max),C.WBTITLE) as WBTITLE, ";
						$SQL.= " A.FACID,C.WBFLOW,B.UID,B.UNAME,convert(varbinary(max),B.UDNAME) as UDNAME,";
            $SQL.= " B.UAID,convert(varbinary(max),B.UADDR) as UADDR,";
						$SQL.= " CASE WHEN C.BTYPE is null THEN '999' ELSE C.BTYPE END as BTYPE,";
						$SQL.= " C.WBPAGES,C.WBOPTION,A.BOTIME,A.BRID,A.BOSEND,A.BONUM,";
						$SQL.= " convert(varbinary(max),G.DBTITLE) as DBTITLE,A.BOSUBPRICE,";
            $SQL.= " A.BOPRICE,A.BOPRICE_SHARE,A.BOPRICEM3,A.BOPRICEDSN,H.DUERP,D.CPBONUS,A.BOCOUPON,D.PREORDER,A.BOPAYDATA,A.BOPAYTYPE,A.BOINVTYPE,A.BOVATNO,A.BOSHIPTIME,A.BOGROUP,";
            $SQL.= " convert(varbinary(max),A.BOINVTITLE) as BOINVTITLE,convert(varbinary(max),A.BORNAME) as BORNAME,A.BOMID,";
            $SQL.= " convert(varbinary(max),A.BORADDR) as BORADDR,A.BORPHONE,convert(varbinary(max),A.BOMEMO) as BOMEMO,convert(varbinary(max),A.BOMEMO2) as BOMEMO2,A.BOPTION,A.PICODE,";
            $SQL.= " E.PDT_ID,E.PTE_NO,E.PDT_GP,E.PDT_NAME,E.UNIT,E.T_UNIT,A.CUSPO ";
            $SQL.= " FROM PORDER A LEFT JOIN PUSER B ON A.UID = B.UID ";
            $SQL.= " LEFT JOIN PORDERGROUP F on A.BOID=F.GBOID ";
            $SQL.= " LEFT JOIN WBOOK C ON C.WBID = A.BID ";
            $SQL.= " LEFT JOIN DSNWBOOK G on (A.BID=G.DBID and G.DBGRANT='12') ";
            $SQL.= " LEFT JOIN DSNPUSER H on G.DBGRANTUSER=H.DUID ";
            $SQL.= " LEFT JOIN WBONUS D on A.BOCOUPON=D.CPID ";
            $SQL.= " LEFT JOIN Product_Master E on A.BID=E.PDT_ID and E.STOP_USE<>'Y' ";
            $SQL.= " WHERE A.BOID NOT IN (Select BOID from PORDEREG where (FLAGPO ='F' or FLAGPO='N' or BOERP is not null)) ";
						if ($_mode) {
								$SQL.= " AND (A.YFPBOID='' OR A.YFPBOID is null ) AND BOSTATUS=12 and ( A.BOVATNO <> '68921101' or A.UID='b4208eadd92ed725d2c997d8c07bd45719df69d8') ";
						} else {
								$SQL.= " AND (A.YFPBOID='' OR A.YFPBOID is null ) AND BOSTATUS=12  ";
						}
            //$SQL.= " AND (A.YFPBOID='' OR A.YFPBOID is null ) AND BOSTATUS=12  ";
            if ($INI_SET[tran_type]=='IN') {
                $SQL.=" AND A.FACID ='10' and C.BTYPE not in('17','20','21','22','23','24','25','26','27','28','29') ";
            } elseif ($INI_SET[tran_type]=='OUT') {
                $SQL.=" AND (A.FACID <> '10' or C.BTYPE in('17','20','21','22','23','24','25','26','27','28','29'))";
            }
            //若有指定訂單號碼則只轉該筆訂單
            if (count($_orderid) > 0) {
                $sub_sql="";
                $SQL.= " AND A.BOID in (";
                foreach ($_orderid as $o_value) {
                    if ($sub_sql=="") {
                        $sub_sql="'".$o_value."'";
                    } else {
                        $sub_sql.=",'".$o_value."'";
                    }
                }
                $SQL.=$sub_sql.") ";
            }
            $SQL.= " ORDER BY A.FACID desc ,C.BTYPE asc,A.BOID asc ";
            $this->Parse = mssql_query($SQL,$GB_dblk);
            $row=0;
            $num=0;
            $now_hour=date("H",time());
            if ($now_hour > '18' and $now_hour < '20') {
                $Max_Num=40;
            } elseif ($now_hour > '22' ) {
                $Max_Num=999;
            } else {
                $Max_Num=40;
            }
            while($result= mssql_fetch_array($this->Parse)) {
                if ($row >=$Max_Num) {
                    break;
                }
                $BOID      = trim($result[BOID]);     //W2P訂單號碼
                $BOTYPE    = trim($result[BOTYPE]);   //W2P訂單號碼類別
                $BID       = trim($result[BID]);
                $TITLE     = trim(iconv('UCS-2LE','UTF-8',$result[WBTITLE])); //品項備註
								$DBTITLE     = trim(iconv('UCS-2LE','UTF-8',$result[DBTITLE])); //設計師作品名稱
                $FACID     = trim($result[FACID]);    //生產工廠編號
                $WBFLOW    = trim($result[WBFLOW]);   //編輯方式 (FTP、Template、編輯器)
                $UID       = trim($result[UID]);      //使用者代號
                $UNAME     = trim($result[UNAME]);    //使用者名稱
                $UDNAME    = trim(iconv('UCS-2LE','UTF-8',$result[UDNAME]));//使用者
                $UAID      = trim($result[UAID]);    //郵遞區號(客戶資料)
                $UADDR     = trim(iconv('BIG5','UTF-8',$result[UADDR]));//使用者住址
                $BTYPE     = trim($result[BTYPE]);    //產品類型
                $WBPAGES   = trim($result[WBPAGES]);  //產品頁數
                $WBOPTION  = trim($result[WBOPTION]);  //產品延伸資訊
                $BOTIME    = trim($result[BOTIME]);   //訂單成立時間
                $BOSHIPTIME= trim($result[BOSHIPTIME]);//約交日
                $BRID      = trim($result[BRID]);     //分店代號
                $BOSEND    = trim($result[BOSEND]);    //遞送方式
                //$BOGROUP   = trim($result[BOGROUP]);   //是否合併寄送(主訂單號碼)
                $GROUPID   = trim($result[GROUPID]);   //是否為購物車訂單
                //$FLOWNEW   = trim($result[FLOWNEW]);   //購物車訂單運費(有值得代表主訂單)
                $BOPRICEDSN= trim($result[BOPRICEDSN]); //設計師分紅金額 //2012/09/10 Arvin 增加抓取設計師代號及分紅金額
                $DUERP     = trim($result[DUERP]);      //設計師客戶代號
								$DBGRANTUSER  = trim($result[DBGRANTUSER]);      //設計師UID
                $BONUM     = trim($result[BONUM]);    //數量
                $BOSUBPRICE= trim($result[BOSUBPRICE]);//單價
                $BOPRICE   = trim($result[BOPRICE]);   //總價
                $BOSHARE   = trim($result[BOPRICE_SHARE]); //拆帳金額
                $BOPRICEM3 = trim($result[BOPRICEM3]); //工廠價
                $CPBONUS   = trim($result[CPBONUS]);   //折扣數
                $BOCOUPON  = trim($result[BOCOUPON]);   //折扣碼
                $PREORDER  = trim($result[PREORDER]);  //預開訂單
                $BOPAYDATA = trim($result[BOPAYDATA]);//刷卡後五碼
                $BOPAYTYPE = trim($result[BOPAYTYPE]);//付款方式
                $BOINVTYPE = trim($result[BOINVTYPE]);//發票形式：2:二聯 3:三聯 21:二聯捐贈 25:電子發票
                $BOVATNO   = trim($result[BOVATNO]);   //統編
                $PICODE    = trim($result[PICODE]);    ///電子發票客戶識別碼
                $BOINVTITLE= trim(iconv('UCS-2LE','UTF-8',$result[BOINVTITLE]));
                $BORNAME   = trim(iconv('UCS-2LE','UTF-8',$result[BORNAME])); //收件者
                $BOMID     = trim($result[BOMID]);    //郵遞區號(訂單)
                $BORADDR   = trim(iconv('UCS-2LE','UTF-8',$result[BORADDR]));//收件地址
                //$BORPHONE  = trim($result[BORPHONE]);  //收件人電話
								$BORPHONE  = trim(iconv('BIG5','UTF-8',$result[BORPHONE])); //收件人電話
                $BOMEMO    = trim(iconv('UCS-2LE','UTF-8',$result[BOMEMO])); //備註
                $BOMEMO2   = trim(iconv('UCS-2LE','UTF-8',$result[BOMEMO2])); //帳務備註
                $BOPTION   = trim($result[BOPTION]);   //訂單延伸資訊
                $PDT_ID    = trim($result[PDT_ID]);    //銷售業產品 ID
                $PTE_NO    = trim($result[PTE_NO]);    //銷售業版號
                $PDT_GP    = trim($result[PDT_GP]);     //銷售業產品群組
                $PDT_NAME  = trim(iconv('BIG5','UTF-8',$result[PDT_NAME]));//銷售業產品名稱
                $UNIT      = trim(iconv('BIG5','UTF-8',$result[UNIT]));    //銷售業產品單位
                $T_UNIT    = trim($result[T_UNIT]);                        //銷售業產品數量轉換
                $CUSPO     = trim($result[CUSPO]);                         //中鋼名片用
                $BMEMO     = $this->findname($BOID,$BTYPE,$WBPAGES);//抓取品項名稱
                $VDP_COUNT = $this->get_vdp_count($BID);
                //企業DM抓BOPTION其他都抓WBOPTION
                if ($BTYPE=='40' or $BTYPE=='42') {
                    $option_array=explode("=",$BOPTION);
                } else {
                    $option_array=explode(",",$WBOPTION);
                }
								if ($FACID==10) {
                    $SAVE_ARY="VOW_ARRAY";
                } else {
                     $SAVE_ARY="WTP_ARRAY";
                }
								if ($DBGRANTUSER!='' and $DBGRANTUSER!=$UID) {
										$BOMEMO2.="送開運紙膠帶";
								}
								
								//2016/02/02 Arvin 活動終止
								// if ($BOPAYTYPE!='4' and $WBFLOW!='3') {
												// if ($GROUPID!='') {
														// $q_sql="Select FLOWNEW,GTOTAL from PORDERGROUP where GROUPID='".$GROUPID."'";
														// $query=mssql_query($q_sql,$GB_dblk);
														// $TOTAL=0;
														// while($q_rs= mssql_fetch_array($query)) {
																// $TOTAL+=intval(trim($q_rs["FLOWNEW"])+trim($q_rs[GTOTAL]));
														// }
														// if ($TOTAL >= 2000) {
																// $BOMEMO2.="送福袋";
														// }
														// ${$SAVE_ARY}[$BOID][TOTAL]=$TOTAL;
												// }	elseif ($BOPRICE >=2000) {
														// $BOMEMO2.="送福袋";
												// }
								// }												
                $DM_PTE_NO="";
               
                switch ($BTYPE) {
                    //企業DM
                    case "40":
                    case "41":
                        $DM_PTE_NO=strtoupper($option_array[1]);
												$rs=$this->get_product_info($DM_PTE_NO);
												${$SAVE_ARY}[$BOID]["PRODUCT_UNIT"]=$rs["PRTUNIT"];//企業產品單位
                        break;
                    //icash  2015/03/09 Arvin 強制轉成版號處理
                    case "81":
                        $PTE_NO='215876M001000';
                        break;
										//保溫瓶  2015/08/24 Arvin 強制轉成版號處理
										case "111":
												$PTE_NO='211341MZ9P000';
												break;
										case "219":
												$PTE_NO='211341MZ9Q000';
												break;
                    //名片
                    case "82":
                    case "83":
                    case "84":
												$bkid="";
                        foreach ($option_array as $tp_value) {
                            $tmp_ary1=explode("=",$tp_value);
                            if (trim($tmp_ary1[0])=='bkid') {
                                $bkid=trim($tmp_ary1[1]);
                            }
                        }
                        $tmp_ary=$this->check_template($bkid);
                        $ck_color  =$tmp_ary['CKCOLOR'];
                        $changeftob=$tmp_ary['CHANGEFTOB'];
                        $change_pte=$tmp_ary['CHANGE_PTE'];
												//判斷自製名片要帶入的版號
												if ($FACID==10) {
														if ($WBPAGES=='2') {
																$PTE_NO='211341MK34000';
														} else {
																$PTE_NO='211341ML33000';
														}
												}
                        switch ($bkid) {
                            case "572cbdfb":
                                $PTE_NO='211341ML43000';
                                break;
														//2015/06/04 Arvin 富邦金此模板要強制加上加工(如果要改外包要確認一下加工條件)
														//2016/04/12 Arvin 增加備註說明【富邦識別證、上亮P】
														case "d5441a57":
																${$SAVE_ARY}[$BOID]["WORKNAME"]=array("3M背膠","裁切","導圓角","富邦識別證","上亮P");
																$PTE_NO='200527M005000';
																break;
                            default:
                                //世界先進用
                                if ($change_pte=="1") {
                                    $PTE_NO='013638M001000';
                                    //自製名片用
                                } 
                                break;
                        }
                        break;
                    case "62":
                        foreach ($option_array as $tp_value) {
                            $tmp_ary1=explode("=",$tp_value);
                            if (strtolower(trim($tmp_ary1[0]))=='dcid') {
                                $cover_kind=trim($tmp_ary1[1]);
                                break;
                            }
                        }
                        switch ($cover_kind) {
                            case 1000:
                                ${$SAVE_ARY}[$BOID]["COVER_KIND"]="Iphone6";
                                break;
                            case 1001:
                                ${$SAVE_ARY}[$BOID]["COVER_KIND"]="Iphone6_plus";
                                break;
                        }
                        break;
                    //貼紙
                    case "74":
                    case "75":
                        foreach ($option_array as $tp_value) {
                            $tmp_ary1=explode("=",$tp_value);
                            if (trim($tmp_ary1[0])=='diecut') {
                                $diecut=trim($tmp_ary1[1]);
                            } elseif (trim($tmp_ary1[0])=='work') {
                                $work  =trim($tmp_ary1[1]);
                            }
                        }
                        $rs=$this->getsize($diecut,$BONUM);
                        //抓出貼紙的尺寸及出紙方向
                        ${$SAVE_ARY}[$BOID]["SIZE"]=$rs["SIZE"];
                        //抓出貼紙的大模張數
                        ${$SAVE_ARY}[$BOID]["PICS"]=$rs["PICS"];
                        switch ($work) {
                            case "766"://右出
                                ${$SAVE_ARY}[$BOID]["OUT"]=' RHOUT';
                                break;
                            case "767"://左出
                                ${$SAVE_ARY}[$BOID]["OUT"]=' LHOUT';
                                break;
                            case "768"://頭出
                                ${$SAVE_ARY}[$BOID]["OUT"]=' HOUT';
                                break;
                            case "769"://尾出
                                ${$SAVE_ARY}[$BOID]["OUT"]=' TOUT';
                                break;
                        }
                        break;
										//紙膠帶
										case 76:
												 foreach ($option_array as $tp_value) {
                            $tmp_ary1=explode("=",$tp_value);
                            if (strtolower(trim($tmp_ary1[0]))=='kind') {
																${$SAVE_ARY}[$BOID]["COVER_KIND"]=trim($tmp_ary1[1])." mm";
                                break;
                            }
                        }
												break;
                    //複寫聯單
                    case ($BTYPE>='170' and $BTYPE<='177'):
                        foreach((array)$option_array as $tmpvalue)  {
                            $raw2   = explode('=', $tmpvalue);
                            $key    = trim(strtolower($raw2[0]));
                            $value  = trim($raw2[1]);
                            if($key!='')  $TMP_ARRAY[$key]= $value;
                        }
                        $t_array=explode(":",$TMP_ARRAY["1"]);
                        if ($t_array[2]!='') {
                            ${$SAVE_ARY}[$BOID]["POINT1"]="X:".$t_array[0]." Y:".$t_array[1]." NO:".$t_array[2];
                        }
                        $t_array=explode(":",$TMP_ARRAY["2"]);
                        if ($t_array[2]!='') {
                            ${$SAVE_ARY}[$BOID]["POINT2"]="X:".$t_array[0]." Y:".$t_array[1]." NO:".$t_array[2];
                        }
                        break;
                    case '220'://3D公仔
                        $objid=$this->get_3Dbojid($BID);
                        break;
                    //制式工商日誌
                    case '270':
                    case '271':
                    case '272':
                        foreach ($option_array as $tp_value) {
                            $tmp_ary1=explode("=",$tp_value);
                            switch (strtolower(trim($tmp_ary1[0]))) {
                                case "hole"://打孔方式
                                    $hole=trim($tmp_ary1[1]);
                                    break;
                                case "position"://燙金/銀位置
                                    $gold_position=trim($tmp_ary1[1]);
                                    break;
                                case "kind"://燙金/銀
                                    $gold_kind=trim($tmp_ary1[1]);
                                    if ($gold_kind!='') {
                                        ${$SAVE_ARY}[$BOID][GOLD]="Y";
                                        if ($gold_kind=='S') {
                                            ${$SAVE_ARY}[$BOID]["GOLDMSG"]="燙銀";
                                        } elseif ($gold_kind=='G') {
                                            ${$SAVE_ARY}[$BOID]["GOLDMSG"]="燙金";
                                        }
                                    }
                                    break;
                            }
                        }
                        break;
										//畫鐘
										case ($BTYPE>='274' and $BTYPE<='283'): 
												foreach ($option_array as $tp_value) {
                            $tmp_ary1=explode("=",$tp_value);
                            if (trim($tmp_ary1[0])=='hand') {
                                $hand=trim($tmp_ary1[1]);
																break;
                            }
                        }
												if ($hand=="white") {
														${$SAVE_ARY}[$BOID]["WORKNAME"][]="指針顏色銀色";
												} else {
														${$SAVE_ARY}[$BOID]["WORKNAME"][]="指針顏色黑色";
												}
												if ($BTYPE==274 or $BTYPE==275) {
														${$SAVE_ARY}[$BOID]["WORKNAME"][]="15短針";
												} else {
														${$SAVE_ARY}[$BOID]["WORKNAME"][]="標準指針";
												}
												break;
                }
                ${$SAVE_ARY}[$BOID][BID]=$BID;
                ${$SAVE_ARY}[$BOID][BOMID]=$BOMID;
                ${$SAVE_ARY}[$BOID][UAID]=$UAID;
                ${$SAVE_ARY}[$BOID][FACID]=$FACID;
                ${$SAVE_ARY}[$BOID][BOTIME]=$BOTIME;
                ${$SAVE_ARY}[$BOID][BOSHIPTIME]=$BOSHIPTIME;
                ${$SAVE_ARY}[$BOID][BOPAYTYPE]=$BOPAYTYPE;
                ${$SAVE_ARY}[$BOID][BOINVTYPE]=$BOINVTYPE;
                ${$SAVE_ARY}[$BOID][BOPAYDATA]=$BOPAYDATA;
                ${$SAVE_ARY}[$BOID][BONUM]=$BONUM;
                ${$SAVE_ARY}[$BOID][WBFLOW]=$WBFLOW;
                ${$SAVE_ARY}[$BOID][BORNAME]=$BORNAME;
                ${$SAVE_ARY}[$BOID][BOINVTITLE]=$BOINVTITLE;
                ${$SAVE_ARY}[$BOID][UID]=$UID;
                ${$SAVE_ARY}[$BOID][UNAME]=$UNAME;
                ${$SAVE_ARY}[$BOID][UDNAME]=$UDNAME;
                ${$SAVE_ARY}[$BOID][UADDR]=$UADDR;
                ${$SAVE_ARY}[$BOID][BORADDR]=$BORADDR;
                ${$SAVE_ARY}[$BOID][BMEMO]=$BMEMO;
                ${$SAVE_ARY}[$BOID][BTYPE]=$BTYPE;
                ${$SAVE_ARY}[$BOID][BOTYPE]=$BOTYPE;
                ${$SAVE_ARY}[$BOID][WBPAGES]=$WBPAGES;
                ${$SAVE_ARY}[$BOID][BORPHONE]=$BORPHONE;
                ${$SAVE_ARY}[$BOID][BOVATNO]=$BOVATNO;
                ${$SAVE_ARY}[$BOID][BOSUBPRICE]=$BOSUBPRICE;
                ${$SAVE_ARY}[$BOID][BOPRICEM3] =$BOPRICEM3;
                ${$SAVE_ARY}[$BOID][BOPRICE] =$BOPRICE;
                ${$SAVE_ARY}[$BOID][BOSHARE] =$BOSHARE;
                ${$SAVE_ARY}[$BOID][BOSEND]=$BOSEND;
                ${$SAVE_ARY}[$BOID][CUSPO]=$CUSPO;
                ${$SAVE_ARY}[$BOID][GROUPID]=$GROUPID;
                ${$SAVE_ARY}[$BOID][BOPRICEDSN]=$BOPRICEDSN;
                ${$SAVE_ARY}[$BOID][DUERP]=$DUERP;
                ${$SAVE_ARY}[$BOID][BOMEMO]=$BOMEMO;
                ${$SAVE_ARY}[$BOID][BOMEMO2]=$BOMEMO2;
                ${$SAVE_ARY}[$BOID][WBOPTION]=$WBOPTION; //T-shirt 用
                ${$SAVE_ARY}[$BOID][BOPTION]=$BOPTION;
                ${$SAVE_ARY}[$BOID][PDT_ID]=$PDT_ID;
                ${$SAVE_ARY}[$BOID][PTE_NO]=$PTE_NO;
                ${$SAVE_ARY}[$BOID][DM_PTE_NO]=$DM_PTE_NO; //DM、帳單版號
                ${$SAVE_ARY}[$BOID][PDT_NAME]=$PDT_NAME;
                ${$SAVE_ARY}[$BOID][UNIT]=$UNIT;
                ${$SAVE_ARY}[$BOID][PDT_GP]=$PDT_GP;
                ${$SAVE_ARY}[$BOID][PREORDER]=$PREORDER;
                ${$SAVE_ARY}[$BOID][CPBONUS]=$CPBONUS;
                ${$SAVE_ARY}[$BOID][BOCOUPON]=$BOCOUPON;
                ${$SAVE_ARY}[$BOID][PICODE]=$PICODE;
                ${$SAVE_ARY}[$BOID][T_UNIT]=$T_UNIT;
                ${$SAVE_ARY}[$BOID][CK_COLOR]=$ck_color;
                ${$SAVE_ARY}[$BOID][CHANGEFTOB]=$changeftob;
                ${$SAVE_ARY}[$BOID][CHANGE_PTE]=$change_pte;
                ${$SAVE_ARY}[$BOID][OBJID]=$objid;
                ${$SAVE_ARY}[$BOID][TITLE] =$TITLE;
								${$SAVE_ARY}[$BOID][DBTITLE] =$DBTITLE;
                ${$SAVE_ARY}[$BOID][VDP_COUNT] =$VDP_COUNT;
                ${$SAVE_ARY}[$BOID][hole]=$hole;
                ${$SAVE_ARY}[$BOID][gold_position]=$gold_position;
                ${$SAVE_ARY}[$BOID][gold_kind]=$gold_kind;
                $Final[$SAVE_ARY][]=$BOID;
                
                //2013/11/25 Arvin 判斷非企業DM產品才列入筆數限制
                if ($BTYPE!=40) {
                    $row++;
                }
                $result_data[BOID][]=$BOID;
                $mail_content = $mail_content.$result[BOID]."\r\n";
                $num++;
            }
            $result_data[NUM_ROW]=$num;
            $result_data[mail_content]=$mail_content;
            /***************************************************************************************
            * 實際轉置PDF檔案
            ****************************************************************************************/
            if (is_array($result_data[BOID])) {
                $SQL ='select PORDER.BOID,PORDER.FACID,WBOOK.BTYPE,W2POP.ITEM,W2POP.V1,convert(varbinary(max), W2POP.V2) as V2,W2POP.V3,W2POP.V4,W2POP.V6,';
                $SQL.=' W2PP.PPTYPE,W2PP.PPNAME,w2productaddr.adrman,w2productaddr.adrmid,w2productaddr.adradr,w2productaddr.adrtel from PORDER ';
                $SQL.=' join WBOOK on PORDER.BID=WBOOK.WBID ';
                $SQL.=' join W2POP on PORDER.BOID=W2POP.BOID ';
                $SQL.=' left join W2PP on W2POP.V1=W2PP.PPID ';
                $SQL.=' left join w2productaddr on W2POP.V1=w2productaddr.adrpt ';
                $SQL.=' where W2POP.ITEM <>\'BKID\' and W2POP.ITEM <>\'GROUP\' and W2POP.ITEM <>\'GNAME\' ';
                $sub_sql="";
                $SQL.= " AND PORDER.BOID in (";
                foreach ($result_data[BOID] as $f_value) {
                    if ($sub_sql=="") {
                        $sub_sql="'".$f_value."'";
                    } else {
                        $sub_sql.=",'".$f_value."'";
                    }
                }
                $SQL.=$sub_sql.") ";
                $SQL.=" order by PORDER.BOID";
                $QUERYSQL = mssql_query($SQL,$GB_dblk);
                while($result= mssql_fetch_array($QUERYSQL)) {
                    $PPTYPE=trim($result[PPTYPE]);
                    $BTYPE =trim($result[BTYPE]);
                    $FACID =trim($result[FACID]);
                    $VAR1  =trim($result[V1]);
                    $VAR2  =trim(iconv('UCS-2LE', 'UTF-8', $result[V2]));
                    $VAR3  =trim($result[V3]);
                    $VAR4  =trim($result[V4]);
                    $ITEM  =trim($result[ITEM]);
                    $BOID  =trim($result[BOID]);
                    $ADRMAN=trim(iconv('BIG5','UTF-8',$result[adrman]));//送貨點收件人
                    $ADRADR=trim(iconv('BIG5','UTF-8',$result[adradr]));//送貨點地址
                    $ADRMID =trim($result[adrmid]);//送貨點郵遞區號
                    $ADRTEL =trim($result[adrtel]);//送貨點電話
                    $ADRCOUNT=trim($result[V6]);//送貨點數量

                    if ($FACID==10) {
                        $SAVE_ARY="VOW_ARRAY";
                    } else {
                        $SAVE_ARY="WTP_ARRAY";
                    }
                    if ($PPTYPE=='1' and $ITEM=='PPID') {
                        ${$SAVE_ARY}[$BOID][PPID]  = $VAR1;
                        ${$SAVE_ARY}[$BOID][PPNAME]= trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                    } elseif ($PPTYPE=='2') {
                        switch ($VAR1) {
                            case '702':
                                ${$SAVE_ARY}[$BOID]["CUT"]="導五號圓角";
                                ${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                break;
                            //桌曆底板燙金
                            case '743':
                                ${$SAVE_ARY}[$BOID]["GOLD"]="Y";
                                ${$SAVE_ARY}[$BOID]["WORKNAME"][]= "桌曆底版燙金";
                                ${$SAVE_ARY}[$BOID]["GOLDMSG"]="桌曆底版燙金";
                                break;
                            //桌曆底板燙銀
                            case '744':
																if ($BTYPE!='83' and $BTYPE!='84' and $BTYPE!='82') {
																		${$SAVE_ARY}[$BOID]["GOLD"]="Y";
																		${$SAVE_ARY}[$BOID]["WORKNAME"][]= "桌曆底版燙銀";
																		${$SAVE_ARY}[$BOID]["GOLDMSG"]="桌曆底版燙銀";
																} else {
																		${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
																}
                                break;
                            case '745':
                                //喜帖燙金選項
                                if ($BTYPE>='124' and $BTYPE<='129') {
                                    ${$SAVE_ARY}[$BOID]["GOLD"]="Y";
                                    ${$SAVE_ARY}[$BOID]["GOLDMSG"]="喜帖信封燙金";
                                    $back_file=$GB_BOOKPATH.$BID."/black.txt";
                                    $handle = fopen($back_file, "r");
                                    $contents = '';
                                    if ($handle) {
                                        while (!feof($handle)) {
                                            $contents = fgets($handle, 4096);
                                            $rs[]=$contents;
                                        }
                                        fclose($handle);
                                        $font_name="";
                                        switch (strtolower(trim($rs[5]))) {
                                            case "min":
                                                $font_name='(細明體)';
                                                break;
                                            case "arial":
                                                $font_name='(黑體)';
                                                break;
                                            case "kaiu":
                                                $font_name='(標楷體)';
                                                break;
                                        }
                                    }
                                    ${$SAVE_ARY}[$BOID][WORKNAME][]= "信封發依您印".trim(iconv('BIG5','UTF-8',$result[PPNAME])).$font_name; //加工條件
                                } else {
                                //名片燙金
                                    ${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                }
                                break;
                            case ($VAR1 > '800' and $VAR1 < '806'):
                            case '780':
                            case '781':
                                ${$SAVE_ARY}[$BOID][BIND]= $VAR1; //作品集、筆記本裝訂方式
                                break;
                            case ($VAR1 > '809' and $VAR1 <= '815'):
                                ${$SAVE_ARY}[$BOID][TEXT]= trim(iconv('BIG5','UTF-8',$result[PPNAME])); //筆記本內頁格式
                                break;
                            case '750':
                            case '753':
                            case '754':
                            case '876': //吸水杯墊腳架
                                ${$SAVE_ARY}[$BOID][EXTRA]= $VAR1;
                                if ($VAR2!='') {
                                    ${$SAVE_ARY}[$BOID][WORKNAME][]= trim(iconv('BIG5','UTF-8',$result[PPNAME])).$VAR2."個"; //包裝紙盒
                                    ${$SAVE_ARY}[$BOID][EXTRA_NUM]=$VAR2; //額外訂單要用的
                                    ${$SAVE_ARY}[$BOID][BOX]      =$VAR2;
                                } else {
                                    ${$SAVE_ARY}[$BOID][WORKNAME][]= trim(iconv('BIG5','UTF-8',$result[PPNAME])).${$SAVE_ARY}[$BOID][BONUM]."個"; //包裝紙盒
                                    ${$SAVE_ARY}[$BOID][EXTRA_NUM]=${$SAVE_ARY}[$BOID][BONUM];
                                    ${$SAVE_ARY}[$BOID][BOX]      =${$SAVE_ARY}[$BOID][BONUM];
                                }
                                break;
                            case '759':
                                ${$SAVE_ARY}[$BOID]["WORKNAME1"][]='P';
                                ${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                break;
                            case '785':
                                ${$SAVE_ARY}[$BOID]["SIZE"]=${$SAVE_ARY}[$BOID]["SIZE"].'R';
                                ${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                break;
                                //拼圖
                            case '790':
                            case '791':
                            case '792':
                            case '793':
                                if ($VAR1=='790' or $VAR1=='791') {
                                    ${$SAVE_ARY}[$BOID]["PREVIEW"]="Y";
                                }
                                ${$SAVE_ARY}[$BOID][EXTRA_SHOW]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                ${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                break;
                            case '817':
                                ${$SAVE_ARY}[$BOID][WORKNAME][]= trim(iconv('BIG5','UTF-8',$result[PPNAME])).",".${$SAVE_ARY}[$BOID][POINT1];
                                break;
                            case '818':
                                ${$SAVE_ARY}[$BOID][WORKNAME][]= trim(iconv('BIG5','UTF-8',$result[PPNAME])).",".${$SAVE_ARY}[$BOID][POINT2];
                                break;
                            case '846':
                                switch ($BTYPE) {
                                    case '125':
                                    case '126':
                                        $SHOW_PPNAME="20x14cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F30621';
                                        break;
                                    case '127':
                                        $SHOW_PPNAME="16.5x16.5cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F30921';
                                        break;
                                    case '128':
                                    case '129':
                                        $SHOW_PPNAME="23x11.5cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F30281';
                                        break;
                                }
                                ${$SAVE_ARY}[$BOID][WORKNAME][]= $SHOW_PPNAME; //加工條件
                                break;
                            case '847'://粉紅
                                switch ($BTYPE) {
                                    case '125':
                                    case '126':
                                        $SHOW_PPNAME="20x14cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F3067';
                                        break;
                                    case '127':
                                        $SHOW_PPNAME="16.5x16.5cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F3097';
                                        break;
                                    case '128':
                                    case '129':
                                        $SHOW_PPNAME="23x11.5cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F3028';
                                        break;
                                }
                                ${$SAVE_ARY}[$BOID][WORKNAME][]= $SHOW_PPNAME; //加工條件
                                break;
                            case '848':
                                switch ($BTYPE) {
                                    case '125':
                                    case '126':
                                        $SHOW_PPNAME="20x14cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F3065';
                                        break;
                                    case '127':
                                        $SHOW_PPNAME="16.5x16.5cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'F3095';
                                        break;
                                    case '128':
                                    case '129':
                                        $SHOW_PPNAME=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                        break;
                                }
                                ${$SAVE_ARY}[$BOID][WORKNAME][]= $SHOW_PPNAME; //加工條件
                                break;
                            case '860'://紅-明信片喜帖專用
                                case '124':
                                    $SHOW_PPNAME="16x12.3cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'A4102';
                                    ${$SAVE_ARY}[$BOID][WORKNAME][]= $SHOW_PPNAME; //加工條件
                                    break;
                            case '861'://粉紅-明信片喜帖專用
                                case '124':
                                    $SHOW_PPNAME="16x12.3cm".trim(iconv('BIG5','UTF-8',$result[PPNAME])).'A4102';
                                    ${$SAVE_ARY}[$BOID][WORKNAME][]= $SHOW_PPNAME; //加工條件
                                    break;
                            case '874': //不要藍色封套(明信片)
                                ${$SAVE_ARY}[$BOID][EXTRA_SHOW]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                ${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                break;
                            default:
                                ${$SAVE_ARY}[$BOID][WORKNAME][]=trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                                break;
                        }
                    //自製名片處理
                    } elseif ($ITEM=='NAME') {
                        if ($VAR1 > 0) {
                            ${$SAVE_ARY}[$BOID][CNUM][]     = $VAR1;  //實際訂購量
                            ${$SAVE_ARY}[$BOID][CNAME][]    = $VAR2;  //名片人名
                            ${$SAVE_ARY}[$BOID][CFILE][]    = $VAR3;  //對應檔案名稱
                            ${$SAVE_ARY}[$BOID][CMINNUM][]  = $VAR4;  //最低訂購量
                        }
                    //銷售業多送貨點
                    } elseif ($ITEM=='SEND') {
                        if ($VAR1 > 0) {
                            ${$SAVE_ARY}[$BOID][POINT][]=$VAR1;
                            ${$SAVE_ARY}[$BOID][ADRMAN][]=$ADRMAN;
                            ${$SAVE_ARY}[$BOID][ADRMID][]=$ADRMID;
                            ${$SAVE_ARY}[$BOID][ADRADR][]=$ADRADR;
                            ${$SAVE_ARY}[$BOID][ADRTEL][]=$ADRTEL;
                            ${$SAVE_ARY}[$BOID][ADRCOUNT][]=$ADRCOUNT;
                        }
                    } elseif (strtoupper($ITEM)=='COLOR') {
                            ${$SAVE_ARY}[$BOID]["COLOR"]=$VAR1;
														${$SAVE_ARY}[$BOID]["PAINTBOX"]=$VAR2;//複製畫外框資訊
                    }
                    //少量名片額外處理
                    if ($BTYPE=='82') {
                        ${$SAVE_ARY}[$BOID][CNUM][]     = ${$SAVE_ARY}[$BOID][BONUM];  //實際訂購量
                        ${$SAVE_ARY}[$BOID][CFILE][]    = '001';  //對應檔案名稱
                        ${$SAVE_ARY}[$BOID][CMINNUM][]  = ${$SAVE_ARY}[$BOID][BONUM];  //最低訂購量
                    }
                    //2012/07/06 Arvin 目前編輯器編輯的外包產品不存ITEM=NAME的資料，抓取資料時做轉換
                    if ($FACID!=10 and $BTYPE!=83 and $BTYPE!=84 and count(${$SAVE_ARY}[$BOID][CNUM]) < 1) {
                        ${$SAVE_ARY}[$BOID][CNUM] = array(''.${$SAVE_ARY}[$BOID][BONUM].'');
                        switch ($BTYPE) {
														case ($BTYPE>=85 and $BTYPE<=102):
														case ($BTYPE>=130 and $BTYPE<=139):
														case ($BTYPE>=170 and $BTYPE<=177):
														case ($BTYPE>=124 and $BTYPE<=129):
														case ($BTYPE>=274 and $BTYPE<=283):
                            case 46: case 109:case 110: case 108: case 106: case 166:case 76:case 105:
														case 167:case 168:
                                ${$SAVE_ARY}[$BOID][CFILE]=array('001');
                                break;
                            default:
                                ${$SAVE_ARY}[$BOID][CFILE]=array('body');
                                break;
                        }
                    }
                }
            }
            return $result_data;
        }
        /***********************************************************************************************/
        /*  函式名稱：ini_set()
        /*  函式參數：
        /*  回傳值  ：
        /*  函式功能：系統值設定
        /***********************************************************************************************/
        function ini_set() {
            global $ROOT_FOLDER,$thislog,$GB_BOOKPATH_PDF,$DIR_NAME,$TRANS_DATE,$DATETIME,$GB_BOOKPATH;

            $DIR_NAME=date("Ymd");
            $TRANS_DATE=time();
            $DATETIME = date("YmdHis",$TRANS_DATE);

            $ini_file   = $ROOT_FOLDER.'./transfer/w2p_trans.ini.php';

            $LOG_PATH=$ROOT_FOLDER.'./transfer/log/'.$DIR_NAME;
            if (!is_dir($LOG_PATH)) {
                mkdir($LOG_PATH);
            }
            $thislog     = $ROOT_FOLDER."./transfer/log/".$DIR_NAME."/LOG".$DIR_NAME.".log";

            if(file_exists($ini_file)){
                $ini_array = parse_ini_file($ini_file, true);
            } else{
                return  "找不到系統參數檔！ -> ".$ini_file;
                die;
            }
            /********************************************************************
             * 設定
             ********************************************************************/
            $INI[master]           = trim($ini_array[system][main]);       // 系統主要收件者
            $INI[MailCato]         = trim($ini_array[system][email]);      // 收件者
            $INI[cc]               = trim($ini_array[system][cc]);         // 副本收件者
            $INI[SEND_MAIL]        = trim($ini_array[system][sendmail]);   // 寄送Mail開關
            $INI[namecard]         = trim($ini_array[system][namecard]);   // 難字通知收件者
            $INI[ERROR]            = trim($ini_array[system][error]);      // 異常訂單收件者
            $INI[BCC]              = trim($ini_array[system][bcc]);        // 生產工廠密件收件者
            $INI[tran_type]        = trim($ini_array[system][tran_type]);  // 轉檔類別：外包或自製
            /********************************************************************
             * WTP FTP上傳檔案開關
             ********************************************************************/
            $INI[FTP]             = trim($ini_array[ftp][transfer]);      // ftp upload  Y or N
            $INI[DEL_FILE]        = trim($ini_array[ftp][DEL_FILE]);      // 有錯誤是否刪除FTP上檔案
            /********************************************************************
             * Biz talk 設定
             ********************************************************************/
            $INI[TRANS_XML]        = trim($ini_array[biz][transfer]); //xml transfer  Y or N
            $INI[mode]             = trim($ini_array[biz][mode]);     //xml transfer mode
            $INI[xmlhost]          = trim($ini_array[biz][host]);    // biz talk server address
            $INI[xmlport]          = trim($ini_array[biz][port]);    // biz talk host
            $INI[xmltimeout]       = trim($ini_array[biz][timeout]);
            $INI[xmlurl]           = trim($ini_array[biz][url]);      // biz talk url

            return $INI;
        }
				 /***********************************************************************************************/
        /*  函式名稱：save_ftp_file($_facid,$_source,$_filename)
        /*  函式參數： $_facid   : 生產單位代號，用來判斷後續檔案要丟去哪
        /*                 $_source : 原始檔案
				/*                 $_filename:實際給廠商的檔名
        /*  回傳值  ：true/false
        /*  函式功能：將原本要傳給生產工廠的檔案，因為網路或其他原因無法將檔案上傳時存放在本機
				/*                方便後續手動將檔案丟到對應的工廠FTP上
        /***********************************************************************************************/
        function save_ftp_file($_facid,$_source,$_filename) {
            global $INI_SET,$thislog,$DIR_NAME;
						$save_root_path="F:\\FTPmiss\\";
						//判斷廠商的資料夾是否存在，不在則建立
						if (!is_dir($save_root_path.$_facid)) {
								@mkdir($save_root_path.$_facid);
						}
						if (!is_dir($save_root_path.$_facid."\\".$DIR_NAME)) {
								@mkdir($save_root_path.$_facid."\\".$DIR_NAME);
						}
						$real_file=$save_root_path.$_facid."\\".$DIR_NAME."\\".$_filename;
						if (!copy($_source,iconv("UTF-8","BIG5",$real_file))) {
								$this->ll_echo("[".$this->show_time()."]備份FTP上傳失敗檔案於本機失敗!!");
								$this->send_mail("備份FTP上傳失敗檔案於本機失敗!!");
						}
        }
        /***********************************************************************************************/
        /*  函式名稱：send_mail($_title=,$_content)
        /*  函式參數： $_title   : 信件標題
        /*             $_content : 信件內容
        /*  回傳值  ：
        /*  函式功能：Email通知信寄發
        /***********************************************************************************************/
        function send_mail($_title="",$_content="",$mode=1) {
            global $thislog,$INI_SET;
            $mail_title       = "『".$_title."』";
            $MailHeader = "MIME-Version: 1.0\r\n
            Content-type: text/html; charset=UTF-8\r\n
            From: WebMaster@cloudw2p.com\r\n
            Reply-To: WebMaster@cloudw2p.com\r\n
            X-Priority: 1\r\n
            X-MSMail-Priority: High\r\n
            X-Mailer: PHP/".phpversion()."\r\n";

            $HeaderFirst = "=?UTF-8?B?" . base64_encode($mail_title) . "?=";
            $this->ll_echo("[".$this->show_time()."]寄送".$_title);

            if ($mode=="1") {
                $AX = mail($INI_SET[cc],$HeaderFirst,$_content,$MailHeader);
            } elseif ($mode=="2") {
                $AX = mail($INI_SET[namecard],$HeaderFirst,$_content,$MailHeader);
            } elseif ($mode=="3") {
                $AX = mail($INI_SET[MailCato],$HeaderFirst,$_content,$MailHeader);
            } elseif ($mode=="4") {
                $AX = mail($INI_SET[ERROR],$HeaderFirst,$_content,$MailHeader);
            } else {
                $AX = mail($INI_SET[master],$HeaderFirst,$_content,$MailHeader);
            }
        }
        /***********************************************************************************************/
        /*  函式名稱：make_xml($_ARRAY,$_BOID,$_STEP)
        /*  函式參數： $_ARRAY   : WTP_ARRAY or VOW_ARRAY 資料陣列
        /*             $_BOID    : 訂單號碼
        /*             $_STEP    : XML 流水號
        /*  回傳值  ： 產生XML 檔案
        /*  函式功能：製做XML檔案
        /*
        /*  零售業抓取FACID的部分如有變更要一併修正 W2P_ARRAY (PDT_GP)
        /***********************************************************************************************/
        function make_xml($_ARRAY=array(),$_BOID,$_STEP) {
            global $GB_dblk,$ROOT_FOLDER,$DATETIME,$INI_SET,$TRANS_DATE,$DIR_NAME,$thislog,$VOW_ARRAY,$tax_check;

            $T_BONUM=$_ARRAY[$_BOID][BONUM];
            $SHOW_UNIT="";

            $search = array ("'","\"","<",">","&");
            $replace = array ("’","＂","＜","＞","＆");
            /***************************************************************************************
            * 建立XML目錄
            ****************************************************************************************/
            $path=$ROOT_FOLDER.'./transfer/xml/'.$DIR_NAME;
            if (!is_dir($path)) {
                mkdir($path);
            }
            $filepath=$ROOT_FOLDER.'./transfer/xml/'.$DIR_NAME.'/'.$_BOID.'.xml';
            $xml_file=fopen($filepath,w);
            /***************************************************************************************
            * 組合 XML 內 PTECODE資料
            ****************************************************************************************/
            $PDT_KD='';
            $SQL = 'Select * from w2pop join w2pp on w2pop.ppid=w2pp.ppid where boid=\''.$_BOID.'\' order by w2pop.ppid';
            $query = mssql_query($SQL,$GB_dblk);
            $process='';
						$_ARRAY[$_BOID][PUZZLE_BOX]=array();
            while ($tmp_result   = mssql_fetch_array($query)) {
                if (trim($tmp_result['PPTYPE'])=='1') {
                    $paper_id=$tmp_result['PPID'];  //紙別對應
                } else {
                    $X_PPID = $tmp_result['PPID'];
                    //拼圖木框料號轉換
                    if ($X_PPID=='793') {
                        switch ($_ARRAY[$_BOID][BTYPE]) {
                            case 117:case 118:
                                $X_PPID='793A5';
                                break;
                            case 119:case 120:
                                $X_PPID='793A4';
                                break;
                            case 121:case 122:
                                $X_PPID='793A3';
                                break;
                        }
                    }
                    $_ARRAY[$_BOID][PUZZLE_BOX][]=$X_PPID;
                    //2012/10/11 拼圖木盒要多帶積木紙盒的料號進去
                    if ($X_PPID=='790' or $X_PPID=='791') {
                        $X_PPID=$X_PPID."_790A";
                        $_ARRAY[$_BOID][PUZZLE_BOX][]='790A';
                    }
                    if ($process=='') {
                        $process  = $X_PPID;
                    } else {
                        $process .= '_'.$X_PPID;
                    }
                }
            }
            /***************************************************************************************
            * 抓封面紙別
            ****************************************************************************************/
            $papercover_id="";
            $tmp_string=$_ARRAY[$_BOID][BOPTION];
            $tmp_ary=explode(",",$tmp_string);
            foreach ($tmp_ary as $val) {
                $tmp_ary1=explode("=",$val);
                if ($tmp_ary1[0]=="papercover") {
                    $papercover_id=trim($tmp_ary1[1]);
                    break;
                }
            }
            switch ($_ARRAY[$_BOID][BTYPE]) {
                case "17":case "22":case "26":case "27":case "28":
                    //A4直,A5直橫,21,B5直平裝封面Indigo印，作品集產品若找不到封面紙別，預設帶123
                    //2012/02/06 Arvin 判斷平裝A4直式、B5直式、A5直式、A5橫式、21正方時才帶入料號
                    //2012/08/22 Arvin A4直,A5直橫,21,B5直平裝封面Indigo印的料號由123(特銅250) 變更為112(銅西250P)
                    if ($_ARRAY[$_BOID][BIND]=='801') {
                        $papercover_id='112';
                    } else {
                        $papercover_id='';
                    }
                    break;
                // 2012/09/04 Arvin 移除，改用xml YFP 的tag 控制
                // 2012/10/03 Arvin 桌掛曆因產品歸類問題需要區分自製外包版號，所以修改800代號為外包的桌掛曆就多帶800過去產生
                //判斷有自製及外包可能的產品，若該筆訂單是外包在@後面多增加800這個代號給MIS
                case "31":case "32":case "38":case "39":
                    if ($_ARRAY[$_BOID][FACID]!='10') {
                        $papercover_id='800';
                    }
                    break;
                //2012/10/12 Arvin 便利貼要多帶封面料號過去
                case '206': //小平裝便利貼
                case '207': //大平裝便利貼
                case '209': //五色便利貼
                    $papercover_id='125';
                    break;
                case '208': //精裝便利貼
                    $papercover_id='198';
                    break;
                   //2012/05/28 大圖噴的不帶封面料號，直接給MIS那邊處理
                default:
                    $papercover_id='';
                    break;
            }
            //2012/07/02 Arvin 判斷蝴蝶裝產品在轉檔時多帶127紙別過去開訂單
            //2013/05/07 筆記本加厚板要額外帶127 高單白銅T到訂單裡面
            //2014/11/27 Arvin 移除筆記本與蝴蝶裝額外帶高單白桐T
            //if ($_ARRAY[$_BOID][BIND]=='804' or $_ARRAY[$_BOID][BIND]=='805' or $_ARRAY[$_BOID][BTYPE]=='35' or $_ARRAY[$_BOID][BTYPE]=='36') {
            //    $papercover_id='127';
            //}
            if ($papercover_id!='') {
                if ($process=='') {
                    $process  = $papercover_id;
                } else {
                    $process .= '_'.$papercover_id;
                }
            }
            $single_double=$_ARRAY[$_BOID][WBPAGES];
            /***************************************************************************************
            * 零售業用
            ****************************************************************************************/
            if (substr($_BOID,0,1)=='S') {
                $T_BONUM=intval($_ARRAY[$_BOID][T_UNIT]*$_ARRAY[$_BOID][BONUM]);  //銷售業要做數量轉換
            }
            //企業產品判斷是否要單位轉換及顯示單位
            $result_prt=$this->get_product_info($_ARRAY[$_BOID][DM_PTE_NO]);
            if (!empty($result_prt)) {
                switch ($result_prt["PRTUNIT"]) {
                    case "捲":
                        $SHOW_UNIT="RL";
                        break;
                    case "本":
                        $SHOW_UNIT="VL";
                        break;
                    case "張":
                        $SHOW_UNIT="SH";
                        break;
                    case "箱":
                        $SHOW_UNIT="BX";
                        break;
                    case "包":
                        $SHOW_UNIT="PK";
                        break;
                    case "個":
                        $SHOW_UNIT="PC";
                        break;
                    case "份":
                        $SHOW_UNIT="BK";
                        break;
                }
                $T_BONUM=intval($result_prt[PRTRATE]*$_ARRAY[$_BOID][BONUM]);  //數量轉換
            }
            $result_ftp=$this->choose_ftp($_ARRAY[$_BOID][FACID]);
            /***************************************************************************************
            * XML 模板
            ****************************************************************************************/
            if ($_ARRAY[$_BOID][EXTRA]!='' and $_ARRAY[$_BOID][CPBONUS]!='' and $_ARRAY[$_BOID][CPBONUS]==0) {
                //2013/08/20 Arvin 判斷另開訂單時若沒有合併交寄資訊，要塞資料進去讓ERP那邊去做關聯
                if ($_ARRAY[$_BOID][GROUPID]=='') {
                    $_ARRAY[$_BOID][GROUPID]=$_BOID;
                }
            }
            $this->ll_echo("[".$this->show_time()."]$_BOID XML製做");
            include($ROOT_FOLDER.'./transfer/xml_template.php');
            fwrite ($xml_file,$xml);
            fclose ($xml_file);


            //判斷兌換券是否有額外購買包裝盒，若有要另開訂單
            if ($_ARRAY[$_BOID][EXTRA]!='' and $_ARRAY[$_BOID][CPBONUS]!='' and $_ARRAY[$_BOID][CPBONUS]==0) {
                $SQL ='Select A.BRID,A.BPRICE,B.PPNAME from W2POC A join W2PP B on A.PPID=B.PPID where BTYPE=\''.$_ARRAY[$_BOID][BTYPE].'\'';
                $SQL.=' and A.PPID=\''.$_ARRAY[$_BOID][EXTRA].'\' Group by A.BRID,B.PPNAME,A.BPRICE ';
                $query = mssql_query($SQL);
                while($result= mssql_fetch_array($query)) {
                    $BRID =trim($result[BRID]);
                    $PRICE=trim($result[BPRICE]);
                    $PPNAME = trim(iconv('BIG5','UTF-8',$result[PPNAME]));
                    $TMP_ARRAY["$BRID"]=$PRICE;
                    $TMP_ARRAY[PPNAME]=$PPNAME;
                }
                $filepath=$ROOT_FOLDER.'./transfer/xml/'.$DIR_NAME.'/'.$_BOID.'A.xml';
                $extra_xml_file=fopen($filepath,w);
                include($ROOT_FOLDER.'./transfer/extra_xml_template.php');
                fwrite ($extra_xml_file,$xml);
                fclose ($extra_xml_file);
            }

        }
        /***********************************************************************************************/
        /*  函式名稱：trans_xml($_ARRAY,$_BOID)
        /*  函式參數： $_BOID    : 訂單號碼
        /*             $_NAME    : 陣列名稱
        /*             $_TYPE    : XML類型 (M：主要訂單，E：額外生成訂單)
        /*  回傳值  ： 訂單號碼或錯誤訊息
        /*  函式功能：傳送XML
        /***********************************************************************************************/
        function trans_xml ($_BOID,$_NAME,$_TYPE) {
            global $ROOT_FOLDER,$GB_dblk,$insert_time,$VOW_ARRAY,$WTP_ARRAY,$INI_SET,$DIR_NAME;
            if ($_TYPE=='M') {
                $send_path=$ROOT_FOLDER.'./transfer/xml/'.$DIR_NAME.'/'.$_BOID.'.xml';
            } else {
                $send_path=$ROOT_FOLDER.'./transfer/xml/'.$DIR_NAME.'/'.$_BOID.'A.xml';
            }
            $handle =fopen($send_path,"r");
            $xml_stream = fread($handle, filesize($send_path));
            fclose($handle);
            //$biz_result=$this->EB_sendhost($INI_SET[xmlhost], $INI_SET[xmlurl], $xml_stream);
            //$biz_xml=html_entity_decode($biz_result);  //將 return資料中 Tag 轉為標準符號而不是用代碼代替
            /***************************************************************************************
            * 解譯 XML 資料
            ****************************************************************************************/
            $vals=array();
            //$p = xml_parser_create();
            //xml_parse_into_struct($p, $biz_xml, $vals, $index);
            //xml_parser_free($p);
            $xml_trans_count=0;
            while (empty($vals) and $xml_trans_count <10) {
                $biz_result=$this->EB_sendhost($INI_SET[xmlhost], $INI_SET[xmlurl], $xml_stream);
                $biz_xml=html_entity_decode($biz_result);  //將 return資料中 Tag 轉為標準符號而不是用代碼代替
                $p = xml_parser_create();
                xml_parse_into_struct($p, $biz_xml, $vals, $index);
                xml_parser_free($p);
                $xml_trans_count++;
            }
            if (empty($vals)) {
                $this->send_mail("ERP主機無法連線開立訂單",$mail_content,4);
            }
            /***************************************************************************************
            * 將Biz Talk回傳的xml解譯資料存放在Array
            ****************************************************************************************/
            foreach ($vals as $key1 => $value2) {
                foreach ($value2 as $key2 => $value3) {
                    if ($vals[$key1][tag]=='STATUS' or $vals[$key1][tag]=='ORDERS' or $vals[$key1][tag]=='MESSAGE') {
                        $update_array[$vals[$key1][tag]]=$vals[$key1][value];
                    }
                }
            }
            /***************************************************************************************
            * 更新資料庫訂單資料
            ****************************************************************************************/
            if ($update_array[STATUS]=='Y') {
                //主訂單處理
                if ($_TYPE=='M') {
                    $EX_YFPBOID='';
                    $tmp_array=explode('_',$update_array[ORDERS]);
                    if (trim($tmp_array[2])=='') { //外包訂單不會有對內訂單號碼(VB)，所以只存對外(AV or VV)的訂單號碼
                        $OUT_YFPBOID=$tmp_array[1]; //對外訂單編號
                        $IN_YFPBOID =$tmp_array[1]; //對內訂單編號
                    }  else {
                        $OUT_YFPBOID=$tmp_array[1]; //對外訂單編號
                        $IN_YFPBOID =$tmp_array[2]; //對內訂單編號
                    }
                    //有拆帳的話固定抓第3個訂單號碼
                    if (trim($tmp_array[3])!='') {
                        $EX_YFPBOID=$tmp_array[3]; //拆帳用訂單編號
                    }
                    //更新ERP號碼、轉檔時間
                    $query_string='Update PORDER set YFPBOID=\''.$IN_YFPBOID.'\',BOERPTIME=\''.$insert_time.'\' where BOID=\''.$_BOID.'\'';
                    $query = mssql_query($query_string,$GB_dblk);

                    //轉檔完畢將拿到的ERP編號存入對應陣列
                    $result[trans_list]=$_BOID;

                    ${$_NAME}["$_BOID"]["YFPBOID"]=$IN_YFPBOID;   //對內訂單編號(VB)
                    ${$_NAME}["$_BOID"]["VVBOID"] =$OUT_YFPBOID;  //對外訂單編號(VV or AV)
                    ${$_NAME}["$_BOID"]["EXBOID"] =$EX_YFPBOID;   //拆帳用訂單編號

                    $query_string ='Update PORDEREG set BOERP=\''.$this->date_time().'\',FLAGPO=\'F\' where BOID=\''.$_BOID.'\' ';
                    $query_string.=' and TSNEW=\''.$insert_time.'\'';
                    $query = mssql_query($query_string,$GB_dblk);
                //額外訂單處理
                } else {
                    $tmp_array=explode('_',$update_array[ORDERS]);
                    $result[trans_list]=$_BOID.'A';
                    //包裝盒不會有VBB訂單
                    $EXTRA_OUT_YFPBOID=$tmp_array[1]; //對外訂單編號
                    $EXTRA_IN_YFPBOID =$tmp_array[1]; //對內訂單編號
                    ${$_NAME}["$_BOID"]["BOMEMO"]=${$_NAME}["$_BOID"]["BOMEMO"]."額外訂單號碼：".$EXTRA_IN_YFPBOID;
                }
            } else {
                $err_ary=explode("|",$update_array[MESSAGE]);
                $err_code=$err_ary[0];//錯誤訊息代碼
                $err_msg =$err_ary[1];//錯誤訊息文字
                if ($_TYPE=='M') {
                    //switch ($err_code) {
                        //出庫存訂單數量不足
                        //case "05":
                            //$sales_ary=$this->search_sales($_BOID);
                            //$err_mail_addr = $sales_ary[smail];
                            //$this->send_mail("[庫存不足] 雲端訂單無法開立","品名：".${$_NAME}["$_BOID"]["BMEMO"]."版號:".${$_NAME}["$_BOID"]["DM_PTE_NO"],"A",$err_mail_addr);
                            //break;
                    //}
                    $this->ll_echo("ERP訂單開立失敗，[".$_BOID."]，原因：".$err_msg);
                    $result[error_array]=$_BOID."ERP訂單開立失敗，原因：".$err_msg;
                    //失敗更新狀態
                    $query_string='Update PORDEREG set FLAGPO=\'E\' where BOID=\''.$_BOID.'\' and TSNEW=\''.$insert_time.'\'';
                    $query = mssql_query($query_string,$GB_dblk);
                } else {
                    $this->ll_echo("額外ERP訂單開立失敗，[".$_BOID."A]，原因：".$err_msg);
                    $result[error_array]=$_BOID."A額外ERP訂單開立失敗，原因：".$err_msg;
                }
            }
            if ($_TYPE=='M') {
                $this->ll_echo("[".$this->show_time()."]".$_BOID." XML傳送結束");
            } else {
                $this->ll_echo("[".$this->show_time()."]".$_BOID."A XML傳送結束");
            }
            return $result;
        }
        /***********************************************************************************************/
        /*  函式名稱：get_pdfdata($_file,$page)
        /*  函式參數： $_file    : PDF 檔案
        /*             $page     : PDF 第幾頁
        /*  回傳值  ： 訂單號碼或錯誤訊息
        /*  函式功能：讀取PDF檔案資訊
        /***********************************************************************************************/
        function get_pdfdata($_file,$page=0) {
            $p2 = new PDFlib();
            $oplist = "inmemory=true optimize=true compatibility=1.6";
            $p2->begin_document("", $oplist);
            $p2->set_info("Creator", ".");
            $p2->set_info("Author",  ".");
            $p2->set_info("Title",   ".");
            $doc = $p2->open_pdi_document($_file, "");
            $opdf[totalpage] = $p2->pcos_get_number($doc, "length:pages");  //得到匯入PDF總頁數
            $opdf[width]     = $p2->pcos_get_number($doc, "pages[".$page."]/width");  //得到匯入PDF該頁的寬
            $opdf[height]    = $p2->pcos_get_number($doc, "pages[".$page."]/height"); //得到匯入PDF該頁的高
            $opdf[version]   = $p2->pcos_get_number($doc, "pdfversion")/10; //得到匯入PDF文件版本
            $p2->begin_page_ext(1, 1, "topdown");   //開啟一個新PDF工作頁
            //關閉所有物件
            $p2->end_page_ext("");
            $p2->close_pdi_document( $doc);
            $p2->end_document("");
            unset($p2);
            return $opdf;
        }
        /***********************************************************************************************/
        /*  函式名稱：choose_ftp($_FACID)
        /*  函式參數： $_FACID   : 工廠代號
        /*
        /*  回傳值  ： 生產工廠 FTP 相關資訊
        /*  函式功能：回傳生產工廠FTP相關資訊
        /*
        /*  條件有變動須一併更改批次轉檔
        /***********************************************************************************************/
        function choose_ftp($_FACID) {
            global $GB_dblk;
            $query_string="Select * from W2PFAC where FACID='$_FACID'";
            $query=mssql_query($query_string,$GB_dblk);
            while ($rs=mssql_fetch_array($query)) {
                $result[FTPIP]=trim($rs[FFTPIP]);      // FTP 位址
                $result[FTPUSER]=trim($rs[FFTPNAME]);  // FTP 帳號
                $result[FTPPASS]=trim($rs[FFTPPASS]);  // FTP 密碼
                $result[FACODE]=trim($rs[FACODE]);     // XML 工廠 ID
                $result[FACYFP]=trim($rs[FACYFP]);     // 是否開立永豐訂單
                $result[FACNAME]=iconv('BIG5','UTF-8',trim($rs[FACNAME])); //工廠名稱
            }
            return $result;
        }
        /***********************************************************************************************/
        /*  函式名稱：search_sales($_BOID)
        /*  函式參數： $_BOID  : 訂單號碼
        /*  回傳值  ： 業務編號
        /*  函式功能：依據群組不同抓取所屬業務
        /***********************************************************************************************/
        function search_sales($_BOID) {
            global $GB_dblk;
            //$query_string ="Select top 1 a.uid,b.wbflow,c.ugid,e.usid from PORDER a join WBOOK b on a.bid=b.wbid ";
            //$query_string.=" join user2group c on a.uid=c.uid ";
            //$query_string.=" join pusergroup d on c.ugid=d.ugid ";
            //$query_string.=" join group2group e on d.ggid=e.ggid ";
            //$query_string.=" join w2pop f on a.boid=f.boid and f.v1=c.ugid ";
            //$query_string.=" where a.boid='$_BOID' and b.wbflow='3' order by BOTIME desc";
            $query_string ="Select top 1 c.v1,e.usid,d.ggid,d.ugname,f.sdeptid from PORDER a ";
            $query_string.="join WBOOK b on a.bid=b.wbid ";
            $query_string.="join w2pop c on a.boid=c.boid ";
            $query_string.=" join pusergroup d on c.v1=d.ugid ";
            $query_string.=" join group2group e on d.ggid=e.ggid ";
            $query_string.=" left join yfpsales f on e.usid=f.sid ";
            $query_string.=" where a.boid='$_BOID' and b.wbflow='3' order by BOTIME desc";

            $query=mssql_query($query_string,$GB_dblk);
            $num_row = mssql_num_rows($query);
            while ($rs=mssql_fetch_array($query)) {
                $result[ugid]  =trim($rs[v1]);
                $result[sal_no]=trim($rs[usid]);
                $result[ggid]  =trim($rs[ggid]);
                $result[dpid]  =trim($rs[sdeptid]);//業務歸屬部門ID，拆帳時發票廠別判斷用
								$result[ugname]=trim(iconv('BIG5','UTF-8',$rs[ugname]));//訂購單位(企業用戶用)
								
                
            }
            //永豐消費金融處業務強制改成逸銘 70095
            if ($result[ugid]=='sp006') {
                $result[sal_no]='70095';
            }
            //C 客戶折價碼抓業務
            $query_string ='Select a.BOID,b.CPID,b.CPSALNO from PORDER a join WBONUS b on a.bocoupon=b.cpid ';
            $query_string.=' where a.BOID=\''.$_BOID.'\' and (a.bocoupon <>\'\' and a.bocoupon is not null) ';
            $query=mssql_query($query_string,$GB_dblk);
            $count = mssql_num_rows($query);
            if ($count > 0) {
                while ($rs=mssql_fetch_array($query)) {
                    $result[sal_no]=trim($rs[CPSALNO]);
                }
            }
            //銷售業抓業務
            if ($result[sal_no]=='') {
                $query_string= 'Select A.BOID,B.PDT_ID,B.SAL_NO from PORDER A join Product_Master b on A.BID=B.PDT_ID ';
                $query_string.=' where a.BOID=\''.$_BOID.'\' ';
                $query=mssql_query($query_string,$GB_dblk);
                $count = mssql_num_rows($query);
                if ($count > 0) {
                   while ($rs=mssql_fetch_array($query)) {
                       $result[sal_no]=trim($rs[SAL_NO]);
                   }
                }
            }
            //判斷特定帳號的業務歸屬
            if ($result[sal_no]=='') {
                $query_string='Select USSALE from PORDER join USER2SALE on PORDER.UID=USER2SALE.USUSER where BOID=\''.$_BOID.'\'';
                $query=mssql_query($query_string,$GB_dblk);
                while ($rs=mssql_fetch_array($query)) {
                    $result[sal_no]=trim($rs[USSALE]);
                }
            }
            //依據產品來歸屬業務
            if ($result[sal_no]=='') {
                $query_string='Select A.BTYPE from wbook A join porder b on a.wbid=b.bid where b.boid=\''.$_BOID.'\'';
                $query=mssql_query($query_string,$GB_dblk);
                while ($rs=mssql_fetch_array($query)) {
                    $BTYPE=trim($rs[BTYPE]);
                }
                switch ($BTYPE) {
                    case "17"://相片書
                    case "20":
                    case "21":
                    case "22":
                    case "23":
                    case "24":
                    case "25":
                    case "26":
                    case "27":
                    case "28":
                    case "29":
                    case "77"://明信片
                    case "74"://貼紙
                    case "75":
                    case "85"://DM
                    case "86":
                    case "87":
                    case "88":
                    case "89":
                    case "90":
                    case "91":
                    case "92":
                    case "93":
                    case "94":
                    case "95":
                    case "96":
                    case "97":
                    case "98":
                    case "99":
                    case "100":
                    case "101":
                    case "102":
                        $result[sal_no]='00587';
                        break;
                    default:
                        $result[sal_no]='01605';
                        break;
                }
            }
            return $result;
        }
        /***********************************************************************************************/
        /*  函式名稱：search_cus_no($_BOID)
        /*  函式參數： $_BOID  : 訂單號碼
        /*  回傳值  ： 客戶代號
        /*  函式功能：抓取訂單所關連的客戶代號
        /***********************************************************************************************/
        function search_cus_no($_BOID) {
            global $GB_dblk;
            $result=array();
            $query_string ="Select top 1 a.uid,b.ugid,c.erpcusid,c.ggid from PORDER a  ";
            $query_string.=" join user2group b on a.uid=b.uid ";
            $query_string.=" join pusergroup c on b.ugid=c.ugid ";
            $query_string.=" join w2pop d on a.boid=d.boid and b.ugid=d.V1 ";
            $query_string.=" where a.boid='$_BOID'  order by BOTIME desc";
            $query=mssql_query($query_string,$GB_dblk);
            $num_row = mssql_num_rows($query);
            while ($rs=mssql_fetch_array($query)) {
                $result[cus_no]=trim($rs[erpcusid]);
                $result[ggid]  =trim($rs[ggid]);
            }
            return $result;
        }
        /***********************************************************************************************/
        /*  函式名稱：search_card($_CARD_NO)
        /*  函式參數： $_CARD_NO  : 信用卡號
        /*  回傳值  ： true | false
        /*  函式功能：判斷是否為永豐信用卡
        /***********************************************************************************************/
        function search_card($_CARD_NO) {
            global $GB_dblk;
            $query_string="SELECT * FROM PCCLIST where pccbin='$_CARD_NO'";
            $query=mssql_query($query_string,$GB_dblk);
            $num_row = mssql_num_rows($query);
            if ($num_row > 0) {
                return true;
            } else {
                return false;
            }
        }
        /***********************************************************************************************/
        /*  函式名稱：ll_echo($str)
        /*  函式參數： $str  : 顯示及記錄的文字
        /*  回傳值  ：
        /*  函式功能：echo 並記錄在LOG檔
        /***********************************************************************************************/
        function ll_echo($str)  {
            global  $thislog;
            error_log("$str \r\n", 3, $thislog);

            echo $str."<br>"; ob_flush(); flush();sleep(1);
        }
        /***********************************************************************************************/
        /*  函式名稱：date_time()、show_time()
        /*  函式參數：
        /*  回傳值   :目前時間
        /*  函式功能：目前時間
        /***********************************************************************************************/
        function date_time() {
            $now=time();
            return $now;
        }
        function show_time () {
          $time=date("Y-m-d H:i:s",time());
          return $time;
        }

        function  EB_sqluniencode( $data)   {
            $ucs2 = iconv('UTF-8', 'UCS-2LE', $data);
            $arr = unpack('H*hex', $ucs2);
            $hex = "0x{$arr['hex']}";
            return  $hex;
        }
        /***********************************************************************************************/
        /* Function: 格式化輸出字串, 左邊補零
        /*@param $num  : 轉換的數字
        /*@param $digit: 輸出格式的長度
        /*@param $chr  : 取代的字元 ,預設為 0
        /*  example: echo strflz(93,3);
        /*  結果為 093
        /***********************************************************************************************/
        function strflz($num,$digit,$chr=0) {
          return sprintf("%'".$chr.$digit."s",$num);
        }

        // ----------------------------------------------------
        // 以 CURL 方式對遠端 host 傳送 data
        // $host 遠端主機 DNS 或 IP
        // $url 要接收資料的程式路徑
        // $xml 傳送資料
        // $timeout 連接時限(timeout,秒數,0=無限久)
        /***********************************************************************************************/
        function  EB_sendhost($host,$url,$xml,$timeout=180)    {
            $httpurl = trim("$host/$url");                                           //XML service API
            $httpheader = array("Content-Type: text/xml;charset=utf-8","Expect: 100-continue");

            $ch = curl_init();         //啟動 CURL 連接
            curl_setopt($ch, CURLOPT_URL, $httpurl);      //設定 CURL 連結網址
            curl_setopt($ch, CURLOPT_POST, 1);            //設定採用 POST 方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $xml); //指定CURL 傳送內容
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//設定回傳內容為變數 (不直接顯示回傳結果)
            curl_setopt($ch, CURLOPT_HTTPHEADER,$httpheader );
            curl_setopt($ch, CURLOPT_HEADER, 0);                //加入 HEADER 內容
            curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);//設定 TIMEOUT 限制
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            $xmlResponse = curl_exec($ch);                            //開始傳送
            $info = curl_getinfo($ch,CURLINFO_HTTP_CODE);//傳送後取得 HTTP 狀態碼
            curl_close($ch);                                                        //關閉 CURL 連結

            return $xmlResponse;
        }
        /**************************************************************************************
        * 阿拉伯數字轉中文數字
        * input : 阿拉伯數字
        * output: 國字數字
        * 例：1000 => 1仟
        ***************************************************************************************/
        function getChineseNumber($money){
            $ar = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9") ;
            $cName = array("", "", "拾", "佰", "仟", "萬", "拾", "佰", "仟", "億", "拾億", "佰億", "仟億");
            $conver = "";
            $cLast = "" ;
            $cZero = 0;
            $i = 0;
            for ($j = strlen($money) ; $j >=1 ; $j--){
                $cNum = intval(substr($money, $i, 1));
                $cunit = $cName[$j]; //取出位數
                if ($cNum == 0) { //判斷取出的數字是否為0,如果是0,則記錄共有幾0
                    $cZero++;
                    if (strpos($cunit,"萬億") >0 && ($cLast == "")){ // '如果取出的是萬,億,則位數以萬億來補
                        $cLast = $cunit ;
                    }
                } else {
                    if ($cZero > 0) {// '如果取出的數字0有n個,則以零代替所有的0
                        if (strpos("萬億", substr($conver, strlen($conver)-2)) >0) {
                            $conver .= $cLast; //'如果最後一位不是億,萬,則最後一位補上"億萬"
                        }
                        $conver .= "零" ;
                        $cZero = 0;
                        $cLast = "" ;
                    }
                    $conver = $conver.$ar[$cNum].$cunit; // '如果取出的數字沒有0,則是中文數字+單位
                }
                $i++;
            }
            //'判斷數字的最後一位是否為0,如果最後一位為0,則把萬億補上
            if (strpos("萬億", substr($conver, strlen($conver)-2)) >0) {
                $conver .=$cLast; // '如果最後一位不是億,萬,則最後一位補上"億萬"
            } elseif(strlen($money)-1 == $cZero && $cZero >=5){
                $conver .=$cName[5];
            }
            //$conver=iconv('UTF-8','BIG5', $conver);;
            return $conver;
        }
        /***********************************************************************************************/
        /*  函式名稱：chkholday($l_date)
        /*  函式參數： $l_date    : 日期
        /*  回傳值  ： true(假日) or false(非假日)
        /*  函式功能：檢查日期是否為假日
        /***********************************************************************************************/
        Function chkholday($l_date){
           global $GB_dblk;

           $w_weekday=date("w",$l_date);
           if($w_weekday<>0 and $w_weekday <> 6){
              $SQL = "SELECT * FROM PHOLIDAY where HID='$l_date' ORDER BY HID ";
              $QUERY = mssql_query($SQL, $GB_dblk);
              $num=mssql_num_rows($QUERY);
              if($num > 0){
                 return true;   //假日
              }else{
                 return false;
              }
           }else{
              return true;   //星期日
           }
        }
        /***********************************************************************************************/
        /*  函式名稱：nextday($date,$days,$kind)
        /*  函式參數： $date    : 日期
        /*             $days    : 天數
        /*             $kind    : 往前或往後 (F 往後,B 往前)
        /*  回傳值  ： 日期
        /*  函式功能：計算輸入日期，前幾天或後幾天
        /*  Example : nextday(2010/12/23,1,F)  => 2010/12/24
        /***********************************************************************************************/
        Function nextday($date,$days,$kind){
           $yy = date("Y",$date);
           $mm = date("m",$date);
           $dd = date("d",$date);
           switch($kind){
              case "F":
                    $monthdays = $this->endmonth($yy.$mm);
                    $dw = $dd + $days;
                    while ($dw>$monthdays){
                       $dw = $dw - $monthdays;
                       $mm = $mm + 1;
                       if ($mm>12){
                          $mm = $mm - 12;
                          $yy = $yy + 1;
                       }
                       $monthdays = $this->endmonth($yy.$mm);
                    }
                    break;
              case "B":
                    $dw = $dd - $days;
                    while ($dw<=0){
                       $mm = $mm - 1;
                       if ($mm<1){
                          $mm = 12;
                          $yy = $yy - 1;
                       }
                       $monthdays = $this->endmonth($yy.$mm);
                       $dw = $dw + $monthdays;
                    }
                    break;
           }
           $dd = $dw;
           $st = mktime(0,0,0,$mm,$dd,$yy);

           return $st;
        }
        /***********************************************************************************************/
        /*  函式名稱：endmonth($yymm)
        /*  函式參數： $yymm    : 年月
        /*  回傳值  ： 天數
        /*  函式功能：計算輸入日期的那個月有幾天
        /***********************************************************************************************/
        Function endmonth($yymm){
           $leapyearsw = 0;
           $yw = substr($yymm,0,4);
           $dec_num = round($yw/4,0);
           if ($dec_num==$yw/4){
              $leapyearsw = $leapyearsw + 1;
           }
           $dec_num = round($yw/100,0);
           if ($dec_num=$yw/100){
              $leapyearsw = $leapyearsw - 1;
           }
           $dec_num = round($yw/400,0);
           if ($dec_num=$yw/400){
              $leapyearsw = $leapyearsw + 1;
           }
           $mm = substr($yymm,4,2);
           if ($mm==2){
              if ($leapyearsw==1){
                 $mdays = 29;
              }else{
                 $mdays = 28;
              }
           }else{
              if ($mm==1 or $mm==3 or $mm==5 or $mm==7 or $mm==8 or $mm==10 or $mm==12){
                 $mdays = 31;
              }else{
                 $mdays = 30;
              }
           }
           return $mdays;
        }
        /***********************************************************************************************/
        /*  函式名稱：opday($BOID)
        /*  函式參數： $BOID    : 訂單編號
        /*  回傳值  ： 工作日
        /*  函式功能：計算輸入訂單編號回傳需要多少個工作日(回傳的值為工廠約交日，平台約交日另外會在外面加 2 天)
        /***********************************************************************************************/
        /*
        function opday($BOID,$TYPE) {
            global $GB_dblk;

            switch (substr($BOID,0,1)) {
                //MOMO產品約交日
                case "S":
                    $a=0;
                break;
                default:
                $SQL1 = "select opdays from w2pop join porder on (w2pop.boid=porder.boid)
                        join wbook on (wbook.wbid=porder.bid) join w2popm on (w2pop.ppid=w2popm.ppid)
                        AND (porder.".$TYPE." = w2popm.brid) AND (w2popm.btype = wbook.btype AND w2popm.bpage=wbook.wbpages)
                        where w2pop.boid='$BOID'";
                $QUERY1=@mssql_query($SQL1,$GB_dblk);
                $a=0;
                while($REC = mssql_fetch_array($QUERY1)){
                   $a +=$REC[opdays];
                }
                $SQL = "Select PORDER.FACID,WBOOK.BTYPE,PORDER.BOPTION from PORDER join WBOOK on PORDER.BID=WBOOK.WBID ";
                $SQL.= "where PORDER.BOID='$BOID'";
                $QUERY=@mssql_query($SQL,$GB_dblk);
                while($result = mssql_fetch_array($QUERY)){
                    $BTYPE   =$result[BTYPE];
                    $BOPTION=$result[BOPTION];
                }
                $tmp_string=$BOPTION;
                $tmp_ary=explode(",",$tmp_string);
                foreach ($tmp_ary as $val) {
                    $tmp_ary1=explode("=",$val);
                    if ($tmp_ary1[0]=="bind") {
                        $bind=$tmp_ary1[1];
                        break;
                    }
                }
                switch ($BTYPE) {
                    case "17":case "20":case "21":case "22":case "23":case "24":case "25":case "26":case "27":case "28":case "29":
                        if ($bind=='b4' or $bind=='b5') {
                            $a=$a+1;
                        } else {
                            $a;
                        }
                        break;
                }

                if($a==0) $a=5;
                break;
            }
            return $a;
        }
        */
        /***********************************************************************************************/
        /*  函式名稱：get_3Dobjid($_BID)
        /*  函式參數： $_BID    : 作品ID
        /*  回傳值  ： 3D訂單物件ID
        /*  函式功能：輸入BID找出要生產的3D物件ID
        /***********************************************************************************************/
        function get_3Dbojid ($_BID) {
            global $GB_dblk;

            $query_string="select WBDATA from WBPAGE where WBID='$_BID'and WBO = 'S' ";
            $query=@mssql_query($query_string,$GB_dblk);
            while ($result = mssql_fetch_array($query)) {
                $WBDATA    = trim($result[WBDATA]); //物件ID
            }
            return $_BID."_".$WBDATA;

        }
        /***********************************************************************************************/
        /*  函式名稱：get_vdp_count($_BID)
        /*  函式參數： $_BID    : 作品ID
        /*  回傳值  ： VDP上傳的變動資料筆數
        /*  函式功能：輸入BID找出該VDP訂單總共有多少變動資料筆數
        /***********************************************************************************************/
        function get_vdp_count($_BID) {
            global $GB_dblk;

            $query_string="SELECT count(distinct X) as NUMS FROM  VDPEXCEL WHERE BID='$_BID'";
            $query=@mssql_query($query_string,$GB_dblk);
            while ($result = mssql_fetch_array($query)) {
                $count     = trim($result[NUMS]);
            }
            return $count;
        }
        /***********************************************************************************************/
        /*  函式名稱：findname($_BOID,$_TYPE,$_PAGES)
        /*  函式參數： $_BOID    : 平台訂單編號
        /*             $_TYPE    : 產品代號
        /*             $_PAGES   : 頁數
        /*  回傳值  ： 品項名稱
        /*  函式功能：輸入BTYPE跟PAGES找出對應的品名
        /***********************************************************************************************/
        function findname ($_BOID,$_TYPE,$_PAGES) {
            global $GB_dblk;

            switch ($_TYPE) {
                case '17':case '20':case '21':case '22':case '23':case '24':case '25':case '26':
                case '27':case '28':case '29':case '54':case '55':case '56':case '57':case '35':
                case '36':case '212':case '213':case '214':
                    $query_string='Select BMEMO from PGPRICE where BTYPE=\''.$_TYPE.'\'';
                    $query=@mssql_query($query_string,$GB_dblk);
                    while ($result = mssql_fetch_array($query)) {
                        $BMEMO     = iconv('BIG5','UTF-8',trim($result[BMEMO])); //備註
                    }
                    $SQL = 'Select w2pp.PPTYPE,w2pop.PPID from w2pop join w2pp on w2pop.ppid=w2pp.ppid where boid=\''.$_BOID.'\'';
                    $query = mssql_query($SQL,$GB_dblk);
                    while ($tmp_result   = mssql_fetch_array($query)) {
                        if ($tmp_result['PPTYPE']=='2' and (($tmp_result['PPID'] > '800' and $tmp_result['PPID'] < '806') or $tmp_result['PPID']=='780' or $tmp_result['PPID']=='781')) {
                            $bind=trim($tmp_result['PPID']);
                        }
                    }
                    if ($bind!='') {
                        switch ($bind) {
                            case '801':
                                $S_BIND="平裝";
                                $S_PAGES=intval($_PAGES-4);
                                break;
                            case '802':
                                $S_BIND="精裝";
                                $S_PAGES=intval($_PAGES-4);
                                break;
                            case '803':
                                $S_BIND="騎馬釘";
                                $S_PAGES=intval($_PAGES-4);
                                break;
                            case '804':
                                $S_BIND="蝴蝶頁(厚)";
                                $S_PAGES=intval($_PAGES-2);
                                break;
                            case '805':
                                $S_BIND="蝴蝶頁(薄)";
                                $S_PAGES=intval($_PAGES-2);
                                break;
                            case '780':
                                $S_BIND="(左裝)";
                                $S_PAGES=100;
                                break;
                            case '781':
                                $S_BIND="(上裝)";
                                $S_PAGES=100;
                                break;
                        }
                        $BMEMO.="-".$S_BIND."-".$S_PAGES."頁";
                    }
                    break;
								//VDP品項增加顯示成品尺寸
								case ($_TYPE >='250' and $_TYPE<='263'):
										$query_string='Select BMEMO,BSIZEW,BSIZEH,BSIZEC from PGPRICE where BTYPE=\''.$_TYPE.'\'';
                    $query=@mssql_query($query_string,$GB_dblk);
                    while ($result = mssql_fetch_array($query)) {
                        $BMEMO     = iconv('BIG5','UTF-8',trim($result[BMEMO])); //品名
												$width     = trim($result[BSIZEW]); //寬
												$height    = trim($result[BSIZEH]); //高
												$blood     = trim($result[BSIZEC]); //出血
                    }
										$BMEMO.=" (尺寸：".$width-($blood*2)."x".$height-($blood*2)." mm)";
										break;
                default:
                    switch ($_TYPE) {
                        case 74:
                        case 75:
												case 76:
                        case 48:
                        case ($_TYPE>=170 and $_TYPE<=177):
                        case ($_TYPE>=270 and $_TYPE<=272):
                            $query_string='Select BMEMO from PGPRICE where BTYPE=\''.$_TYPE.'\' and BPAGE=\'2\'';
                            break;
                        default:
                            $query_string='Select BMEMO from PGPRICE where BTYPE=\''.$_TYPE.'\' and BPAGE=\''.$_PAGES.'\'';
                            break;
                    }
                    $query=@mssql_query($query_string,$GB_dblk);
                    while ($result = mssql_fetch_array($query)) {
                        $BMEMO     = iconv('BIG5','UTF-8',trim($result[BMEMO])); //備註
                    }
                    if ($_TYPE>=170 and $_TYPE<=177) {
                        $BMEMO.="-".($_PAGES/2)."聯單";
                    }
                    break;
            }
            return $BMEMO;
        }
        /***********************************************************************************************/
        /* 應用程序打包
        /***********************************************************************************************/
        function  fopen_exec($URL)  {
            $timeout = 1800;

            set_time_limit(0);
            ini_set ('user_agent', $_SERVER['HTTP_USER_AGENT']);
            $old = ini_set('default_socket_timeout', $timeout);
            $file = fopen($URL, 'r');
            if($file)   {
              return  true;
            } else {
              return  false;
            }
        }
        /***********************************************************************************************/
        /*  函式名稱：check_template($_BKID)
        /*  函式參數： $yfpboid   :模版ID
        /*  回傳值  ： 對色/null;
        /*  函式功能： 判斷名片模版是否加上對色字樣傳給白紗
        /***********************************************************************************************/
        function check_template($_BKID) {
            global $GB_dblk;
            $CK_COLOR ="SELECT  top 1 pusergroup.ggid,pusergroup.ugid from user2group ";
            $CK_COLOR.=" join pusergroup on user2group.ugid=pusergroup.ugid ";
            $CK_COLOR.=" join group2block on user2group.ugid=group2block.ugid ";
            $CK_COLOR.=" where group2block.bkid ='".$_BKID."' ";
            $CK_COLOR.=" and pusergroup.ggid <>'38019423' ";

            $query=@mssql_query($CK_COLOR,$GB_dblk);
            while ($result = mssql_fetch_array($query)) {
                $ugid    = trim($result[ugid]); //子群組ID
                $ggid    = trim($result[ggid]); //母群組ID
            }
            $result=array();
            switch (strtolower($ugid)) {
                case 'b8ff7'://全聯
                case 'dbaf4'://杏一
                    $result['CKCOLOR']="對色";
                    break;
                case 'c2feb'://復興航空正反面需要對調
                    $result['CHANGEFTOB']="1";
                    break;
            }
            switch ($ggid) {
                case "84149358"://世界先進強制轉換成用版號開訂單
                    $result['CHANGE_PTE']="1";
                    break;
            }
            return $result;
        }
        /***********************************************************************************************/
        /*  函式名稱：mail_black($yfpboid,$file,$mail_addr)
        /*  函式參數： $yfpboid   : YFP訂單編號
        /*             $file      : 燙金黑板檔案(完整路徑)
        /*             $mail_addr : 檔案寄送人員
        /*
        /*  回傳值  ： null
        /*  函式功能：將喜帖燙金黑板寄送給相關人員
        /***********************************************************************************************/
        function mail_black($yfpboid,$file,$mail_addr) {
            $title =$yfpboid.'喜帖信封訂單燙金黑板';
            $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

            $boundary = uniqid("");

            $headers ="From: webmaster@cloudw2p.com"."\r\n";
            $headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
            $headers.="X-Priority: 1"."\r\n";
            $headers.="X-MSMail-Priority: High"."\r\n";
            $headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

            $attachment = fread(fopen($file, "r"), filesize($file));

            $read = base64_encode($attachment);
            $read = chunk_split($read);

            $emailBody1 = '--'.$boundary."\r\n";
            $emailBody1.="Content-Type: application/octet-stream; name=$yfpboid.pdf"."\r\n";
            $emailBody1.= 'Content-disposition: inline; attachment'."\r\n";
            $emailBody1.= 'Content-transfer-encoding: base64'."\r\n\r\n";

            $emailBody1 .=$read."\r\n";

            $emailBody1 .="--$boundary--";

            $result=mail($mail_addr, $subject, $emailBody1, $headers);
        }
        /***********************************************************************************************/
        /*  函式名稱：getpaperppid($btype,$ppid)
        /*  函式參數： $btype   : 產品代號
        /*             $ppid    : 紙別代號
        /*
        /*  回傳值  ： 加工條件陣列
        /*  函式功能：輸入BTYPE跟PPID找出紙別是否有綁訂加工條件
        /***********************************************************************************************/
        function getpaperppid($btype, $ppid) {
            global $GB_dblk;
            $btype = intval($btype);
            $ppid  = intval($ppid);
            $SQL = "SELECT W2POPMW.BTYPE, W2POPMW.PPID, W2POPMW.WPPID, W2PP.PPNAME ";
            $SQL.= "FROM W2POPMW  LEFT JOIN W2PP ON W2POPMW.WPPID = W2PP.PPID ";
            $SQL.= "WHERE W2POPMW.BTYPE = $btype AND W2POPMW.PPID = $ppid ORDER BY WPPID ";
            $QUERY = mssql_query($SQL);
            while($REC = mssql_fetch_array($QUERY))        {
                $IDX = $REC[PPID];
                $A[$IDX] = iconv('BIG5','UTF-8',$REC[PPNAME]);
            }
            // 有加工條件:回傳陣列; 沒有:回傳FALSE
            if(count($A)>0)    return    $A;
            else               return    FALSE;
        }
        /***********************************************************************************************/
        /*  函式名稱：check_same_receive($uid)
        /*  函式參數： $uid   : 下單人員代號
        /*
        /*  回傳值  ：是否為設定中的合併運送群組
        /*  函式功能：輸入下單人員代號，回傳是否為設定合併運送的群組
        /*  2014/04/22 Arvin 因目前杏一會替相關企業訂購名片造成可能收件者跟地址不同，固當初的名片合併判斷移除杏一
                             改用ord_groupID讓怡萍那邊去判斷收件人跟地址是否要合併
        /***********************************************************************************************/
        function check_same_receive ($uid='') {
            global $GB_dblk;

            $ck_array=array("14101330","86120567","22128207","16740494","28171546","16093877","46480100","97311466");
                           //新光人壽     南都      廣陽         全聯      震旦      板信      新竹物流     精誠
            $SQL= 'SELECT A.UGID,B.GGID,C.GGNAME  FROM USER2GROUP A join pusergroup B on A.UGID=B.UGID';
            $SQL.=' join GROUP2GROUP C on B.GGID=C.GGID where uid=\''.$uid.'\'';
            $QUERY = mssql_query($SQL);
            $nums = mssql_num_rows($QUERY);

            $result[status]=false;//default

            if ($nums > 0) {
                while ($result = mssql_fetch_array($QUERY)) {
                    $ggid  = trim($result[GGID]);
                }
                if (in_array($ggid,$ck_array)) {
                    $result[status]=true;
                    $result[mergeid]=$ggid;
                }
                $result[groupid]=$ggid;
            }
            return $result;
        }
        /***********************************************************************************************/
        /*  函式名稱：getsize($diecut,$bonum)
        /*  函式參數： $diecut    : 刀模代號
        /*             $bonum     : 數量
        /*  回傳值  ： 貼紙尺寸
        /*  函式功能：輸入平台編號傳回貼紙尺寸、出紙方向及大模張數
        /***********************************************************************************************/
        function getsize ($diecut,$bonum) {
            global $GB_dblk;
            $SQL='Select DCWIDTH,DCHEIGHT,DCMEMO from W2PDIECUT where DCID=\''.$diecut.'\'';
            $QUERY = mssql_query($SQL);
            while($REC = mssql_fetch_array($QUERY)) {
                $width =trim($REC[DCWIDTH]);
                $height=trim($REC[DCHEIGHT]);
                $memo  =trim($REC[DCMEMO]);
            }
            switch (substr($memo,0,1)) {
                case "B":
                    $result[SIZE]=$width.'X'.$height;
                    break;
                case "C":
                    $result[SIZE]=$width.'X'.$height.'+CR';
                    break;
                case "D":
                    $result[SIZE]=$width.'X'.$height.'+OV';
                    break;
                case "E":
                    $result[SIZE]='POST+'.$width.'X'.$height;
                    break;
                case "F":
                    $result[SIZE]='HEART+'.$width.'X'.$height;
                    break;
            }
            //橫放算法
            $H_width=143/($width+3);
            $H_height=450/($height+3);
            $H_PICS = (floor($H_width))*2*floor($H_height);
            //直放算法
            $V_width=143/($height+3);
            $V_height=450/($width+3);
            $V_PICS = (floor($V_width))*2*floor($V_height);
            if($V_PICS>$H_PICS) {
               $PICS=$V_PICS;
            } else {
               $PICS=$H_PICS;
            }
            $result[PICS]=ceil($bonum/$PICS);

            return $result;

        }
        /***********************************************************************************************/
        /*  函式名稱：get_product_info($PTE_ID)
        /*  函式參數： $PTE_ID: 企業產品版號
        /*  回傳值  ： 資訊陣列
        /*  函式功能：企業產品名稱、對應數量等
        /***********************************************************************************************/
        function get_product_info($DM_PTE_NO) {
            global $GB_dblk;
            $search = array ("'","\"","<",">","&");
            $replace = array ("’","＂","＜","＞","＆");
            $SQL="Select * from w2product40 where PRTPTEID='$DM_PTE_NO'";
            $query=mssql_query($SQL,$GB_dblk);
            $result=array();
            while ($rs1=mssql_fetch_array($query)) {
                $result[WBPAGES]  =trim($rs1[BPAGE]);
                $result[PPID]     =trim($rs1[PPID]);
                $result[MAP_BTYPE]=trim($rs1[SIZETYPE]);
                $result[PRTRATE]  =trim($rs1[PRTRATE]);
                $result[PRTUNIT]  =trim(iconv('BIG5','UTF-8',$rs1["PRTUNIT"]));
                $result[ERPUNIT]  =trim(iconv('BIG5','UTF-8',$rs1["ERPUNIT"]));
                $result[BMEMO]    =str_replace($search, $replace, trim(iconv('BIG5','UTF-8',$rs1["PRTNAME"])));
                $result[BMEMO2]   =str_replace($search, $replace, trim(iconv('BIG5','UTF-8',$rs1["PRTNAME2"])));
            }
            return $result;
        }
        /***********************************************************************************************/
        /* 轉檔清冊表頭
        /***********************************************************************************************/
        function list_mail_head ($MAIL_N='in',$BTYPE) {
            $html ='<table border=\'1\' cellpadding=\'0\' width=\'100%\'>';
            $html.='<tr>';
            $html.='<td><p align=\'center\'><strong>項</strong></p></td>';
            if ($MAIL_N=='out' or $MAIL_N=='out_s') {
                switch ($BTYPE) {
                    case '17':
                    case '20':
                    case '21':
                    case '22':
                    case '23':
                    case '24':
                    case '25':
                    case '26':
                    case '27':
                    case '28':
                    case '29':
                        break;
                    default:
                        $html.='<td><p align=\'center\'><strong>條碼</strong></p></td>';
                        break;
                }
            }
            $html.='<td><p align=\'center\'><strong>永豐編號<br>(平台編號)</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>生產工廠</strong></p></td>';
            if ($MAIL_N=='in') {
                $html.='<td><p align=\'center\'><strong>作品名稱</strong></p></td>';
            } else {
                switch ($BTYPE) {
                    case '17':
                    case '20':
                    case '21':
                    case '22':
                    case '23':
                    case '24':
                    case '25':
                    case '26':
                    case '27':
                    case '28':
                    case '29':
                        $html.='<td><p align=\'center\'><strong>作品名稱</strong></p></td>';
                        break;
                }
            }
            $html.='<td><p align=\'center\'><strong>產品類型</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>數量</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>加工條件</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>地址</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>電話</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>收件人 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>約交日</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>卡別<br>(發票訂單)</strong></p></td>';
            $html.='<td><p align=\'center\'><strong>備註</strong></p></td>';
            $html.='</tr>';

            return $html;
        }
        /***********************************************************************************************/
        /* 工作單表頭
        /***********************************************************************************************/
        function work_mail_head () {
            $html ='<table border=\'1\' cellpadding=\'0\' cellspacing=\'0\' width=\'100%\'>';
            $html.='<tr>';
            $html.='<td><p align=\'center\'><strong>項 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>條碼 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>永豐編號 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>檔案名稱 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>作品名稱 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>訂購時間 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>產品類型 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>本數 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>內頁 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>殼衣 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>書衣 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>約交日 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>卡別 </strong></p></td>';
            //$html.='<td><p align=\'center\'><strong>發票訂單 </strong></p></td>';
            //$html.='<td><p align=\'center\'><strong>平台編號 </strong></p></td>';
            $html.='<td><p align=\'center\'><strong>備註 </strong></p></td>';
            $html.='</tr>';

            return $html;
        }

    }
?>


