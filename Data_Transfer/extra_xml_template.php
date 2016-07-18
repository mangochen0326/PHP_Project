<?
    /***********************************************************************************************
    *  2011/08/30 Code by Arvin
    *  額外付款需開訂單的資料。Ex:精美包裝盒
    *  2011/09/14 Arvin 包裝盒不開VBB訂單，用MANUF_COMP = YFP2來給琇雲判斷
    *  2011/12/06 Arvin 增加桌曆包裝紙盒
    *  2011/12/22 Arvin 非兌換券產品且非開立三聯式發票的訂單及兌換券但有額外購買包裝盒的額外訂單，轉ERP的客戶代號寫入922195
    *  2012/01/10 Arvin 約交日抓xml_template所計算出來的
    *  2012/07/12 Arvin 包裝紙盒版號異動
    *  2013/08/14 Arvin 重新調整配送方式ERP代號對應
    *  2013/08/20 Arvin 判斷另開訂單時若沒有合併交寄資訊，要塞資料進去讓ERP那邊去做關聯
    *  2013/08/22 Arvin 增加YFP TAG 固定寫入0讓新的LOG判斷正常
    *  2013/10/15 Arvin 判斷若為兌換券有包裝盒但總金額是0元該包裝盒就是贈送的，額外訂單類別要開內部自用
    *  2013/11/15 Arvin 額外訂單類別改成要開打樣訂單 (贈品)
    *  2014/10/30 Arvin 增加客戶比例、客戶CUS_PO欄位
    *  2015/01/22 Arvin XML V16版 增加發票廠別、出貨倉別
		*  2015/04/30 Arvin 增加2個地方ORD_SUM TAG，記錄未稅總價
    ***********************************************************************************************/

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml.= '<ns0:TRANORD xmlns:ns0="http://WTPOrderSchemas.TRANORD">';
    $xml.= '<SEND_DEST>YFP</SEND_DEST>';
    $xml.= '<DOC_SRC>W2P</DOC_SRC>';
    $xml.= '<DOC_TYPE>'.$INI_SET[mode].'</DOC_TYPE>';
    $xml.= '<DOC_ID>'.$_BOID.'A</DOC_ID>';
    $xml.= '<TRAN_CNT>1</TRAN_CNT>';
    $xml.= '<ORG_NO>V</ORG_NO>';
    $xml.= '<XML_VERSION>17</XML_VERSION>';//XML版本標記
    $xml.= '<TRAN>';
    $xml.= '<ORDER_MANUF>';
    $xml.= '<MANUF_COMP>YFP2</MANUF_COMP>';
    $xml.= '<YFP>0</YFP>';
    $xml.= '<PLATE_ID>'.$DATETIME.sprintf("%'02s",$_STEP).'A</PLATE_ID>';
    $xml.= '<ORDER_CUS_CNT>1</ORDER_CUS_CNT>';
    $xml.= '<MANUF_QTY>'.$_ARRAY[$_BOID][BONUM].'</MANUF_QTY>';

    $xml.= '<MANUF_PRI>'.round(($TMP_ARRAY["888"]/1.05),4).'</MANUF_PRI>';
    $xml.= '<MANUF_PRI2>'.$TMP_ARRAY["888"].'</MANUF_PRI2>';
    $xml.= '<MANUF_REL_DTE>'.date("Y/m/d",$fac_sendday).'</MANUF_REL_DTE>';
    $xml.= '</ORDER_MANUF>';

    $xml.= '<ORDER_YFP>';
    $xml.= '<CREATE_ORD>N</CREATE_ORD>';//是否拆帳開關，預設N
    $xml.= '<ORD_PRI>0</ORD_PRI>';
		$xml.= '<ORD_SUM>0</ORD_SUM>';//未稅總價
    $xml.= '<TOL_SUM>0</TOL_SUM>';
    $xml.= '</ORDER_YFP>';

    $xml.= '<ORDER_CUS>';
    $xml.= '<MEMB_ID>'.$_ARRAY[$_BOID][UNAME].'</MEMB_ID>';
    $show_boinvtitle = str_replace($search, $replace, $_ARRAY[$_BOID][BOINVTITLE]);    //轉換特殊字元
    $xml.= '<INV_NA><![CDATA['.$show_boinvtitle.']]></INV_NA>';
    $xml.= '<INV_NO>'.$_ARRAY[$_BOID][BOVATNO].'</INV_NO>';
    $xml.= '<CUS_IVC_KIND>1</CUS_IVC_KIND>';

    if ($_ARRAY[$_BOID][UDNAME]=='') {
        $show_udname=$_ARRAY[$_BOID][BORNAME];
    } else {
        $show_udname=$_ARRAY[$_BOID][UDNAME];
    }

    if ($_ARRAY[$_BOID][BOINVTYPE]=='2' or $_ARRAY[$_BOID][BOINVTYPE]=='21') {
       //$xml.= '<CUS_NO>922195</CUS_NO>';
       //判斷有輸入抬頭就不帶入客戶編號
        if ($show_boinvtitle!='') {
            $xml.= '<CUS_NO></CUS_NO>';
            $show_udname = $show_boinvtitle;
        } else {
            $xml.= '<CUS_NO>922195</CUS_NO>';
            $show_udname = str_replace($search, $replace, $show_udname);    //轉換特殊字元
        }

    } else {
       $xml.= '<CUS_NO></CUS_NO>';
       $show_udname = str_replace($search, $replace, $show_udname);    //轉換特殊字元
    }
    $xml.= '<CUS_NA><![CDATA['.$show_udname.']]></CUS_NA>';
    $xml.= '<CUS_PO></CUS_PO>';
    $xml.= '<COUNTRY_CODE></COUNTRY_CODE>';
    $xml.= '<CUS_ZIP>'.$_ARRAY[$_BOID][UAID].'</CUS_ZIP>';
    if ($_ARRAY[$_BOID][UADDR]=='') {
        $show_addr = $_ARRAY[$_BOID][BORADDR];
    } else {
        $show_addr=$_ARRAY[$_BOID][UADDR];
    }
    $show_addr = str_replace($search, $replace, $show_addr);    //轉換特殊字元
    $xml.= '<CUS_ADDR><![CDATA['.$show_addr.']]></CUS_ADDR>';
    //抓所屬業務
    $tmp_ary=$this->search_sales($_BOID);
    $xml.= '<SAL_NO>'.$tmp_ary[sal_no].'</SAL_NO>';
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
        $xml.= '<ORD_QTY>'.$T_BONUM.'</ORD_QTY>';
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
    $xml.= '<WTP_ORDNO>'.$_BOID.'A</WTP_ORDNO>';
    //2013/10/15 判斷若為兌換券有包裝盒但總金額是0元該包裝盒就是贈送的，額外訂單類別要開內部自用
    //2013/11/15 額外訂單類別改成要開打樣訂單 (贈品)
    if (trim($_ARRAY[$_BOID][BOPRICE])==0) {
        $xml.='<INTERNAL_KIND>D</INTERNAL_KIND>';
    }
    $xml.= '<STOCK>01</STOCK>';
    $xml.= '<CMP_NO>V</CMP_NO>'; //出貨倉別
    $xml.= '<INV_ORGNO>V</INV_ORGNO>';//發票廠別
    $xml.= '<STOCK_ORDNO></STOCK_ORDNO>';
    $PTE_NO='';
    //薄蝴蝶 > 46頁厚蝴蝶 > 16頁用厚的包裝盒
    if (($_ARRAY[$_BOID][BIND]=='804' and $_ARRAY[$_BOID][WBPAGES]>'18') or ($_ARRAY[$_BOID][BIND]=='805' and $_ARRAY[$_BOID][WBPAGES]>'48')) {
        switch ($_ARRAY[$_BOID][BTYPE]) {
            case 22:
            case 23:
            case 28:
            case 29:
                $PTE_NO='YBFC004000000';
                break;
            case 17:
            case 26:
            case 27:
                $PTE_NO='YBFC006000000';
                break;
            case 20:
            case 21:
            case 24:
            case 25:
                $PTE_NO='YBFC002000000';
                break;
        }
    //桌、掛曆
    } elseif ($_ARRAY[$_BOID][BTYPE]=='31' or $_ARRAY[$_BOID][BTYPE]=='30') {
        $PTE_NO='YBFA002000000';
    //明信片
    } elseif ($_ARRAY[$_BOID][BTYPE]=='77') {
        $PTE_NO='211341MD80000';
    //薄蝴蝶 <=46頁厚蝴蝶 <=16頁用薄的包裝盒
    } else {
        switch ($_ARRAY[$_BOID][BTYPE]) {
            case 22://B5
            case 23:
            case 28://A4
            case 29:
                $PTE_NO='YBFC005000000';
                break;
            case 17://21
            case 26://A5
            case 27:
                $PTE_NO='YBFC007000000';
                break;
            case 20://A3
            case 21:
            case 24://B4
            case 25:
                $PTE_NO='YBFC003000000';
                break;
        }
    }
    $xml.= '<PTE_NO>'.$PTE_NO.'</PTE_NO>';
    $xml.= '<WTP_PTECODE></WTP_PTECODE>';
    $xml.= '<WTP_PTENA>'.$TMP_ARRAY[PPNAME].'</WTP_PTENA>';
    $xml.= '<ORD_QTY>'.$_ARRAY[$_BOID][EXTRA_NUM].'</ORD_QTY>';
    $xml.= '<CUS_QTY></CUS_QTY>'; //客戶顯示數量
    $xml.= '<CUS_UNIT></CUS_UNIT>'; //客戶顯示單位
    $xml.= '<CUS_PERCENT></CUS_PERCENT>'; //單位換算比率
    $xml.= '<WK_QTY>'.$_ARRAY[$_BOID][EXTRA_NUM].'</WK_QTY>';
    $xml.= '<ORD_PRI>'.round($TMP_ARRAY["28A015"]/1.05,4).'</ORD_PRI>';
		$xml.= '<ORD_SUM>'.round($TMP_ARRAY["28A015"]/1.05,4)*$_ARRAY[$_BOID][EXTRA_NUM].'</ORD_SUM>';//未稅總價
    $xml.= '<TOL_SUM>'.trim($_ARRAY[$_BOID][BOPRICE]).'</TOL_SUM>';
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
    $xml.= '<WTP_FILENAME_BODY></WTP_FILENAME_BODY>';
    $xml.= '<WTP_FILENAME_COVER></WTP_FILENAME_COVER>';
    $xml.= '<WTP_FILENAME_WING></WTP_FILENAME_WING>';
    $xml.= '<WTP_VOWNA>'.$TMP_ARRAY[PPNAME].'</WTP_VOWNA>';
    $xml.= '<MEMO>'.$_BOID.'額外訂單</MEMO>';
    $xml.= '<PAGE></PAGE>';
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
        case '3':
            $PAY_KIND="ATM轉帳";
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
    if ($_ARRAY[$_BOID][GROUPID]!='') {
        $xml.= '<merge_id>'.$_ARRAY[$_BOID][GROUPID].'</merge_id>';
    } else {
        $xml.= '<merge_id></merge_id>';
    }
    //群組ID提供給ERP去做地址判斷合併
    $xml.='<ord_groupID></ord_groupID>';
    $xml.= '</ORDER_CUS>';
    $xml.= '</TRAN>';
    $xml.= '</ns0:TRANORD>';
?>