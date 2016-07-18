<?
    /***********************************************************************************************
    *  WTP 批次轉檔主程式 wtp_trans.php
    *  設定檔：       wtp_trans.ini.php
    *  合板產品分支： wtp_array.php
    *  photo產品分支：vow_array.php
    *  XML 樣版：     xml_template.php
    *  轉檔清冊信件： wtp_mail.php
    *  2010/07/05 Code by Arvin
    *  2011/04/01 Arvin 增加兌換碼判斷，若有對應訂單號碼則寫入XML
    *  2011/04/21 Arvin 針對可能輸入特定符號如單引號等透過內建function 轉成html Tag
    *  2011/05/26 Arvin 將名片的訂購人姓名盒數加入XML MEMO內
    *  2011/05/30 Arvin 增加判斷統編為虛擬統編時(99999開頭)，要找出客戶代號代入，並不帶入統編進入XML
    *  2011/06/15 Arvin 將付款方式，虛擬ATM (5)的發票寄送方式也改為月結
    *  2011/07/05 Arvin 修改抓CSU_NO Function 使用方式
    *  2011/08/04 Arvin 修改opday抓加工日期的方式，修改區分為工廠約交日與消費者約交日，工作單、轉檔清冊是工廠約交日
    *  2011/11/15 Arvin 攝影展活動截止移除轉檔清冊額外判斷
    *  2011/12/13 Arvin 判斷UDNAME若為空值則改抓BORNAME
    *  2011/12/22 Arvin 非兌換券產品且非開立三聯式發票的訂單及兌換券但有額外購買包裝盒的額外訂單，轉ERP的客戶代號寫入922195
    *  2012/03/09 Arvin 增加MEMO2存放包裝紙盒數量資訊
    *  2012/05/02 Arvin 抓取合併交寄主訂單資訊改用get_mainboid func 處理
    *  2012/05/23 Arvin 只要是二聯式發票，客戶代號全部帶922195
    *  2012/06/14 Arvin 筆記本內頁有印紋的部分改為180，以達到產生不同料號
    *  2012/06/27 Arvin 三聯式發票將CUS_NA改帶入發票抬頭
    *  2012/07/02 Arvin 拼圖的產品轉檔時要多開VB訂單，所以在產生XML時轉換為YFP
    *  2012/07/05 Arvin 拼圖直接從前台就變更為 FACID=10 自製
    *  2012/07/13 Arvin 增加計算單雙面頁數TAG
    *  2012/07/20 Arvin 無框畫訂單類別改為加工收入
    *  2012/08/23 Arvin 增加YFP Tag 記錄半自製半外包的資訊
    *  2012/08/24 Arvin 增加GROUPID、FLOWNEW判斷購物車主訂單處理
    *  2012/08/29 Arvin 吸水杯墊會依據數量給不同單位生產，若給依您印23則要產生VB訂單
    *  2012/09/10 Arvin 只要有設計師分紅的訂單YFP都填入1，去多開VB訂單來灌加工費進去
    *  2012/10/04 Arvin 變更設計師分紅照原本的判斷，有自製才產生VB單純外包購進不產生。修正虛擬ATM除了Template客戶改成月結外其餘為一般訂單
    *  2012/10/12 Arvin 便利貼多帶封面料號過去ERP配合class.php裡面的make_xml()
    *  2012/10/25 Arvin 判斷二聯式發票若有輸入抬頭就不帶客戶代號
    *  2012/10/25 Arvin 判斷銷售業 C、D大類，影印紙、包裝盒生產工廠要帶010486，G大類 牛皮紙袋生產工廠要帶入010110
    *  2012/11/14 Arvin 判斷若為設計師訂購本身的商品不分紅時，XML轉檔把設計師客戶代號拿掉
    *  2012/11/29 Arvin 重新整理移除YFP Tag 吸水杯墊、HTC的設定，由FACID判斷即可
    *  2012/12/06 Arvin 吸水杯墊訂單變更為加工收入
    *  2012/12/25 Arvin 拼圖訂單類別由加工收入改為一般訂單
    *  2013/02/06 Arvin 銷售業 E 燈籠產品改為一般訂單
    *  2013/02/06 Arvin 燈籠生產廠商改帶入PDT_ID=E1112081 E1112083 帶014825 扶風文化(洪新富) PDT_ID=E1112082 帶 211735 依您印
    *  2013/02/22 Arvin 二聯式發票若有填買受人將買受人資料帶入客戶名稱
    *  2013/03/11 Arvin 假統編(99999)開頭的判斷拿掉
    *  2013/03/18 Arvin 調整原先的檔案處理流程，增加複寫聯單的流水號位置座標及起號；自製名片轉版號給ERP;依您印自製名片處理
    *  2013/05/03 Arvin 增加4Tag 分別為 <DS_NO2>	設計師代號2
    *                                   <DS_AMT2>	設計師分紅金額2
    *                                   <MEMO3>	名片明細資料
    *                                   <merge_id>	合併送貨點key值
    *                                   增加喜帖、數位名片、自製名片、筆記本產品計算單雙頁數；合併交寄T
    *  2013/05/07 Arvin 筆記本加厚板要額外帶125 高單白銅T到訂單裡面
    *  2013/06/26 Arvin 鏡盒訂單屬性改為加工收入
    *  2013/07/02 Arvin 直噴機產品改為外包訂單
    *  2013/07/10 Arvin 多配送點xml Tag 設定
    *  2013/07/22 Arvin 企業DM(BTYPE=40)含稅單價直接抓出來用不用在除以1.05
    *  2013/08/14 Arvin 重新調整配送方式ERP代號對應
    *  2013/08/20 Arvin 判斷若有加購包裝紙盒要把數量帶進來
    *  2013/09/12 Arvin 判斷特殊客戶直接修改發票類別不再保持月結但備註發票隨貨送
    *                            增加訂單發票備註、內部自用的訂單分類
    *  2013/09/16 Arvin 判斷當BOTYPE = B3的時候才開自用訂單其餘開打樣訂單，不包括有預收單號的訂單。
    *  2013/10/14 Arvin 增加PACKING_KIND代號Z，給賴怡萍用來判斷直接抓QTY欄位理面的值出來秀
    *  2014/01/29 Arvin 增加苗順汽車發票隨貨送
    *  2014/02/14 Arvin 增加是否拆帳及拆帳金額、XML版本、合併群組TAG
    *  2014/02/26 Arvin 修改明信片不要藍色封套的資訊，避免同時間有買包裝盒又不要藍色封套造成判斷錯誤
    *  2014/04/08 Arvin 厚薄蝴蝶裝增加高單白銅T，厚的為原本算出來的張數x2，薄的等於原本算出來的張數
    *  2014/04/11 Arvin 在由使用者輸入的資料欄位TAG中，用<![CDATA[資料]]>包起來避免XML Parse失敗
    *  2014/04/23 Arvin 增加雄獅旅行社月結但發票隨貨送
    *  2014/05/07 Arvin 修改蝴蝶裝高單白銅T紙張計算及增加隨身瓶、拼圖的料號帶給ERP去排程內自動帶料
    *  2014/05/30 Arvin 修改紙袋生產工廠不帶新店廠(010110)，讓訂單是一般訂單自製的狀態
    *  2014/07/14 Arvin 增加名信片規格喜帖紙張計算
    *  2014/07/22 Arvin 便利貼紙張計算由原本的內頁變更為封面
    *  2014/08/04 Arvin 判斷如果沒有成本就把售價當做工廠成本
    *  2014/08/19 Arvin 判斷若為客訴訂單則客戶代號強制改為922195
    *  2014/10/20 Arvin 增加直式掛勾掛曆處理
    *  2014/10/28 Arvin 移除計算約交日公式，讓無BOSHIPTIME的訂單不要轉過
    *  2014/10/30 Arvin 調整送貨點的訂單數量為原始數量，不經過單位轉換
    *  2014/10/30 Arvin 增加客戶比例、客戶CUS_PO欄位
    *  2014/11/27 Arvin 移除筆記本與蝴蝶裝額外帶高單白桐T
    *  2014/12/17 Arvin 增加生產機台資料TAG，買賣業及純自製不需要丟機台資料。
    *  2015/01/22 Arvin XML V16版 增加發票廠別、出貨倉別
    *  2015/03/12 Arvin 增加icash生產機台及工作單設定
		*  2015/03/30 Arvin 2015/03/30 Arvin 生管通知拿掉180內頁紙別(還是帶180過去當CODE第三碼，但ERP對應代號內的料號清空)
		*  2015/04/30 Arvin 增加2個地方ORD_SUM TAG，記錄未稅總價
		*  2015/05/26 Arvin 銷售業BOSUBPRICE資料改成計算完的平均單價值接存入，XML不在另外判斷
		*  2015/05/27 Arvin 未稅總金額小數點第一位四捨五入到整數位
		*  2015/06/09 Arvin 客訴訂單歸屬業務改為網印的業務，開VV訂單
		*  2015/06/24 Arvin 修正只要任何產品是客訴STOCK都要給04
		*  2015/06/24 Arvin 移除拼圖料號的資訊
		*  2015/06/30 Arvin 拼圖改為加工收入
		*  2015/07/23 Arvin 調整判斷是否為打樣、客訴訂單的判斷往上，並增加判斷當月結且一般訂單才將發票抬頭當客戶名稱，否則一樣用客戶名稱貨收件人名稱當客戶名稱
		*  2015/08/24 Arvin 保溫瓶強制轉換成版號211341MZ9P000
		*  2015/09/16 Arvin 例外判斷富邦識別證的紙張算法
		*  2015/12/31 Arvin 2016/1/1 開始都都轉成不拆帳  (又改回要拆帳)
		*  2016/03/24 Arvin 增加台中所出貨、發票設定
    ***********************************************************************************************/
    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml.= '<ns0:TRANORD xmlns:ns0="http://WTPOrderSchemas.TRANORD">';
    $xml.= '<SEND_DEST>YFP</SEND_DEST>';
    $xml.= '<DOC_SRC>W2P</DOC_SRC>';
    $xml.= '<DOC_TYPE>'.$INI_SET[mode].'</DOC_TYPE>';
    $xml.= '<DOC_ID>'.$_BOID.'</DOC_ID>';
    $xml.= '<TRAN_CNT>1</TRAN_CNT>';
    $xml.= '<ORG_NO>V</ORG_NO>';
    $xml.= '<XML_VERSION>17</XML_VERSION>';//XML版本標記
    $xml.= '<TRAN>';
    $xml.= '<ORDER_MANUF>';
    $FAC_DATA=true;//是否傳遞機台資料開關
    $check_sal_ary=array('01605','00587');
    //抓所屬業務
    $sal_ary=$this->search_sales($_BOID);
    if (substr($_BOID,0,1)=='S') {
        $FAC_DATA=false;
        switch (substr($_ARRAY[$_BOID][PDT_ID],0,1)) {
            case 'C':
            case 'D':
                $xml.= '<MANUF_COMP>010486</MANUF_COMP>';
                break;
            //case 'G':
            //    $xml.= '<MANUF_COMP>010110</MANUF_COMP>';
            //    break;
            case 'E':
                if ('E1112081'==$_ARRAY[$_BOID][PDT_ID]) {
                    $xml.= '<MANUF_COMP>014825</MANUF_COMP>';
                } else {
                    $xml.= '<MANUF_COMP>211735</MANUF_COMP>';
                }
                break;
            default:
                $xml.= '<MANUF_COMP>'.$result_ftp[FACODE].'</MANUF_COMP>';
                break;
        }
    } else {
        //非網印業務發給新店廠只要產生AC or DC..單純工廠訂單的情況，生產工廠不給值
        //若哪天要進網印的情況生產工廠就要丟用以產生VC or VB訂單
        if (!in_array($sal_ary[sal_no],$check_sal_ary) and ($_ARRAY[$_BOID][FACID]=='16' or $_ARRAY[$_BOID][FACID]=='42')) {
            $xml.= '<MANUF_COMP></MANUF_COMP>';
        } else {
            $xml.= '<MANUF_COMP>'.$result_ftp[FACODE].'</MANUF_COMP>';
        }
    }
    //2012/08/23 Arvin 增加YFP Tag 記錄自製資訊，只要有自己生產的時候YFP都填入1
    switch ($_ARRAY[$_BOID][BTYPE]) {
        case "142": case "143": case "144": case "145": case "146": case "147": case "148": case "149": case "150": case "151":
        case "152": case "153": case "154": case "155": case "156": case "157": case "158": case "159": case "160": case "161":
        case "162": case "163": case "115": case "116": case "117": case "118": case "119": case "120": case "121": case "122":
        case "30": case "31": case "32": case "33": case "38": case "39":
            $xml.= '<YFP>1</YFP>';
            break;
        default:
            if ($_ARRAY[$_BOID][FACID]=='10') {
                $xml.= '<YFP>1</YFP>';
            } else {
                $xml.= '<YFP>0</YFP>';
            }
            break;
    }
    $xml.= '<PLATE_ID>'.$DATETIME.sprintf("%'02s",$_STEP).'</PLATE_ID>';
    $xml.= '<ORDER_CUS_CNT>1</ORDER_CUS_CNT>';
    $xml.= '<MANUF_QTY>'.$T_BONUM.'</MANUF_QTY>';

    //2014/08/04 Arvin 判斷如果沒有成本就把售價當做工廠成本
    $COST=$_ARRAY[$_BOID][BOPRICEM3];
    if ($COST=='' or $COST < 1) {
        $COST=$_ARRAY[$_BOID][BOPRICE];
    }
    $xml.= '<MANUF_PRI>'.round(($COST/1.05)/$T_BONUM,4).'</MANUF_PRI>';
    $xml.= '<MANUF_PRI2>'.round($COST/$T_BONUM,4).'</MANUF_PRI2>';
    //判斷是否有存約交日，若有直接抓出來倒扣
    if ($_ARRAY[$_BOID][BOSHIPTIME]!=0) {
        if ($_ARRAY[$_BOID][BTYPE]=='31' or $_ARRAY[$_BOID][BTYPE]=='30') {
            //桌曆自製的工廠約交日為客戶約交日扣 1 天
            if ($_ARRAY[$_BOID][FACID]=='10') {
                $s_day=1;
            } else {
           //桌曆外包的工廠約交日為客戶約交日扣 3 天
                $s_day=3;
            }
        } elseif (substr($_BOID,0,1)=='S') {
            if ($_ARRAY[$_BOID][PDT_GP]!='G001') {
                $s_day=1;
            } else {
                $s_day=2;
            }
        } else {
            $s_day=1;
        }
        $sub_day=1;
        $k=1;
        $real_sday=0;
        while ($k <= $s_day) {
            $chk_h=$this->chkholday($this->nextday($_ARRAY[$_BOID][BOSHIPTIME],$sub_day,'B'));
            if ($chk_h) {
                $real_sday++;
            } else {
                $k++;
            }
            $sub_day++;
        }
        $tol_sub_day=$s_day + $real_sday;
        $fac_sendday=$this->nextday($_ARRAY[$_BOID][BOSHIPTIME],$tol_sub_day,'B');
    } else {
         //沒有存約交日，用原本的計算方式 2014/10/28 移除不計算
        $fac_sendday=$_ARRAY[$_BOID][BOSHIPTIME];
    }
		$xml.= '<MANUF_REL_DTE>'.date("Y/m/d",$fac_sendday).'</MANUF_REL_DTE>';
    //規則：全自製、銷售業不丟機台資料。另外半自製半外包若我方無任何生產行為，只需要丟外包商的機台資料(客戶代號)
    //        代號：1940 ->大圖機  153 -> indigo5000
    if ($_ARRAY[$_BOID][FACID]=='10') {
        switch ($_ARRAY[$_BOID][BTYPE]) {
						case ($_ARRAY[$_BOID][BTYPE]==121 and $_ARRAY[$_BOID][BTYPE]==122)://A3拼圖
								$fac=$this->choose_ftp(19);
								$xml.= '<MANUF_MACHINE>'.$fac[FACODE].',</MANUF_MACHINE>';//生產機台資料
								break;
            case ($_ARRAY[$_BOID][BTYPE]>=115 and $_ARRAY[$_BOID][BTYPE]<=120)://非A3拼圖
                $fac=$this->choose_ftp(23);
                $xml.= '<MANUF_MACHINE>'.$fac[FACODE].',</MANUF_MACHINE>';//生產機台資料
                break;
            case ($_ARRAY[$_BOID][BTYPE]>=142 and $_ARRAY[$_BOID][BTYPE]<=163)://舊無框畫-小雅
                $xml.= '<MANUF_MACHINE>1940,801078</MANUF_MACHINE>';//生產機台資料
                break;
            case ($_ARRAY[$_BOID][BTYPE]>=232 and $_ARRAY[$_BOID][BTYPE]<=236)://新無框畫-
                $xml.= '<MANUF_MACHINE>1940,801239</MANUF_MACHINE>';//生產機台資料
                break;
            case "81"://icash
                $xml.= '<MANUF_MACHINE>2609</MANUF_MACHINE>';//生產機台資料
                break;
            default:
                $xml.= '<MANUF_MACHINE></MANUF_MACHINE>';//生產機台資料
                break;
        }
    } else {
        if ($FAC_DATA) {
            $xml.= '<MANUF_MACHINE>'.$result_ftp[FACODE].'</MANUF_MACHINE>';//生產機台資料
        } else {
            $xml.= '<MANUF_MACHINE></MANUF_MACHINE>';//生產機台資料
        }
    }
    $xml.= '</ORDER_MANUF>';
		$xml.= '<ORDER_YFP>';
    //2014/02/14 Arvin 增加是否拆帳及拆帳金額TAG
		$CREATE_ORD='N';
		$CREATE_PRI=0;
		$CREATE_SUM=0;
		$CREATE_PER=0;
		//拆帳條件為企業產品或Template(WBFLOW=3)名片且有拆帳金額(BOSHARE與BOPRICE金額不同代表有要拆帳)的一般訂單(BOTYPE=A1)。
    //目前只有下列的企業有拆帳，為避免資料錯誤問題先設定範圍在下列企業中
		//	'68921101' = 京城銀行
		//  '84149358' = 世界先進
		if ((in_array($_ARRAY[$_BOID][BOVATNO], array('68921101','84149358'))  and (in_array($_ARRAY[$_BOID][BTYPE], array('40','83','84'))	and $_ARRAY[$_BOID][WBFLOW]=='3')) and $_ARRAY[$_BOID][BOPRICE] != $_ARRAY[$_BOID][BOSHARE]	and $_ARRAY[$_BOID][BOTYPE]=='A1') {
				//2015/12/31 Arvin 2016/1/1 開始都都轉成不拆帳 
				//if($TRANS_DATE < mktime(0,0,0,1,1,2016)){
						$CREATE_ORD = 'Y';
				//}
        $CREATE_PRI = round(($_ARRAY[$_BOID][BOSHARE]/1.05)/$_ARRAY[$_BOID][BONUM],4); //未稅單價
        $CREATE_SUM = $_ARRAY[$_BOID][BOSHARE]; //含稅總價
        $CREATE_PER = round($_ARRAY[$_BOID][BOSHARE]/$_ARRAY[$_BOID][BOPRICE],4)*100; //拆帳比例
    }
		$xml.= '<CREATE_ORD>'.$CREATE_ORD.'</CREATE_ORD>';//是否拆帳開關，預設N
		$xml.= '<ORD_PRI>'.$CREATE_PRI.'</ORD_PRI>';
		$xml.= '<ORD_SUM>'.round($CREATE_PRI*$_ARRAY[$_BOID][BONUM]).'</ORD_SUM>';//未稅總價
		$xml.= '<TOL_SUM>'.$CREATE_SUM.'</TOL_SUM>';
		$xml.= '<PDT_PERCENT>'.$CREATE_PER.'</PDT_PERCENT>';
		$xml.= '</ORDER_YFP>';
		$xml.= '<ORDER_CUS>';
		$xml.= '<MEMB_ID>'.$_ARRAY[$_BOID][UNAME].'</MEMB_ID>';
    $show_boinvtitle = str_replace($search, $replace, $_ARRAY[$_BOID][BOINVTITLE]);    //轉換特殊字元
    $xml.= '<INV_NA><![CDATA['.$show_boinvtitle.']]></INV_NA>';
    //20110530增加判斷統編為虛擬統編時(99999開頭)，要找出客戶代號代入，並不帶入統編進入XML 2013/03/11 移除
    $t_result=array();
    $t_result=$this->search_cus_no($_BOID);
    $cus_no=trim($t_result[cus_no]);
    $show_bovatno=$_ARRAY[$_BOID][BOVATNO];
    $xml.= '<INV_NO>'.$show_bovatno.'</INV_NO>';
    //月結或隨貨開
    //2013/09/12 Arvin 判斷特殊客戶直接修改發票類別不再保持月結但備註發票隨貨送
    //$sal_ary=$this->search_sales($_BOID);
    $OTHER=true;
    switch ($sal_ary[ugid]) {
        case '242bb':
        case 'ebbe9':
        case '1075c':
        case '10d9e':
            $OTHER=false;
            break;
        default:
            if ($sal_ary[ggid]=='51816908') {
                $OTHER=false;
            }
            break;
    }
    if (($_ARRAY[$_BOID][BOPAYTYPE]=='4' or ($_ARRAY[$_BOID][BOPAYTYPE]=='5' and $_ARRAY[$_BOID][WBFLOW]=='3')) and $OTHER) {
        $xml.= '<CUS_IVC_KIND>10</CUS_IVC_KIND>';
    } else {
        $xml.= '<CUS_IVC_KIND>1</CUS_IVC_KIND>';
    }
		//2013/9/12 Arvin 增加訂單發票備註、內部自用的訂單分類
    $ORDER_TYPE="";
    switch ($_ARRAY[$_BOID][BOTYPE]) {
        case 'B1'://   打樣訂單 (客訴)
            $ORDER_TYPE='A';
            break;
        case 'B2'://   打樣訂單 (打樣)
            $ORDER_TYPE='B';
            break;
        case 'B3'://   自用訂單 (自用)
            $ORDER_TYPE='C';
            break;
        case 'B4'://   打樣訂單 (贈品)
            $ORDER_TYPE='D';
            break;
    }
				
    if ($_ARRAY[$_BOID][BOINVTYPE]=='3' and $ORDER_TYPE=="") {
        $show_udname=$_ARRAY[$_BOID][BOINVTITLE];
    } else {
        if ($_ARRAY[$_BOID][UDNAME]=='') {
            $show_udname=$_ARRAY[$_BOID][BORNAME];
        } else {
            $show_udname=$_ARRAY[$_BOID][UDNAME];
        }
    }
    if (($_ARRAY[$_BOID][BOINVTYPE]=='2' or $_ARRAY[$_BOID][BOINVTYPE]=='21') and $_ARRAY[$_BOID][WBFLOW]!='3') {
        //判斷有輸入抬頭就不帶入客戶編號
        if ($show_boinvtitle!='') {
            $xml.= '<CUS_NO></CUS_NO>';
            $show_udname = $show_boinvtitle;
        } else {
            $xml.= '<CUS_NO>922195</CUS_NO>';
            $show_udname = str_replace($search, $replace, $show_udname);    //轉換特殊字元
        }
    } else {
        // 2014/08/19 Arvin 判斷若為客訴訂單則客戶代號強制改為922195
        if ($_ARRAY[$_BOID][BOTYPE]=='B1') {
            $cus_no='922195';
        }
        $xml.= '<CUS_NO>'.$cus_no.'</CUS_NO>';
        $show_udname = str_replace($search, $replace, $show_udname);    //轉換特殊字元
    }
    $xml.= '<CUS_NA><![CDATA['.$show_udname.']]></CUS_NA>';
    $xml.= '<CUS_PO>'.$_ARRAY[$_BOID][CUSPO].'</CUS_PO>';
    $xml.= '<COUNTRY_CODE></COUNTRY_CODE>';
    $xml.= '<CUS_ZIP>'.$_ARRAY[$_BOID][UAID].'</CUS_ZIP>';
    if ($_ARRAY[$_BOID][UADDR]=='') {
        $show_addr = $_ARRAY[$_BOID][BORADDR];
    } else {
        $show_addr=$_ARRAY[$_BOID][UADDR];
    }
    $show_addr = str_replace($search, $replace, $show_addr);    //轉換特殊字元
    $xml.= '<CUS_ADDR><![CDATA['.$show_addr.']]></CUS_ADDR>';
		//2015/06/09 Arvin 客訴訂單歸屬業務改為網印的業務，開VV訂單
		if ($_ARRAY[$_BOID][BOTYPE]=='B1') {
				$xml.= '<SAL_NO>00587</SAL_NO>';
		} else {
				$xml.= '<SAL_NO>'.$sal_ary[sal_no].'</SAL_NO>';
		}
    $show_borname = str_replace($search, $replace, $_ARRAY[$_BOID][BORNAME]);    //轉換特殊字元
    $xml.= '<CONTACTOR><![CDATA['.$show_borname.']]></CONTACTOR>';
    $tmp_ary=explode("#",$_ARRAY[$_BOID][BORPHONE]);
    $xml.= '<CON_TEL>'.trim($tmp_ary[0]).'</CON_TEL>';
    $xml.= '<CON_TEL_EXP>'.trim($tmp_ary[1]).'</CON_TEL_EXP>';
    if (count($_ARRAY[$_BOID][POINT]) < 2) {
        $xml.= '<DELIVERY_COUNT>1</DELIVERY_COUNT>';
        $xml.= '<POINT>';
        $xml.= '<POINT_1>';
        $xml.= '<POINT_NO></POINT_NO>';
        $xml.= '<POINT_NA><![CDATA['.$show_borname.']]></POINT_NA>';
        $xml.= '<RECEIPT><![CDATA['.$show_borname.']]></RECEIPT>';
        $xml.= '<REC_TEL>'.trim($tmp_ary[0]).'</REC_TEL>';
        $xml.= '<REC_TEL_EXP>'.trim($tmp_ary[1]).'</REC_TEL_EXP>';
        $xml.= '<POINT_ZIP>'.$_ARRAY[$_BOID][BOMID].'</POINT_ZIP>';
        $show_boraddr = $_ARRAY[$_BOID][BORADDR];
        $show_boraddr = str_replace($search, $replace, $show_boraddr);    //轉換特殊字元
        $xml.= '<POINT_ADDR><![CDATA['.$show_boraddr.']]></POINT_ADDR>';
        $xml.= '<REL_DTE>'.date("Y/m/d",$fac_sendday).'</REL_DTE>';
        $xml.= '<ORD_QTY>'.$_ARRAY[$_BOID][BONUM].'</ORD_QTY>';
        $xml.= '</POINT_1>';
        $xml.= '</POINT>';
    //多送貨點
    } else {
        $xml.= '<DELIVERY_COUNT>'.count($_ARRAY[$_BOID][POINT]).'</DELIVERY_COUNT>';
        $xml.= '<POINT>';
        for ($d_i=1;$d_i<=count($_ARRAY[$_BOID][POINT]);$d_i++) {
            $P_NAME='POINT_'.$d_i;
            $xml.= '<'.$P_NAME.'>';
            $xml.= '<POINT_NO></POINT_NO>'; //POINT_NO 目前與ERP的是沒對應，但暫時保留TAG不給值留著將來或許有用到
            $xml.= '<POINT_NA><![CDATA['.$_ARRAY[$_BOID][ADRMAN][($d_i-1)].']]></POINT_NA>';
            $xml.= '<RECEIPT><![CDATA['.$_ARRAY[$_BOID][ADRMAN][($d_i-1)].']]></RECEIPT>';
            $tmp_ary=explode("#",$_ARRAY[$_BOID][ADRTEL][($d_i-1)]);
            $xml.= '<REC_TEL>'.trim($tmp_ary[0]).'</REC_TEL>';
            $xml.= '<REC_TEL_EXP>'.trim($tmp_ary[1]).'</REC_TEL_EXP>';
            $xml.= '<POINT_ZIP>'.$_ARRAY[$_BOID][ADRMID][($d_i-1)].'</POINT_ZIP>';
            $xml.= '<POINT_ADDR><![CDATA['.$_ARRAY[$_BOID][ADRADR][($d_i-1)].']]></POINT_ADDR>';
            $xml.= '<REL_DTE>'.date("Y/m/d",$fac_sendday).'</REL_DTE>';
            $xml.= '<ORD_QTY>'.$_ARRAY[$_BOID][ADRCOUNT][($d_i-1)].'</ORD_QTY>';
            $xml.= '</'.$P_NAME.'>';
        }
        $xml.= '</POINT>';
    }
    $xml.= '<WTP_ORDNO>'.$_BOID.'</WTP_ORDNO>';
    
    //2012/07/20 Arvin 無框畫訂單類別改為加工收入
    switch ($_ARRAY[$_BOID][BTYPE]) {
        case ($_ARRAY[$_BOID][BTYPE]>='142' and $_ARRAY[$_BOID][BTYPE]<='163'):
        case ($_ARRAY[$_BOID][BTYPE]>='232' and $_ARRAY[$_BOID][BTYPE]<='241'):
				case ($_ARRAY[$_BOID][BTYPE]>='115' and $_ARRAY[$_BOID][BTYPE]<='122'):
				case "81":  //icash
				case "111"://保溫瓶
				case "219":
						if ($ORDER_TYPE!='' and $ORDER_TYPE!='C') {
                $xml.= '<STOCK>04</STOCK>';
            } else {
                $xml.= '<STOCK>11</STOCK>';
            }
            break;
        default:
            //初期新店廠的新竹物流都是出庫存，若新店改為計畫性生產的時候就要改成一般訂單01
            if ($_ARRAY[$_BOID][FACID]=='16') {
                $xml.= '<STOCK>03</STOCK>';
						//買賣3D公仔開預收訂單
						} elseif ($_ARRAY[$_BOID][FACID]=='33') {
								$xml.= '<STOCK>08</STOCK>';
            } elseif ($ORDER_TYPE!='' and $ORDER_TYPE!='C') {
                $xml.= '<STOCK>04</STOCK>';
            } else {
                $xml.= '<STOCK>01</STOCK>';
            }
            break;
    }
    //拆帳訂單目發票歸屬抓YFPSALES內設定的單位代號
    if ($CREATE_ORD=='Y') {
        $xml.= '<CMP_NO>V</CMP_NO>'; //出貨倉別
        $xml.= '<INV_ORGNO>'.$sal_ary[dpid].'</INV_ORGNO>';//發票廠別
    } else {
        //新竹物流拖運單由新店廠製作，又因非網印業務直接開單只會有AC or DC等一張訂單而已
        //所以發票廠別跟出貨倉別都是C。除非客戶要求要總公司發票，就要改發票廠別
        if (!in_array($sal_ary[sal_no],$check_sal_ary) and $_ARRAY[$_BOID][FACID]=='16') {
            $xml.= '<CMP_NO>C</CMP_NO>'; //出貨倉別
            $xml.= '<INV_ORGNO>C</INV_ORGNO>';//發票廠別
				} elseif ($_ARRAY[$_BOID][FACID]=='42') { //台中商銀影印紙
						$xml.= '<CMP_NO>H</CMP_NO>'; //出貨倉別
            $xml.= '<INV_ORGNO>H</INV_ORGNO>';//發票廠別
        } else {
            //網印處業務接的單，發票網印(V)出貨網印(V)
            $xml.= '<CMP_NO>V</CMP_NO>'; //出貨倉別
            $xml.= '<INV_ORGNO>V</INV_ORGNO>';//發票廠別
        }
    }
    $xml.= '<STOCK_ORDNO>'.$_ARRAY[$_BOID][PREORDER].'</STOCK_ORDNO>';
  
    if (substr($_BOID,0,1)=='S') {
        $xml.= '<PTE_NO>'.$_ARRAY[$_BOID][PTE_NO].'</PTE_NO>';
        $xml.= '<WTP_PTECODE></WTP_PTECODE>';
        $xml.= '<WTP_PTENA><![CDATA['.$_ARRAY[$_BOID][PDT_NAME].']]></WTP_PTENA>';
    } else {
        switch ($_ARRAY[$_BOID][BTYPE]) {
            //企業產品
            case 40:
            case 42:
                $xml.= '<PTE_NO>'.$_ARRAY[$_BOID][DM_PTE_NO].'</PTE_NO>';
                $xml.= '<WTP_PTECODE></WTP_PTECODE>';
                $xml.= '<WTP_PTENA><![CDATA['.$_ARRAY[$_BOID][BMEMO].']]></WTP_PTENA>';
                break;
						//保溫瓶強制轉換成版號
						case 111:
						case 219:
								$xml.= '<PTE_NO>'.$_ARRAY[$_BOID][PTE_NO].'</PTE_NO>';
                $xml.= '<WTP_PTECODE></WTP_PTECODE>';
                $xml.= '<WTP_PTENA><![CDATA['.$_ARRAY[$_BOID][BMEMO].']]></WTP_PTENA>';
								break;
           //Template名片、icash卡
            case 81:
            case 83:
            case 84:
                //2015/03/10 Arvin 除了自製的名片之外，世界先進的名片也要強制轉換成用版號開訂單
                if ($_ARRAY[$_BOID][FACID]=='10' or $_ARRAY[$_BOID][CHANGE_PTE]=="1") {
                    $xml.= '<PTE_NO>'.$_ARRAY[$_BOID][PTE_NO].'</PTE_NO>';
                    $xml.= '<WTP_PTECODE></WTP_PTECODE>';
                    $xml.= '<WTP_PTENA><![CDATA['.$_ARRAY[$_BOID][BMEMO].']]></WTP_PTENA>';
                } else {
                    $xml.= '<PTE_NO></PTE_NO>';
                    $xml.= '<WTP_PTECODE>'.$this->strflz($_ARRAY[$_BOID][BTYPE],3).'_'.$this->strflz($single_double,3).'_'.$paper_id.'@'.$process.'</WTP_PTECODE>';
                    $xml.= '<WTP_PTENA>'.$_ARRAY[$_BOID][BMEMO].'</WTP_PTENA>';
                }
                break;
            default:
                $xml.= '<PTE_NO></PTE_NO>';
                switch ($_ARRAY[$_BOID]["BTYPE"]) {
                    case 35:
                    case 36:
                    case 55:
                    case 56:
                        if ($process=='') {
                            $show_pro=$paper_id;
                        } else {
                            $show_pro=$process.'_'.$paper_id;
                        }
                        //2012/06/14 Arvin 筆記本內頁，有印紋的部分改為180
												//2015/03/30 Arvin 生管通知拿掉180內頁紙別((還是帶180過去當CODE第三碼，但ERP對應代號內的料號清空))
                        if (stristr($show_pro,'813')) {
                            $BODY_PAPER='113';
                        } else {
                            $BODY_PAPER='180';
                        }
                        $xml.= '<WTP_PTECODE>'.$this->strflz($_ARRAY[$_BOID][BTYPE],3).'_'.$this->strflz($single_double,3).'_'.$BODY_PAPER.'@'.$show_pro.'</WTP_PTECODE>';
                        break;
                    case 207:
                    case 208:
                        //2012/08/22 Arvin 大平裝100張便利貼、精裝100張便利貼有兩料號，所以@後面多帶一次給琇雲那邊判斷
                        //2012/10/12 Arvin 便利貼多帶封面料號過去ERP配合class.php裡面的make_xml()
                        $xml.= '<WTP_PTECODE>'.$this->strflz($_ARRAY[$_BOID][BTYPE],3).'_'.$this->strflz($single_double,3).'_'.$paper_id.'@'.$paper_id.'_'.$process.'</WTP_PTECODE>';
                        break;
                    default:
                        $xml.= '<WTP_PTECODE>'.$this->strflz($_ARRAY[$_BOID][BTYPE],3).'_'.$this->strflz($single_double,3).'_'.$paper_id.'@'.$process.'</WTP_PTECODE>';
                        break;

                }
                $xml.= '<WTP_PTENA>'.$_ARRAY[$_BOID][BMEMO].'</WTP_PTENA>';
                break;
          }
    }
    $xml.= '<ORD_QTY>'.$T_BONUM.'</ORD_QTY>';
    if ($_ARRAY[$_BOID][BTYPE] < 85 and $_ARRAY[$_BOID][BTYPE] > 81) {
        $total_wkqty=0;
        //名片外包的數量為最低訂購量的加總
        for ($i_count=0;$i_count < count($_ARRAY[$_BOID][CFILE]);$i_count++) {
            $REAL_NUM=$_ARRAY[$_BOID][CNUM][$i_count];
            $MIN_NUM =$_ARRAY[$_BOID][CMINNUM][$i_count];
            if ($REAL_NUM > $MIN_NUM) {
                $total_wkqty=$REAL_NUM+$total_wkqty;
            } else {
                $total_wkqty=$MIN_NUM+$total_wkqty;
            }
        }
        $xml.= '<CUS_QTY></CUS_QTY>'; //客戶顯示數量
        $xml.= '<CUS_UNIT></CUS_UNIT>'; //客戶顯示單位
        $xml.= '<CUS_PERCENT></CUS_PERCENT>'; //單位換算比率
        $xml.= '<WK_QTY>'.$total_wkqty.'</WK_QTY>';
    } else {
        if (!empty($result_prt)) {
						//標準值
            $xml.= '<CUS_QTY>'.trim($_ARRAY[$_BOID][BONUM]).'</CUS_QTY>'; //客戶顯示數量
            $xml.= '<CUS_UNIT>'.$SHOW_UNIT.'</CUS_UNIT>'; //客戶顯示單位
            $xml.= '<CUS_PERCENT>'.$result_prt[PRTRATE].'</CUS_PERCENT>'; //單位換算比率
        } else {
            $xml.= '<CUS_QTY></CUS_QTY>'; //客戶顯示數量
            $xml.= '<CUS_UNIT></CUS_UNIT>'; //客戶顯示單位
            $xml.= '<CUS_PERCENT></CUS_PERCENT>'; //單位換算比率
        }
        $xml.= '<WK_QTY>'.$T_BONUM.'</WK_QTY>';
    }
		 //預收實現訂單金額為0
		 if ($_ARRAY[$_BOID][PREORDER]!='') {
				$ORD_PRI=0;
				$ORD_SUM=0;
				$TOL_SUM=0;
		 } else {
					//if (substr($_BOID,0,1)=='S') {
					//		$ORD_PRI=round(($_ARRAY[$_BOID][BOSUBPRICE]/$_ARRAY[$_BOID][BONUM])/1.05,4);
					//		$ORD_SUM=round($_ARRAY[$_BOID][BOSUBPRICE]/1.05,4);
					//} else {
							$ORD_PRI=round($_ARRAY[$_BOID][BOSUBPRICE]/1.05,4);
							$ORD_SUM=round($ORD_PRI*$_ARRAY[$_BOID][BONUM]);
					//}
					$TOL_SUM=trim($_ARRAY[$_BOID][BOPRICE]);
		 }
		 //判斷含稅總價小於未稅總價或稅額大於50000或未稅總價乘以1.05 - 含稅總價若相差超過1就認定為不合法的訂單
		if (($TOL_SUM-$ORD_SUM) < 0 or ($TOL_SUM-$ORD_SUM) > 50000 or abs(round($ORD_SUM*1.05-$TOL_SUM)) > 1) { 
					$tax_check=false;
		}
		$xml.= '<ORD_PRI>'.$ORD_PRI.'</ORD_PRI>';//未稅單價
		$xml.= '<ORD_SUM>'.$ORD_SUM.'</ORD_SUM>';//未稅總價
    $xml.= '<TOL_SUM>'.$TOL_SUM.'</TOL_SUM>';
    $xml.= '<REL_DTE>'.date("Y/m/d",$fac_sendday).'</REL_DTE>';
    switch ($_ARRAY[$_BOID][BOSEND]) {
        case 'A'://郵局掛號
            $ORD_SOU='EC_WAY2';
            break;
        case 'B'://郵局包裹
            $ORD_SOU='EC_WAY1';
            break;
        case 'C'://自取
            $ORD_SOU='SELF';
            break;
        case 'D'://宅急便
            $ORD_SOU='EC_WAY3';
            break;
        case 'E'://新竹物流
            $ORD_SOU='EC_WAY4';
            break;
        case 'G'://永航快遞
            $ORD_SOU='EC_WAY7';
            break;
        case 'H'://便利袋
            $ORD_SOU='EC_WAY6';
            break;
        default://預設新竹物流
          $ORD_SOU='EC_WAY4';
          break;
    }
    $xml.= '<ORD_SOU>'.$ORD_SOU.'</ORD_SOU>';
    $xml.= '<WTP_ORDDTE>'.date("Y/m/d H:i",$_ARRAY[$_BOID][BOTIME]).'</WTP_ORDDTE>';
    $xml.= '<WTP_FILENAME_BODY>'.$_ARRAY[$_BOID][BODY].'</WTP_FILENAME_BODY>';
    $xml.= '<WTP_FILENAME_COVER>'.$_ARRAY[$_BOID][COVER].'</WTP_FILENAME_COVER>';
    $xml.= '<WTP_FILENAME_WING>'.$_ARRAY[$_BOID][WING].'</WTP_FILENAME_WING>';
    $xml.= '<WTP_VOWNA><![CDATA['.htmlspecialchars($_ARRAY[$_BOID][TITLE]).']]></WTP_VOWNA>';
    $show_bomemo = str_replace($search, $replace, $_ARRAY[$_BOID][BOMEMO]);    //轉換特殊字元
    if ($_ARRAY[$_BOID][BTYPE] < 85 and $_ARRAY[$_BOID][BTYPE] > 81) {
        $tmp_str='';
        if ($_ARRAY[$_BOID][CNAME][0]!='') {
            $tmp_str='';
            foreach ($_ARRAY[$_BOID][CNAME] as $c_key => $c_value) {
                if ($tmp_str=='') {
                    $tmp_str=$c_value.$_ARRAY[$_BOID][CNUM][$c_key]."盒";
                } else {
                    $tmp_str.=",".$c_value.$_ARRAY[$_BOID][CNUM][$c_key]."盒";
                }
            }
        }
        $xml.= '<MEMO><![CDATA['.$show_bomemo.' '.$tmp_str.']]></MEMO>';
    } else {
        $xml.= '<MEMO><![CDATA['.$show_bomemo.']]></MEMO>';
    }
    //2013/05/16 Arvin MEMO2改存加工條件資訊(中文)
    $work_process="";
    if (is_array($_ARRAY[$_BOID][WORKNAME])) {
        foreach ($_ARRAY[$_BOID][WORKNAME] as $w_value) {
            if ($work_process=='') {
                $work_process=$w_value;
            } else {
                $work_process.="，".$w_value;
            }
        }
        $work_process.="。";
    }
    $sub_work_process="";
    if (is_array($_ARRAY[$_BOID][SUBWORKNAME])) {
        foreach ($_ARRAY[$_BOID][SUBWORKNAME] as $s_value) {
            if ($sub_work_process=='') {
                $sub_work_process=$s_value;
            } else {
                $sub_work_process.="，".$s_value;
            }
        }
    }
    $show_memo2 = str_replace($search, $replace, ($work_process.$sub_work_process));    //轉換特殊字元
    $xml.= '<MEMO2><![CDATA['.$show_memo2.']]></MEMO2>';
    //2013/08/20 Arvin 判斷若有加購包裝紙盒要把數量帶進來
    if ($_ARRAY[$_BOID][BOX]!='') {
        if ($_ARRAY[$_BOID][BTYPE]=='77') {
            if ($_ARRAY[$_BOID][EXTRA_SHOW]!='') {
                $xml.= '<PACKING_KIND>Z</PACKING_KIND>'; //包裝類型
                $xml.= '<PACKING_QTY>包裝紙盒 '.$_ARRAY[$_BOID][BOX].'個</PACKING_QTY>';//包裝數量
            } else {
                $xml.= '<PACKING_KIND>Z</PACKING_KIND>'; //包裝類型
                $xml.= '<PACKING_QTY>包裝紙盒 '.$_ARRAY[$_BOID][BOX].'個，明信片卡套 '.$T_BONUM.'個</PACKING_QTY>';//包裝數量
            }
        } elseif ($_ARRAY[$_BOID][BTYPE]=='44' or $_ARRAY[$_BOID][BTYPE]=='43') {
            $xml.= '<PACKING_KIND>Z</PACKING_KIND>'; //吸水杯墊腳架
            $xml.= '<PACKING_QTY>'.$show_memo2.'</PACKING_QTY>';//吸水杯墊腳架
        } else {
            $xml.= '<PACKING_KIND>A</PACKING_KIND>'; //包裝類型
            $xml.= '<PACKING_QTY>'.$_ARRAY[$_BOID][BOX].'</PACKING_QTY>';//包裝數量
        }
    } else {
        if ($_ARRAY[$_BOID][BTYPE]=='77') {
            if ($_ARRAY[$_BOID][EXTRA_SHOW]!='') { //不要藍色卡套
                $xml.= '<PACKING_KIND></PACKING_KIND>';
                $xml.= '<PACKING_QTY></PACKING_QTY>';
            } else {
                $xml.= '<PACKING_KIND>Z</PACKING_KIND>';
                $xml.= '<PACKING_QTY>明信片卡套 '.$T_BONUM.'個</PACKING_QTY>';
            }
        //2014/09/11 Arvin 彩TEE活動用
				//2015/12/30 Arvin 福袋活動-滿2000送福袋(不含月結及template訂單)
        // } elseif ($BOPAYTYPE!='4' and $WBFLOW!='3') {						
						// if ($_ARRAY[$_BOID][GROUPID]!='' and $_ARRAY[$_BOID][TOTAL] >= 2000) {
								// $xml.= '<PACKING_KIND>Z</PACKING_KIND>';
								// $xml.= '<PACKING_QTY>送福袋</PACKING_QTY>';
						// } elseif ($_ARRAY[$_BOID][BOPRICE] >=2000) {
								// $xml.= '<PACKING_KIND>Z</PACKING_KIND>';
								// $xml.= '<PACKING_QTY>送福袋</PACKING_QTY>';
						// }
        		// $xml.= '<PACKING_KIND>Z</PACKING_KIND>';
        		// $xml.= '<PACKING_QTY>海灘包1個</PACKING_QTY>';
        } else {
            if ($_ARRAY[$_BOID][EXTRA_SHOW]!='') { //拼圖立體木盒、木框判斷
                $xml.= '<PACKING_KIND>Z</PACKING_KIND>'; //Z代號轉給ERP來判斷直接抓QTY欄位出來顯示
                $xml.= '<PACKING_QTY>'.$_ARRAY[$_BOID][EXTRA_SHOW].'</PACKING_QTY>';
            } else {
                $xml.= '<PACKING_KIND></PACKING_KIND>';
                $xml.= '<PACKING_QTY></PACKING_QTY>';
            }
        }
    }
    $xml.='<INTERNAL_KIND>'.$ORDER_TYPE.'</INTERNAL_KIND>';
    //增加訂單發票備註
    $xml.='<IVC_MEMO>'.$_ARRAY[$_BOID][BOMEMO2].'</IVC_MEMO>';

    if ($_ARRAY[$_BOID][BTYPE]!='' and $_ARRAY[$_BOID][BTYPE] < 30) {
        if ($_ARRAY[$_BOID][BIND]!='804' and $_ARRAY[$_BOID][BIND]!='805' ) {
            $xml.= '<PAGE>'.intval($_ARRAY[$_BOID][WBPAGES]-4).'</PAGE>';
        } else {
            $xml.= '<PAGE>'.intval($_ARRAY[$_BOID][WBPAGES]-2).'</PAGE>';
        }
    } else {
        $xml.= '<PAGE></PAGE>';
    }
    $xml.= '<COVER_COLOR></COVER_COLOR>';
    $xml.= '<POINT_STORE></POINT_STORE>';
    switch ($_ARRAY[$_BOID][BOPAYTYPE]) {
        case '1':
            $card_status=$this->search_card($_ARRAY[$_BOID][BOPAYDATA]);
            if ($card_status) {
                $PAY_KIND="永豐信用卡";
            } else {
                $PAY_KIND="信用卡";
            }
            break;
        case '2':
            $PAY_KIND="優惠券";
            break;
        case '3':
            $PAY_KIND="ATM轉帳";
            break;
        case '4':
            $PAY_KIND="月結";
            break;
        case '5':
            if ($_ARRAY[$_BOID][WBFLOW]=='3') {
                $PAY_KIND="月結";
            } else {
                $PAY_KIND="虛擬ATM轉帳";
            }
            break;
        default:
            $PAY_KIND="";
            break;
    }
    $xml.= '<PAY_KIND>'.$PAY_KIND.'</PAY_KIND>';
    if ($_ARRAY[$_BOID][BOINVTYPE]=='25' or $_ARRAY[$_BOID][BOINVTYPE]=='26') {
        $xml.= '<eIVC>Y</eIVC>';  //是否為電子發票
        $iden= $_ARRAY[$_BOID][PICODE];
        $xml.= '<eIVC_iden>'.$iden.'</eIVC_iden>'; //電子發票識別碼
        if ($_ARRAY[$_BOID][BOINVTYPE]=='26') {
            $xml.= '<donate>Y</donate>'; //捐贈
        } else {
            $xml.= '<donate>N</donate>'; //不捐贈
        }
    } else {
        $xml.= '<eIVC>N</eIVC>';  //是否為電子發票
        $xml.= '<eIVC_iden></eIVC_iden>'; //電子發票識別碼
        if ($_ARRAY[$_BOID][BOINVTYPE]=='21') { //二聯式捐贈
            $xml.= '<donate>Y</donate>'; //是否捐贈
        } else {
            $xml.= '<donate>N</donate>'; //是否捐贈
        }
    }
    //設計師欄位
    if ($_ARRAY[$_BOID][BOPRICEDSN]==0) {
        $xml.= '<DS_NO></DS_NO>';
    } else {
        $xml.= '<DS_NO>'.$_ARRAY[$_BOID][DUERP].'</DS_NO>';
    }
    $xml.= '<DS_AMT>'.$_ARRAY[$_BOID][BOPRICEDSN].'</DS_AMT>';
    $xml.= '<DS_NO2></DS_NO2>';  //學校分紅 Tag 先開
    $xml.= '<DS_AMT2>0</DS_AMT2>';
    //名片類判斷是否要給合併ID做處理
    $s_result=$this->check_same_receive($_ARRAY[$_BOID][UID]);
    if ($_ARRAY[$_BOID][GROUPID]!='') {
        $xml.= '<merge_id>'.$_ARRAY[$_BOID][GROUPID].'</merge_id>';
    //有設定要合併運送的名片公司要給對應的統編當作Merge_id給ERP那邊去做判斷合併
    } elseif (($_ARRAY[$_BOID][BTYPE]=='83' or $_ARRAY[$_BOID][BTYPE]=='84') and $s_result[status]) {
        $xml.= '<merge_id>'.$s_result[mergeid].'</merge_id>';
    } else {
        $xml.= '<merge_id></merge_id>';
    }
    //群組ID提供給ERP去做地址判斷合併
    $xml.='<ord_groupID>'.$s_result['groupid'].'</ord_groupID>';
    if ($_ARRAY[$_BOID][BTYPE] < 85 and $_ARRAY[$_BOID][BTYPE] > 82) {
        $tmp_str='';
        if ($_ARRAY[$_BOID][CFILE][0]!='') {
            $tmp_str='';
            foreach ($_ARRAY[$_BOID][CFILE] as $c_key => $c_value) {
                if ($tmp_str=='') {
                    $tmp_str='X'.substr($c_value,1,2)."_".$_ARRAY[$_BOID][CNUM][$c_key];
                } else {
                    $tmp_str.=",".'X'.substr($c_value,1,2)."_".$_ARRAY[$_BOID][CNUM][$c_key];
                }
            }
        }
        $xml.= '<MEMO3>'.$tmp_str.'</MEMO3>';
    } else {
        $xml.= '<MEMO3></MEMO3>';
    }
    //單雙面計算
    $S_FACE=0;
    $D_FACE=0;
    if ($_ARRAY[$_BOID][FACID]==10 or $_ARRAY[$_BOID][BTYPE]=="30" or $_ARRAY[$_BOID][BTYPE]=="31" or $_ARRAY[$_BOID][BTYPE]=="32" or $_ARRAY[$_BOID][BTYPE]=="33") {
        switch ($_ARRAY[$_BOID][BTYPE]) {
            case 17://21
            case 22://B5直
            case 23://B5橫
            case 28://A4直
            case 29://A4橫
                switch ($_ARRAY[$_BOID][BIND]) {
                    case 801:
                    case 802:
                        $D_FACE=ceil(($single_double-4)/2/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 804:
                        //$S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id.",".(ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]+2)."_127";
                        $S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 805:
                        //$S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id.",".($_ARRAY[$_BOID][BONUM]*2)."_127";
                        $S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                }
                break;
            case 20://A3直
            case 21://A3橫
            case 24://B4直
            case 25://B4橫
                switch ($_ARRAY[$_BOID][BIND]) {
                    case 801:
                    case 802:
                        $D_FACE=ceil(($single_double-4)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 804:
                        //$S_FACE=($single_double-2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id.",".(($single_double-2)*$_ARRAY[$_BOID][BONUM]+2)."_127";
                        $S_FACE=($single_double-2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 805:
                        //$S_FACE=($single_double-2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id.",".($_ARRAY[$_BOID][BONUM]*2)."_127";;
                        $S_FACE=($single_double-2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                }
                break;
            case 26://A5直
            case 27://A5橫
                switch ($_ARRAY[$_BOID][BIND]) {
                    case 801:
                    case 802:
                        $D_FACE=ceil(($single_double-4)/4/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 804:
                        //$S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id.",".(ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]+2)."_127";
                        $S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 805:
                        //$S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id.",".(2*$_ARRAY[$_BOID][BONUM])."_127";
                        $S_FACE=ceil(($single_double-2)/2)*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                }
                break;
            case 30:
            case 31://桌曆
                switch ($single_double) {
                    case 26:
                    case 32:
                        $S_FACE=5*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 52:
                    case 64:
                        $D_FACE=5*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                }
                break;
            case 32://掛曆
            case 33:
                $D_FACE=(14*$_ARRAY[$_BOID][BONUM])."_".$paper_id;
                break;
            case 38://直式掛勾掛曆
            case 39:
                $S_FACE=(13*$_ARRAY[$_BOID][BONUM])."_".$paper_id;
                break;
            case 35: //A5筆記本(厚)
            case 55: //A5筆記本(薄)
                if ($_ARRAY[$_BOID][BTYPE]=='55') {
                    $S_FACE=($_ARRAY[$_BOID][BONUM]*2)."_125";
                } else {
                    //$S_FACE=($_ARRAY[$_BOID][BONUM]*2)."_125,".($_ARRAY[$_BOID][BONUM]*2)."_127";
                    $S_FACE=($_ARRAY[$_BOID][BONUM]*2)."_125";
                }
                $D_FACE=($_ARRAY[$_BOID][BONUM]*2*100)."_".$BODY_PAPER;
                break;
            case 36: //A6筆記本(厚)
            case 56: //A6筆記本(薄)
                if ($_ARRAY[$_BOID][BTYPE]=='56') {
                    $S_FACE=($_ARRAY[$_BOID][BONUM]*4)."_125";
                } else {
                    //$S_FACE=($_ARRAY[$_BOID][BONUM]*4)."_125,".($_ARRAY[$_BOID][BONUM]*4)."_127";
                    $S_FACE=($_ARRAY[$_BOID][BONUM]*4)."_125";
                }
                $D_FACE=($_ARRAY[$_BOID][BONUM]*100*4)."_".$BODY_PAPER;
                break;
            case 77://明信片
                $D_FACE=1*$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                break;
            case 82://數位名片
            case 83://名片
            case 84:
                switch ($_ARRAY[$_BOID][FACID]) {
                    case 10:
                        switch ($single_double) {
                            case 2:
																//2015/09/16 Arvin 例外判斷富邦識別證的紙張算法
																if ($_ARRAY[$_BOID][PTE_NO]=='200527M005000') {
																		$S_FACE=ceil($_ARRAY[$_BOID][BONUM]/21)."_".$paper_id;
																} else {
																		$S_FACE=$_ARRAY[$_BOID][BONUM]."_".$paper_id;
																}
                                break;
                            case 4:
                                $D_FACE=$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                                break;
                            case 40:
                                $S_FACE=$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                                break;
                            case 80:
                                $D_FACE=$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                                break;
                        }
                        break;
                }
                break;
            case 85:
            case 86:
                switch ($single_double) {
                    case 2:
                        $S_FACE=($_ARRAY[$_BOID][BONUM]/2)."_".$paper_id;
                        break;
                    case 4:
                        $D_FACE=($_ARRAY[$_BOID][BONUM]/2)."_".$paper_id;
                        break;
                }
                break;
            case 87:
            case 88:
                switch ($single_double) {
                    case 2:
                        $S_FACE=($_ARRAY[$_BOID][BONUM]/4)."_".$paper_id;
                        break;
                    case 4:
                        $D_FACE=($_ARRAY[$_BOID][BONUM]/4)."_".$paper_id;
                        break;
                }
                break;
            case 89:
            case 90:
            case 91:
            case 92:
                switch ($single_double) {
                    case 2:
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                    case 4:
                        $D_FACE=$_ARRAY[$_BOID][BONUM]."_".$paper_id;
                        break;
                }
                break;
            case 112://圓蓋隨身瓶
                $S_FACE=ceil($_ARRAY[$_BOID][BONUM]/2)."_".$paper_id.",".$_ARRAY[$_BOID][BONUM]."_XWD0100002";
                break;
            case 113://拉環隨身瓶
                $S_FACE=ceil($_ARRAY[$_BOID][BONUM]/2)."_".$paper_id.",".$_ARRAY[$_BOID][BONUM]."_XWD0100001";
                break;
            case 114://PLA環保隨身瓶
                $S_FACE=ceil($_ARRAY[$_BOID][BONUM]/2)."_".$paper_id.",".$_ARRAY[$_BOID][BONUM]."_XWD0100003";
                break;
						/*		
            case 115:
            case 116:
                if (in_array("792",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0100004";
                }
                if (in_array("790A",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    if ($S_FACE!='') {
                        $S_FACE.=",".$_ARRAY[$_BOID][BONUM]."_XWA0300002";
                    } else {
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0300002";
                    }
                }
                break;
            case 117:
            case 118:
                if (in_array("792",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0100003";
                }
                if (in_array("793A5",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    if ($S_FACE!='') {
                        $S_FACE.=",".$_ARRAY[$_BOID][BONUM]."_XWA0200003";
                    } else {
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0200003";
                    }
                }
                if (in_array("790A",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    if ($S_FACE!='') {
                        $S_FACE.=",".$_ARRAY[$_BOID][BONUM]."_XWA0200003";
                    } else {
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0200003";
                    }
                }
                break;
            case 119:
            case 120:
                if (in_array("792",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0100002";
                }
                if (in_array("793A4",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    if ($S_FACE!='') {
                        $S_FACE.=",".$_ARRAY[$_BOID][BONUM]."_XWA0200002";
                    } else {
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0200002";
                    }
                }
                if (in_array("790A",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    if ($S_FACE!='') {
                        $S_FACE.=",".$_ARRAY[$_BOID][BONUM]."_XWA0300002";
                    } else {
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0300002";
                    }
                }
                break;
            case 121:
            case 122:
                if (in_array("792",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0100001";
                }
                if (in_array("793A3",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    if ($S_FACE!='') {
                        $S_FACE.=",".$_ARRAY[$_BOID][BONUM]."_XWA0200001";
                    } else {
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0200001";
                    }
                }
                if (in_array("790A",$_ARRAY[$_BOID][PUZZLE_BOX])) {
                    if ($S_FACE!='') {
                        $S_FACE.=",".$_ARRAY[$_BOID][BONUM]."_XWA0300001";
                    } else {
                        $S_FACE=$_ARRAY[$_BOID][BONUM]."_XWA0300001";
                    }
                }
                break;
								*/
            case ($_ARRAY[$_BOID][BTYPE]>='125' AND $_ARRAY[$_BOID][BTYPE]<='129'): //喜帖
                $D_FACE=($_ARRAY[$_BOID][BONUM]/2)."_".$paper_id;
                $S_FACE="1_".$paper_id;
                break;
            case 124://名信片喜帖
                $D_FACE=ceil($_ARRAY[$_BOID][BONUM]/8)."_".$paper_id;
                $S_FACE="1_".$paper_id;
                break;
            case 206://小平裝100張便利貼
                $S_FACE=($_ARRAY[$_BOID][BONUM]/3)."_".$papercover_id;
                break;
            case 207://大平裝100張便利貼
                $S_FACE=($_ARRAY[$_BOID][BONUM]/2)."_".$papercover_id;
                break;
            case 208://精裝100張便利貼
                $S_FACE=($_ARRAY[$_BOID][BONUM]/4)."_".$papercover_id;
                break;
            case 209://平裝五色便利貼
                $S_FACE=ceil($_ARRAY[$_BOID][BONUM]/12)."_".$papercover_id;
                break;
            case 212://磁鐵
            case 213:
            case 214:
                $S_FACE=ceil($_ARRAY[$_BOID][BONUM]/3)."_".$paper_id;
                break;
        }
        switch ($_ARRAY[$_BOID][BTYPE]) {
            case 17:
            case 22:
            case 26:
            case 27:
            case 28:
                if ($_ARRAY[$_BOID][BIND]=='801') {
                    $S_FACE="1_".$papercover_id;
                }
                break;
        }
        $VOW_ARRAY[$_BOID][S_FACE]=$S_FACE;
        $VOW_ARRAY[$_BOID][D_FACE]=$D_FACE;
    }
    $xml.= '<S_FACE>'.$S_FACE.'</S_FACE>';
    $xml.= '<D_FACE>'.$D_FACE.'</D_FACE>';
    $xml.= '</ORDER_CUS>';
    $xml.= '</TRAN>';
    $xml.= '</ns0:TRANORD>';
?>