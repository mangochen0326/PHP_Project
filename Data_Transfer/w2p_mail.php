<?
		/***********************************************************************************************
		*  WTP 批次轉檔主程式 w2p_trans.php
		*  設定檔：       w2p_trans.ini.php
		*  合板產品分支： w2p_array.php
		*  photo產品分支：vow_array.php
		*  XML 樣版：     xml_template.php
		*  轉檔清冊信件： w2p_mail.php
		*  物件         : class.php
		*  2010/07/05 Code by Arvin
		*  2010/11/18 Arvin 修改T-shrit轉檔清冊
		*  2010/12/8  Arvin BOPTION欄位從PORDERDATA移至 PORDER 內
		*  2011/01/06 Arvin 轉當清冊增加 ERP訂單編號、生產工廠及約交日欄位，並把平台編號移到最後
		*  2011/01/18 Arvin 名片轉檔增加各PDF數量表，以利工廠核對盒數
		*  2011/01/19 Arvin 修正清冊依據不同產品將表格分開
		*  2011/02/08 Arvin Template強制轉換收件者資料
		*  2011/03/09 Arvin 增加工作單寄送
		*  2011/03/22 Arvin 增加明信片工作單寄送及美術卡名稱轉換對應
		*  2011/04/07 Arvin 修改工作單及轉檔清冊格式
		*  2011/05/24 Arvin 修改工作清冊寄送方式，合板產品與自製產品分開寄送
		*  2011/05/31 Arvin T-shrit統一回萬華更換包裝後再寄送給客戶，所以外部清冊要替換掉收件者資訊
		*  2011/06/03 Arvin 增加B5直、21正方封面改顯示 Indigo印刷
		*  2011/06/17 Arvin 增加加工條件資料於轉檔清冊備註顯示
		*  2011/07/20 Arvin 增加判斷BOINVTYPE 發票型態，若為21則在BOMEMO內加上二聯式捐贈，25為電子發票
		*  2011/07/21 Arvin 若名片有難字則在轉檔清冊內的備註註明。
		*  2011/07/26 Arvin 判斷優惠券產品折價後為0元時顯示兌換券，自取顯示在工作單的備註
		*  2011/08/04 Arvin 判斷就算BOINVTYPE 為21或25，若是 0 元兌換券也不顯示
		*  2011/08/04 Arvin 修改opday抓加工日期的方式，修改區分為工廠約交日與消費者約交日，工作單、轉檔清冊是工廠約交日
		*  2011/08/15 Arvin 修改工作單同紙別為同一區塊，再依據裝訂、產品做區分
		*  2011/09/13 Arvin A3作品集內頁都要上亮光
		*  2011/09/15 Arvin 增加企業DM、帳單的轉檔清冊
		*  2011/09/26 Arvin 增加判斷奇哥名片轉檔清冊備註【發票隨貨送】
		*  2011/10/17 Arvin T-shirt收件者資訊改回原本的收件者，不再轉成生管，由御牧出貨
		*  2011/11/04 Arvin A5直不管任何裝訂方式都改indigo印，且紙別換銅板貼紙
		*  2011/11/07 Arvin iphone殼轉檔程式更新
		*  2011/11/15 Arvin 攝影展活動截止移除轉檔清冊額外判斷
		*  2011/11/21 Arvin 自製轉檔清冊輸出Excel資料寫入PORDEREPORT
		*  2011/12/06 Arvin 增加老麥攝影轉檔額外備註說明
		*  2011/12/13 Arvin 修改iphone保護殼把加工條件加入外包清冊中的產品類型區分 iphone4 or iphone4s
		*  2011/12/14 Arvin 製作工作單時一併刪除拼好版的檔案，降低空間的使用率
		*  2012/01/18 Arvin 增加合併寄送的訂單顯示
		*  2012/02/15 Arvin T-shirt工廠清冊增加備註顯示是否為合作方案(團購網)-兌換券
		*  2012/03/12 Arvin 增加工作單條碼，並移除欄位發票號碼、平台編號
		*  2012/03/13 Arvin 外包產品轉檔清冊增加條碼欄位移除作品名稱欄位
		*  2012/03/20 Arvin 增加貼紙廠商出貨日計算，並顯示在工廠轉檔清冊內
		*  2012/03/30 Arvin 增加神腦試作的Coupon碼判斷
		*  2012/04/25 Arvin 將零售業轉檔清冊另外獨立出來，不包含在外包轉檔清冊內
		*  2012/04/30 Arvin 吸水杯墊轉檔處理
		*  2012/05/02 Arvin 抓取合併交寄主訂單資訊改用get_mainboid func 處理
		*  2012/05/16 Arvin 無框畫工作單及轉檔清冊處理
		*  2012/05/25 Arvin 增加蝴蝶裝給生產工廠清單
		*  2012/07/02 Arvin B4蝴蝶裝增加註記上亮光
		*  2012/07/05 Arvin 拼圖變更為自製項目，但還是要寄送外包轉檔清冊給英傑特
		*  2012/08/03 Arvin 增加便利貼產品處理
		*  2012/08/24 Arvin 增加購物車判斷主訂單處理
		*  2012/08/29 Arvin 增加桌曆轉依您印的處理判斷
		*  2012/08/29 Arvin 將原先吸水杯墊會轉FACID=10的修改拿掉。吸水杯墊生產單位是英傑特與依您印，依您印就等於自製。xml 會多開VB訂單出來
		*  2012/10/01 Arvin 作品集只要蝴蝶裝、特銅250P 的通通上光
		*  2012/10/04 Arvin 移除情人節送熊的資訊
		*  2012/10/11 Arvin 便利貼封面紙別設定
		*  2012/10/11 Arvin 複寫聯單處理上正式機
		*  2012/10/16 Arvin 吸水杯墊轉回由永豐生產
		*  2012/11/06 Arvin 增加判斷總金額贈送禮品
		*  2012/11/22 Arvin 增加掛曆轉依您印處理流程
		*  2012/11/23 Arvin 移除每次轉檔備註送抱枕改由後台彙整判斷
		*  2012/11/29 Arvin 增加Iphone5處理
		*  2013/01/21 Arvin B4直平裝增加殼衣大圖相紙260，冷表；複寫聯單增加流水號的座標資訊及起號
		*  2013/03/18 Arvin 調整原先的檔案處理流程，增加複寫聯單的流水號位置座標及起號；自製名片轉版號給ERP;依您印自製名片處理
		*  2013/04/25 Arvin 圓形鏡盒處理
		*  2013/05/03 Arvin 喜帖雙霧P 紙別名稱轉換一級卡，並固定加入加工條件 雙霧P
		*  2013/05/07 Arvin 增加判斷台中商業銀行備註【發票隨貨送】
    *  2013/05/23 Arvin 增加厚筆記本加工條件註明。
    *  2013/06/07 Arvin 增加FACID=25(伍泰)零售外包商處理
    *  2013/06/11 Arvin 筆記本在工作單埋入Tag在後台彙整時要顯示封面封底用
    *  2013/06/19 Arvin 增加無痕壁掛貼、靜電停車證的一套幾個的數量提示
    *  2013/06/20 Arvin 新竹物流-科谷實業mail通知表頭內容拆開。
    *  2013/07/02 Arvin 直噴機產品改為外包訂單
		*  2013/07/23 Arvin 無蓋ipad mini加送觸控筆+試鏡布
		*  2013/07/24 Arvin 增加判斷佳和實業備註【發票隨貨送】
		*  2013/08/06 Arvin 外包工廠的轉檔清冊，生管及需要通知的人員改為密件副本寄送
		*  2013/08/06 Arvin 增加木質面紙盒及samsung S3轉檔清冊
		*  2013/09/12 Arvin 判斷特殊客戶直接修改發票類別不再保持月結但備註發票隨貨送
		*  2013/09/16 Arvin 變更紙別對應抓取的方式
		*  2013/10/09 Arvin 增加磁鐵工作單標註
		*  2013/10/25 Arvin 增加埋入工作單隨身瓶彙整預覽圖Tag，京城銀行轉檔清冊與日常的訂單拆分開來分別寄送
		*  2013/11/19 Arvin 周年慶寄送小鴨
		*  2013/12/03 Arvin 喜帖燙金資訊增加寄送給依您印，燙金黑板檔案改用FTP方式傳送
		*  2013/12/06 Arvin 增加天威外包商處理
		*  2013/12/12 Arvin 增加記錄用紙數量PORDERBOM資料表
		*  2014/01/23 Arvin 增加喜來登名片尺寸備註
		*  2014/05/23 Arvin 增加3D公仔轉檔清冊
		*  2014/05/26 Arvin 修改拼圖及隨身瓶紙張顯示問題。修正物料個數被一併加總到紙張數量上的bug
		*                   增加T-shirt廠商清冊約交日資訊
		*                   貼紙廠商國田看到的約交日為客戶約交日扣2天
		*  2014/07/14 Arvin 增加名信片喜帖處理
		*  2014/07/21 Arvin 白紗名片命名X01 => X001 變更為3碼
		*  2014/08/04 Arvin 3D公仔轉檔清冊增加預覽圖
		*  2014/08/19 Arvin 判斷工研院活動桌掛曆不寄出(移到class.php去判斷)
		*  2014/09/09 Arvin 增加彩色TEE處理
		*  2014/10/20 Arvin 增加直式掛勾掛曆處理
		*  2014/10/07 Arvin 增加外包商嘉記興業處理
		*  2014/11/07 Arvin 增加轉單不給檔的直落產品判斷
		*  2014/10/11 Arvin 增加桌曆底版寄發通知依您印
		*  2014/11/12 Arvin 新版厚無框畫處理
		*  2014/11/13 Arvin VDP處理
    *  2014/12/05 Arvin 工商日誌處理
    *  2015/03/12 Arvin 增加icash生產機台及工作單設定
		*  2015/04/08 Arvin 紙膠帶編輯器產品跟L夾產品處理
		*  2015/04/30 Arvin 拼圖除了A3尺寸外其餘都改依您印製作
		*  2015/04/30 Arvin 基本版外包商轉檔清冊增加訂購單位的資訊，非企業用戶該欄位就是空白的
		*  2015/05/05 Arvin 外商商轉檔清冊單位抓w2product40內的PRTUNIT來顯示
		*  2015/05/19 Arvin 依您印的產品分類一種產品寄一封清單，燙金算一種(喜帖跟桌曆)
		*  2015/06/04 Arvin 增加富邦金控名片模板(d5441a57)特殊加工判斷
		*  2015/09/08 Arvin 塑料面紙包
		/
    /**********************************************************************************************************************************
  	 *  工作單
		 **********************************************************************************************************************************/
    $v_num=1;
    $html="";

		$PAPER_MAP["PPID"]["150"]=array('77','82','125','126','127','128','129'); // 美術紙名稱轉換陣列
		$PAPER_MAP["NAME"]["150"]="禾風原卡280P";

		$PAPER_MAP["PPID"]["692"]=array('125','126','127','128','129'); //雙面霧P名稱轉換陣列
		$PAPER_MAP["NAME"]["692"]="一級卡250P";


    $mailpath=$ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME;
		if (!is_dir($mailpath)) {
				mkdir($mailpath);
		}
    foreach ($LOOP_ARRAY as $value) {
        $S_BIND="";
        $S_PAGES="";
        $S_WORK="";
        $C_PAPER="";
        $S_PS="";
				$S_BTYPE=$print_array[$value][BTYPE];

        //把加工條件加在備註裡
        $work_process="";
        if (is_array($print_array[$value][WORKNAME])) {
            foreach ($print_array[$value][WORKNAME] as $w_value) {
                if ($work_process=='') {
                    $work_process=$w_value;
                } else {
                    $work_process.="，".$w_value;
                }
            }
            $work_process.="。";
        }
        $sub_work_process="";
        if (is_array($print_array[$value][SUBWORKNAME])) {
            foreach ($print_array[$value][SUBWORKNAME] as $s_value) {
                if ($sub_work_process=='') {
                    $sub_work_process=$s_value;
                } else {
                    $sub_work_process.="，".$s_value;
                }
            }
        }
        switch ($print_array[$value][FACID]) {
            case "10":
                $rowspan='rowspan="2"';
                $loop=2;
                switch ($S_BTYPE) {
                    case "30"://桌曆
                    case "31":
                        if ($print_array[$value]["WBPAGES"]=="32" or $print_array[$value]["WBPAGES"]=="26") {
                            $S_BIND="，單面，加工：環裝";
                        } else {
                            $S_BIND="，雙面，加工：環裝";
                        }
                        break;
										case "38"://直式掛勾掛曆
										case "39":
												$S_BIND="，單面，加工：掛勾";
												break;
                    case "77": //明信片
                        if ($work_process!='') {
                            if (stristr($work_process,"不上水光")) {
                                $S_BIND="，雙面，加工：$work_process";
                            } else {
                                $S_BIND="，雙面，加工：正面上水光，$work_process";
                            }
                        } else {
                            $S_BIND="，雙面，加工：正面上水光";
                        }
                        break;
                    case ($S_BTYPE >='142' and $S_BTYPE<='163')://無框畫
                        $S_BIND="，裝訂方式：無框畫";
                        $S_WORK="外包商(小雅)";
                        $S_PS="印刷完外包";
                        break;
										case ($S_BTYPE >='232' and $S_BTYPE<='236')://新版厚無框畫
												$S_BIND="，裝訂方式：厚無框畫";
												$S_WORK="外包商(健隆)";
												$S_PS="印刷完外包";
												break;
										case ($S_BTYPE>='250' and $S_BTYPE<='263'):
												$S_BIND="，變動資料筆數：".$print_array[$value]["VDP_COUNT"];
												break;
                    case ($S_BTYPE>='121' and $S_BTYPE<='122'): //拼圖
                        $S_WORK="外包商(英傑特)";
                        break;
										case ($S_BTYPE>='115' and $S_BTYPE<='120'): //拼圖
												$S_WORK="外包商(依您印)";
												break;
                    case "35":
                    case "36":
                        $S_WORK ="非鐵圈邊導10號圓角";
                        break;
                    case "124":case "125": case "126": case "127": case "128": case "129":
												//2013/07/17 只有金彩蝶才要加雙霧P(692一級卡=金彩蝶)
												if ($print_array[$value]["PPID"]=='692') {
														$S_BIND="，加工：雙霧P，中間壓一線";
												} else {
														$S_BIND="，加工：中間壓一線";
												}
                        break;
										case "124":
												//2013/07/17 只有金彩蝶才要加雙霧P(692一級卡=金彩蝶)
												if ($print_array[$value]["PPID"]=='692') {
														$S_BIND="，加工：雙霧P";
												}
                        break;
										case '206': //小平裝
										case '207': //大平裝
										case '209': //五色
												$S_WORK="封面紙別：銅西300P + 上光";
												break;
										case '208': //精裝
												$S_WORK="封面紙別：銅版貼紙 + 上光";
												break;
										case '82';
												//少量名片若有加工顯示在紙別下面那格
												if ($VOW_ARRAY[$value]["CUT"]!='') {
														$S_WORK=$VOW_ARRAY[$value]["CUT"];
												}
												break;
                }
                break;
            //非自製產品不需要工作單
            default:
                $loop=0;
                break;
        }
        if ($loop > 0) {
            $paper_id= $print_array[$value]["PPID"]; //紙別
            $bind    = $print_array[$value]["BIND"]; //裝訂方式
            //顯示有紙別名稱轉換名稱
						if (is_array($PAPER_MAP["PPID"]["$paper_id"])) {
								if (in_array($S_BTYPE,$PAPER_MAP["PPID"]["$paper_id"])) {
										$print_array[$value]["PPNAME"]=$PAPER_MAP["NAME"]["$paper_id"];
								}
						}
            $MAP_WORK_ARRAY[PAPER]["$paper_id"][]=$value;
            if ($bind!='') {
                $MAP_WORK_ARRAY["$paper_id"]["$bind"][]=$value;
            } else {
                $MAP_WORK_ARRAY["$paper_id"]["$S_BTYPE"][]=$value;
            }

            switch ($bind) {
                case '801':
                    $S_BIND="，裝訂方式：平裝膠裝";
                    $S_PAGES=intval($print_array[$value][WBPAGES]-4);
                    $S_WORK ="加扉頁紙(再生象牙)";
                    $C_PAPER="260相紙";
                    break;
                case '802':
                    $S_BIND="，裝訂方式：精裝膠裝";
                    $S_PAGES=intval($print_array[$value][WBPAGES]-4);
                    $S_WORK ="加扉頁紙(再生象牙)";
                    $C_PAPER="PVC貼紙";
                    break;
                case '803':
                    $S_BIND="，裝訂方式：騎馬釘";
                    break;
                case '804':
                    $S_BIND="，裝訂方式：蝴蝶頁膠裝(厚)";
                    $S_PAGES=intval($print_array[$value][WBPAGES]-2);
                    $S_WORK="裝訂含封面";
                    $C_PAPER="PVC貼紙";
                    $S_PS="外包台亞"; //備註
                    break;
                case '805':
                    $S_BIND="，裝訂方式：蝴蝶頁膠裝(薄)";
                    $S_PAGES=intval($print_array[$value][WBPAGES]-2);
                    $S_WORK="裝訂含封面";
                    $C_PAPER="PVC貼紙";
                    $S_PS="外包台亞"; //備註
                    break;
                case '780'://左裝
                    switch ($S_BTYPE) {
                        case "35": //A5加厚筆記本
                            $S_WORK.=",成品尺寸：208 x 148 mm";
                            $C_PAPER="銅西卡300P，單面霧P，裱2張厚卡";
                            break;
                        case "36": //A6加厚筆記本
                            $S_WORK.=",成品尺寸：146 x 105 mm";
                            $C_PAPER="銅西卡300P，單面霧P，裱2張厚卡";
                            break;
                        default:
                            $C_PAPER="銅西卡300P，雙面霧P";
                            break;
                    }
                    break;
                case '781': //上裝
                    switch ($S_BTYPE) {
                        case "35": //A5加厚筆記本
                            $S_WORK.=",成品尺寸：210 x 146 mm";
                            $C_PAPER="銅西卡300P，單面霧P，裱2張厚卡";
                            break;
                        case "36": //A6加厚筆記本
                            $S_WORK.=",成品尺寸：148 x 103 mm";
                            $C_PAPER="銅西卡300P，單面霧P，裱2張厚卡";
                            break;
                        default:
                            $C_PAPER="銅西卡300P，雙面霧P";
                            break;
                    }
                    break;
            }
            //自取註明在備註裡
            if (trim($print_array[$value][BORADDR])=='自取' or trim($print_array[$value][BOSEND])=='C') {
                $S_PS.="自取。";
            }
            
            for ($i_loop=0;$i_loop < $loop;$i_loop++) {
                switch ($i_loop) {
                    case "0":
                        $html ='<tr>';
                        $html.='<td '.$rowspan.'>'.$v_num.' </td>';
                         //2012/03/12 Arvin 增加工作單條碼，並移除欄位發票號碼、平台編號
                        $html.='<td '.$rowspan.'><div id="'.$print_array["$value"]["YFPBOID"].'" class="barcodeTarget"></div><canvas id="'.$print_array["$value"]["YFPBOID"].'" width="150" height="150"></canvas></td>';
                        $html.='<td '.$rowspan.'>'.$print_array["$value"]["YFPBOID"].' </td>';
                        //名片模版
                        if ($S_BTYPE=='83' or $S_BTYPE=='84') {
                            $html.='<td '.$rowspan.'><table border="1" cellpadding="0" cellspacing="0" width="100%">';
                						$html.='<tr align="center"><td width="50%">檔案</td><td width="50%">數量</td></tr>';
                						$p_ii=0;
                						foreach ($print_array["$value"]["PDF"] as $pdf_value) {
                								$html.='<tr align="center">';
                								$html.='<td>X'.$pdf_value.'</td>';
                								$html.='<td>'.$print_array["$value"]["PDF2"]["$p_ii"].'</td>';
                								$html.='</tr>';
                								$p_ii++;
                						}
                						$html.='</table></td>';
                        } else {
                            switch ($S_BTYPE) {
                                //2013/06/11 Arvin 筆記本在工作單埋入Tag在後台彙整時要顯示封面封底用
                                case ($S_BTYPE>='54' and $S_BTYPE<='57'):
                                case "35":
                                case "36":
                                    $html.='<td><span id="'.$print_array[$value]["BID"].'" class="note_cover"></span></td>';
                                    break;
                                //2013/10/25 Arvin 隨身瓶在工作單埋入Tag在後台彙整時要顯示預覽圖用
                                //2014/03/19 Arvin 便利貼在工作單埋入Tag在後台彙整時要顯示預覽圖用
                                //2015/03/12 Arvin icash在工作單埋入Tag在後台彙整時要顯示預覽圖用
                                case ($S_BTYPE>='112' and $S_BTYPE<='114'):
                                case ($S_BTYPE>='206' and $S_BTYPE<='209'):
                                case "81": //icash卡
																case "218"://馬克杯
                                    if ($print_array[$value][WBFLOW]==1) {
                                        $html.='<td><span id="'.$print_array[$value]["BID"].'_1" class="bottle_cover"></span></td>';
                                    } else {
                                        $html.='<td><span id="'.$print_array[$value]["BID"].'_2" class="bottle_cover"></span></td>';
                                    }
                                    break;
                                default:
                                     $html.='<td>'.$print_array[$value]["BODY"].'</td>';
                                    break;
                            }
                        }
                         //刪除檔案
                        $delfile=$GB_BOOKPATH_PDF."spool\\".$print_array[$value]["BODY"];
                        @unlink($delfile);
                        $html.='<td '.$rowspan.'>'.$print_array["$value"]["TITLE"].'</td>';
                        $html.='<td '.$rowspan.'>'.date("Y-m-d H:i:s",$print_array["$value"]["BOTIME"]).'</td>';
                        $S_PAPER=0;
                        $D_PAPER=0;
                        if ($print_array["$value"]["S_FACE"] > 0) {
														$S_PAPER_TMP=explode(",",$VOW_ARRAY["$value"]["S_FACE"]);
														switch ($S_BTYPE) {
																case ($S_BTYPE>='112' and $S_BTYPE<='122'):
																		$S_PAPER_TMP2=explode("_",$S_PAPER_TMP[0]);
																		$S_PAPER+=$S_PAPER_TMP2[0];
																		break;
																default:
																		foreach ($S_PAPER_TMP as $S_PAPER_VAL) {
																				$S_PAPER_TMP2=explode("_",$S_PAPER_VAL);
																				$S_PAPER+=$S_PAPER_TMP2[0];
																		}
																		break;
														}
												}
                        if ($print_array["$value"]["D_FACE"] > 0) {
														$D_PAPER_TMP=explode(",",$VOW_ARRAY["$value"]["D_FACE"]);
														switch ($S_BTYPE) {
																case ($S_BTYPE>='112' and $S_BTYPE<='122'):
																		$D_PAPER_TMP2=explode("_",$D_PAPER_TMP[0]);
																		$D_PAPER+=$D_PAPER_TMP2[0];
																		break;
																default:
																		foreach ($D_PAPER_TMP as $D_PAPER_VAL) {
																				$D_PAPER_TMP2=explode("_",$D_PAPER_VAL);
																				$D_PAPER+=$D_PAPER_TMP2[0];
																		}
																		break;
														}
												}
                        $html.='<td '.$rowspan.'>'.$print_array["$value"]["BMEMO"];
                       
                        if ($S_BTYPE<115 or  $S_BTYPE>122) {
                            if ($S_PAPER!='') {
                                $html.='，A3單面：'.$S_PAPER.' 張';
                            }
                            if ($D_PAPER!='') {
                                $html.='，A3雙面：'.$D_PAPER.' 張';
                            }
                        }
                        $html.='</td>';
												switch ($S_BTYPE) {
														case ($S_BTYPE >='54' and $S_BTYPE <='57'):
														case 35:
														case 36:
																$html.='<td '.$rowspan.'>'.$print_array["$value"]["BONUM"].'本</td>';
																break;
														case ($S_BTYPE>='115' and $S_BTYPE<='122'):
																$html.='<td '.$rowspan.'>'.$print_array["$value"]["BONUM"].'件</td>';
																break;
														case ($S_BTYPE>='124' and $S_BTYPE<='129'):
                            case 81:
																$html.='<td '.$rowspan.'>'.$print_array["$value"]["BONUM"].'張</td>';
																break;
														default:
																$html.='<td '.$rowspan.'>'.$print_array["$value"]["BONUM"].'本</td>';
																break;
												}
                        //2011/09/13 A3作品集內頁都要上亮光
                        //2011/11/02 A3作品集平、精內頁都要不上亮光
                        //2012/10/01 所有作品集只要蝴蝶裝、特銅250P 的通通上光
                        if ($print_array[$value]["PPID"]=='123' and ($bind=='805' or $bind=='804')) {
                            $html.='<td>紙別：'.$print_array[$value]["PPNAME"].$S_BIND.'，上水光</td>';
                        //筆記本
                        } elseif ($S_BTYPE=='54' or $S_BTYPE=='55' or $S_BTYPE=='56' or $S_BTYPE=='57' or $S_BTYPE=='35' or $S_BTYPE=='36') {
                            $html.='<td>紙別：'.$print_array[$value][TEXT].'；加工：環裝(9/16白鐵圈)';
                        } elseif ($S_BTYPE=='206') {
                            $html.='<td>紙別：10*7.5cm模造便利貼紙</td>';
                        } elseif ($S_BTYPE=='207') {
                            $html.='<td>紙別：10*7.5cm模造便利貼紙+五色便利貼紙</td>';
                        } elseif ($S_BTYPE=='208') {
                            $html.='<td>紙別：10*7.5cm模造便利貼紙+五色便利貼紙+5*7.5cm模造便利貼紙</td>';
                        } elseif ($S_BTYPE=='209') {
                            $html.='<td>紙別：五色便利貼紙</td>';
												} elseif ($S_BTYPE>='212' and $S_BTYPE<='214') {
														$html.='<td>紙別：珠光紙200P、'.$print_array[$value]["PPNAME"].$S_BIND.'，加工：上霧P</td>';
                        } else {
                            $html.='<td>紙別：'.$print_array[$value]["PPNAME"].$S_BIND.'</td>';
                        }
                        $html.='<td>&nbsp;</td>';
                        $html.='<td>&nbsp;</td>';
                        //判斷是否有存約交日，若有直接抓出來倒扣
                        if ($print_array[$value][BOSHIPTIME]!=0) {
                            if ($S_BTYPE=='31' or $S_BTYPE=='30') {
                                //桌曆自製的工廠約交日為客戶約交日扣 1 天
                                if ($print_array[$value][FACID]=='10') {
                                    $s_day=1;
                                } else {
                               //桌曆外包的工廠約交日為客戶約交日扣 3 天
                                    $s_day=3;
                                }
                            } elseif (substr($value,0,1)=='S' and $print_array[$value][PDT_GP]!='G001') {
                                $s_day=2;
                            } else {
																switch ($S_BTYPE) {
																		case 70://iphone保護殼工廠約交日為客戶約交日扣2天
																		case 71:
																		case ($S_BTYPE>='274' and $S_BTYPE<='283'):
																				$s_day=2;
																				break;
																		default:
																				$s_day=1;
																				break;
																}
                            }
                            $sub_day=1;
                            $k=1;
                            $real_sday=0;
                      			while ($k <= $s_day) {
                      					$chk_h=$obj->chkholday($obj->nextday($print_array[$value][BOSHIPTIME],$sub_day,'B'));
                      					if ($chk_h) {
                      							$real_sday++;
                      					} else {
                      							$k++;
                      					}
                      					$sub_day++;
                      			}
                      			$tol_sub_day=$s_day + $real_sday;
                       			$fac_sendday =$obj->nextday($print_array[$value][BOSHIPTIME],$tol_sub_day,'B');
                         //沒有存約交日，用原本的計算方式
												 //2014/10/29 Arvin 直接代入讓訂單轉不過
                        } else {
														$fac_sendday=$print_array[$value][BOSHIPTIME];
                        }
                        $html.='<td '.$rowspan.'>'.date("Y/m/d",$fac_sendday).'</td>';
                        switch ($print_array[$value][BOPAYTYPE]) {
                				    case "1":
                								$TYPE="信用卡";
                								break;
                            case "2":
                                $TYPE="優惠券";
                                break;
                						case "3":
                								$TYPE="ATM轉帳";
                								break;
                						case "4":
                								$TYPE="月結";
                								break;
                           	case "5":
                                if ($print_array[$value][WBFLOW]=='3') {
                								    $TYPE="月結";
                                } else {
                                    $TYPE="虛擬ATM";
                                }
                								break;
                				}
                        //2011/07/26 Arvin 判斷優惠券產品折價後為0元時標註顯示兌換券
                        if ($print_array[$value][CPBONUS]!='' and $print_array[$value][CPBONUS]==0) {
                            $TYPE="兌換券(".$print_array[$value][PREORDER].")";
                        }
                        $html.='<td '.$rowspan.'>'.$TYPE.'</td>';
                        $s_memo="";
                        switch (strtoupper($print_array["$value"][BOCOUPON])) {
                            case '33AD3F67':
                                $s_memo="神腦試作";
                                break;
                            case '70E71CD5':
                                $s_memo="母親節活動專案";
                                break;
                            case '18888888':
                                $s_memo="父親節活動專案";
                                break;
                        }	
                        $html.='<td '.$rowspan.'>'.$S_PS.$work_process.$sub_work_process.$print_array["$value"]["BOMEMO"].$s_memo.'</td>';
                        $html.='</tr>';
                        break;
                    case 1:
                        $html.='<tr>';
                        if ($S_BTYPE!='83' and $S_BTYPE!='84') {
                            if ($S_BTYPE=='33' or $S_BTYPE=='38' or $S_BTYPE=='39') {
                                if ($print_array[$value][WBFLOW]==1) {
																		$html.='<td><span id="'.$print_array[$value]["BID"].'_1" class="cal_cover"></span></td>';
																} else {
																		$html.='<td><span id="'.$print_array[$value]["BID"].'_2" class="cal_cover"></span></td>';
																}
                            } else {
                                $html.='<td>'.$print_array[$value]["COVER"].'</td>';
                            }
                    				
                            //刪除檔案
                            $delfile=$GB_BOOKPATH_PDF."spool\\".$print_array[$value]["COVER"];
                            @unlink($delfile);
                        }
                				$html.='<td>'.$S_WORK.'</td>';
                        switch ($S_BTYPE) {
                            //A4直,A5橫,21,B5直平裝封面Indigo印
                            //2011/11/04 Arvin A5直不管任何裝訂方式都改indigo印，且紙別換銅板貼紙
                            case "28": case "22": case "17": case "26": case "27":
                                if ($bind=='801') {
                                    $html.='<td>&nbsp;</td>';
                                } else {
                                    $html.='<td>大圖'.$C_PAPER.'，冷表</td>';
                                }
                                break;
                            case "20": case "21": case "23": case "29": case "24":
                                $html.='<td>大圖'.$C_PAPER.'，冷表</td>';
                                break;
                            //筆記本
                            case "35": case "36": case "54": case "55": case "56": case "57":
                                $html.='<td>'.$C_PAPER.'</td>';
                                break;
                            default:
                                $html.='<td>&nbsp;</td>';
                                break;
                        }
                        switch ($S_BTYPE) {
                            //2011/11/04 Arvin A5直都改indigo印，非平裝紙別換銅板貼紙
                            case "28": case "22": case "17":case "27":case "26":
                                if ($bind=='801') {
                            				$html.='<td>INDIGO，銅西250，上霧P</td>';
                                } else {
                                    $html.='<td>&nbsp;</td>';
                                }
                                break;
                            default:
                                $html.='<td>&nbsp;</td>';
                                break;
                        }
                		    $html.='</tr>';
                        break;
                    case "2":
                        $html.='<tr>';
                    		$html.='<td>'.$print_array[$value]["WING"].'</td>';
                    	  $html.='<td>&nbsp;</td>';
                    		$html.='<td>&nbsp;</td>';
                    		$html.='<td>&nbsp;</td>';
                    		$html.='</tr>';
                        break;
                }
            }
            $v_num++;
          	$SQL =' Insert into pordereport (BOID,DATA3,TSNEW,FLAGPO) ';
    				$SQL.=' values (\''.$print_array[$value][BOID].'\',\''.base64_encode($html).'\',\''.$insert_time.'\',\'N\')';
		    		$query=mssql_query($SQL,$GB_dblk);
        }
    }

		/**********************************************************************************************************************************
		 *  轉檔清冊
		 **********************************************************************************************************************************/
		$num=1;
    $paint_num=1;
		$new_paint_num=1;
    $beautifly_num=1;
		foreach ($LOOP_ARRAY as $value) {
				$S_BTYPE=$print_array[$value][BTYPE];
        switch ($print_array[$value][BOPAYTYPE]) {
						case "1":
								$TYPE="信用卡";
								break;
            case "2":
								$TYPE="優惠券";
								break;
						case "3":
								$TYPE="ATM轉帳";
								break;
						case "4":
								$TYPE="月結";
								break;
            case "5":
                if ($print_array[$value][WBFLOW]=='3') {
								    $TYPE="月結";
                } else {
                    $TYPE="虛擬ATM";
                }
								break;
				}
        //加工條件
        $work_process="";
        if (is_array($print_array[$value][WORKNAME])) {
            foreach ($print_array[$value][WORKNAME] as $w_value) {
                if ($work_process=='') {
                    $work_process=$w_value;
                } else {
                    $work_process.="，".$w_value;
                }
            }
        }
        $sub_work_process="";
        if (is_array($print_array[$value][SUBWORKNAME])) {
            foreach ($print_array[$value][SUBWORKNAME] as $s_value) {
                if ($sub_work_process=='') {
                    $sub_work_process=$s_value;
                } else {
                    $sub_work_process.="，".$s_value;
                }
            }
        }

        //2011/07/26 Arvin 判斷優惠券產品折價後為0元時標註顯示兌換券
        if ($print_array[$value][CPBONUS]!='' and $print_array[$value][CPBONUS]==0) {
            $TYPE="兌換券(".$print_array[$value][PREORDER].")";
        }
				/********************************************************************************************
				*  內部用清冊
				*********************************************************************************************/
				$sub_html ='<tr>';
				$sub_html.='<td>'.($num).'</td>';
        //外包產品才顯示條碼欄位
        if ($MAIL_N=='out') {
						switch ($S_BTYPE) {
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
								//非作品集的產品才顯示條碼
								default:
										$sub_html.='<td><div id="'.$print_array["$value"]["YFPBOID"].'" class="barcodeTarget"></div><canvas id="'.$print_array["$value"]["YFPBOID"].'" width="150" height="150"></canvas></td>';
										break;
						}
        }
        $TMP_ARY=$obj->choose_ftp($print_array[$value][FACID]);
        $SHOW_FACNAME=$TMP_ARY['FACNAME'];
        switch ($S_BTYPE) {
             /********************************************************************************************
      			 *  名片檔案數量表格
  		    	 *********************************************************************************************/
            case ($S_BTYPE< '85' and  $S_BTYPE > '82'):
                $sub_html.='<td align="center">'.$print_array[$value]["YFPBOID"].'<BR> ';
                $sub_html.='('.$print_array["$value"]["BOID"].')<BR> ';
    						$sub_html.='<table border="1" cellpadding="0" cellspacing="0" width="100%">';
    						$sub_html.='<tr align="center"><td width="50%">檔案</td><td width="50%">數量</td></tr>';
    						$p_ii=0;
    						foreach ($print_array["$value"]["PDF"] as $pdf_value) {
    								$sub_html.='<tr align="center">';
    								$sub_html.='<td>X'.$pdf_value.'</td>';
    								$sub_html.='<td>'.$print_array["$value"]["PDF2"]["$p_ii"].'</td>';
    								$sub_html.='</tr>';
    								$p_ii++;
    						}
    						$sub_html.='</table>';
    						$sub_html.='</td>';
                if ($MAIL_N=='in') {
                    $sub_html.='<td align="center">&nbsp;</td>';
                }
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
                break;
             /********************************************************************************************
      			 *  企業DM、帳單
      			 *********************************************************************************************/
            case 40:
            case 41:
                $sub_html.='<td align="center">'.$print_array[$value]["YFPBOID"].'<BR> ';
                $sub_html.='('.$print_array["$value"]["BOID"].')<BR> ';
                if ($print_array["$value"]["FACID"]!='25') {
                    $sub_html.='<table border="1" cellpadding="0" cellspacing="0" width="100%">';
                    $sub_html.='<tr align="center"><td width="50%">檔案</td><td width="50%">數量</td></tr>';
                    $sub_html.='<tr align="center">';
                    $sub_html.='<td>'.$print_array[$value]["FILENAME"].'</td>';
                    $sub_html.='<td>'.$print_array["$value"]["BONUM"].'</td>';
                    $sub_html.='</tr>';
                    $sub_html.='</table>';
                }
                $sub_html.='</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                if ($MAIL_N=='in') {
                    $sub_html.='<td align="center">'.$print_array["$value"]["TITLE"].'</td>';
                }
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].$print_array["$value"]["PRODUCT_UNIT"].'</td>';
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
                break;
            /********************************************************************************************
      			*  T-shirt尺寸數量表格
			     	*********************************************************************************************/
            case 110:
						case 107:
                $sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center"> '.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"].'<br> ';
    						foreach ($print_array[$value][PDF] as $pdf_value) {
    								$sub_html.='<table border="1" cellpadding="0" cellspacing="0" width="100%">';
    								$sub_html.='<tr align="center"><td width="37%">正面</td><td width="37%">背面</td><td width="26%">件</td></tr>';
    								$sub_html.='<tr>';
    								$sub_html.='<td align="center">'.$T_SHIRT_FILE["$value"]["$pdf_value"]['FRONT'].'</td>'; //正面檔名
    								$sub_html.='<td align="center">'.$T_SHIRT_FILE["$value"]["$pdf_value"]['BACK'].'</td>';  //背面檔名
    								$sub_html.='<td>';
    								$sub_html.='<table border="1" cellpadding="0" cellspacing="0" width="100%">';
    								foreach ($T_SHIRT_FILE[$value][$pdf_value][SIZE] as $s_key =>$s_value) {
    									  $sub_html.='<tr align="center">';
    										switch (strtolower($s_key)) {
    												case "f":
																if ($S_BTYPE=='107') {
																		$show_kind='亞規成人';
																} else {
																		$show_kind='中性';
																}
    														break;
    												case "w";
																if ($S_BTYPE=='107') {
																		$show_kind='修身女版';
																} else {
																		$show_kind='女版';
																}
    														break;
    										}
    										$sub_html.='<td align="center">'.$show_kind.'</td><td>數量</td>';
    										$sub_html.='</tr>';
    										foreach ($s_value as $s_key1 => $s_value1) {
    												$sub_html.='<tr align="center"><td>'.strtoupper($s_key1).'</td><td>'.$s_value1.'</td></tr>';
    										}
    								}
    								$sub_html.='</table></td>';
    								$sub_html.='</tr></table>';
    								$sub_html.='</td>';
                    $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'件</td>';
    						}
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
                break;
            /********************************************************************************************
    				*  Iphone殼、貼紙
		    		*********************************************************************************************/
            case 70:
            case 71:
            case 74:
            case 75:
								$sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'件</td>';
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
								break;
						case 220://3D公仔
                $sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'件</td>';
                // $sub_html.='<td align="center">公仔ID:'.$print_array[$value][OBJID].'</td>';
                break;
						/********************************************************************************************
    				*  工商日誌
		    		*********************************************************************************************/
						case ($S_BTYPE>='270' and $S_BTYPE<='272'):
								$sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'件</td>';
                $sub_html.='<td align="center">'.$work_process;
								$sub_html.='<BR>'.$print_array["$value"]["hole"];
								if ($print_array["$value"]["gold_kind"]!="") {
										$sub_html.='<BR>'.$print_array["$value"]["gold_position"];
										$sub_html.='<BR>'.$print_array["$value"]["gold_kind"];
								}
								$sub_html.='</td>';
								break;
            /********************************************************************************************
    				*  紙袋
		    		*********************************************************************************************/
            case ($S_BTYPE>='130' and $S_BTYPE <='139'):
                $sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array["$value"]["PPNAME"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'個</td>';
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'，繩子顏色:'.$print_array["$value"]["COLOR"].'</td>';
                break;
						 /********************************************************************************************
    				*  棉布袋
		    		*********************************************************************************************/
						case 167:
						case 168:
  							$sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array["$value"]["PPNAME"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'個</td>';
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
								break;
            /********************************************************************************************
    				*  拼圖
		    		*********************************************************************************************/
            case ($S_BTYPE>='115' and $S_BTYPE<='122'):
                $sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["TITLE"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                $sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'件</td>';
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
                break;
            default:
                if (substr($value,0,1)=='S') {
                    $sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                    $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
    								$sub_html.='<td align="center">'.$print_array["$value"]["PDT_NAME"].'</td>';
    								$sub_html.='<td align="center">'.$print_array["$value"]["BONUM"].'&nbsp;'.$print_array["$value"]["UNIT"].'</td>';
    						} else {
     								//*******************************************************************************************
            				//  廣告面紙及其它
            				//*******************************************************************************************
                    $show_t='';
                    if ($S_BTYPE=='109' or $S_BTYPE=='106' or $BTYPE=='105') {
                        switch ($print_array["$value"]["PPID"]) {
                            case "650":
                                $show_t='5抽';
                                break;
                            case "651":
                                $show_t='7抽';
                                break;
														case "652":
																$show_t='8抽';
																break;
														case "654":
														case "655":
																$show_t=$print_array["$value"]["PPNAME"];
																break;
														case "653":
																$show_t='10抽';
																break;
                        }
                        $show_t='-'.$show_t;
                    }
                    //自製產品才顯示作品名稱
                    $sub_html.='<td>'.$print_array["$value"]["YFPBOID"].'<BR>('.$print_array["$value"]["BOID"].')</td>';
                    $sub_html.='<td align="center">'.$SHOW_FACNAME.'</td>';
                    if ($MAIL_N=='in') {
                        $sub_html.='<td align="center">'.$print_array["$value"]["TITLE"].'</td>';
                    }
    								$sub_html.='<td align="center">'.$print_array["$value"]["BMEMO"].$print_array[$value]["COVER_KIND"].$show_t.'</td>';
    								$sub_html.='<td align="center">'.$print_array["$value"]["BONUM"];
                    switch ($S_BTYPE) {
                        case ($S_BTYPE < '32'):
                        case ($S_BTYPE>='206' and $S_BTYPE<='209'):
                        case '77':
                            $sub_html.='本';
                            break;
                        case '83':
                        case '84':
                            $sub_html.='盒';
                            break;
                        case '35':
                        case '36':
                        case '55':
                        case '56':
                        case '54':
                        case '57':
                            $sub_html.='本';
                            break;
                        case '109'://廣告面紙
												case '106'://名片面紙包
												case '105'://塑料面紙包
                            $sub_html.='包';
                            break;
                        case '48':
                            $sub_html.='套(1套6個)';
                            break;
                        case '50':
                            $sub_html.='套(1套48個)';
                            break;
                        case '72':
                        case '73':
                        case '166':
                            $sub_html.='個';
                            break;
                        case '81':
                            $sub_html.='張';
                            break;
                       case '76':
                            $sub_html.='捲';
                            break;
                        default:
                            $sub_html.='份';
                            break;
                    }
                    $sub_html.='</td>';
    						}
                $sub_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
                break;
        }
        //加工條件
        $work_process="";
        if (is_array($print_array[$value][WORKNAME])) {
            foreach ($print_array[$value][WORKNAME] as $w_value) {
                if ($work_process=='') {
                    $work_process=$w_value;
                } else {
                    $work_process.="，".$w_value;
                }
            }
        }
        $sub_work_process="";
        if (is_array($print_array[$value][SUBWORKNAME])) {
            foreach ($print_array[$value][SUBWORKNAME] as $s_value) {
                if ($sub_work_process=='') {
                    $sub_work_process=$s_value;
                } else {
                    $sub_work_process.="，".$s_value;
                }
            }
        }
       // $show_addr =;

			  $sub_html.='<td align="center">'.$print_array[$value][BORADDR].'</td>';
    	  $sub_html.='<td align="center">'.$print_array[$value][BORPHONE].'</td>';
    		$sub_html.='<td align="center">'.$print_array[$value][BORNAME].'</td>';
        //訂單號碼第一位等於 S 的是買賣業訂單，約交日為當日
        if (substr($value,0,1)=='S') {
				    $sub_html.='<td>'.date("Y/m/d",time()).'</td>';
        } else {
            //判斷是否有存約交日，若有直接抓出來倒扣
            if ($print_array[$value][BOSHIPTIME]!=0) {
                //蝴蝶裝作品集、iphone保護殼工廠約交日為客戶約交日扣2天
                if ($S_BTYPE=='70' or $S_BTYPE=='71') {
                    $s_day=2;
                } elseif ($S_BTYPE=='31' or $S_BTYPE=='30') {
                    //桌曆自製的工廠約交日為客戶約交日扣 1 天
                    if ($print_array[$value][FACID]=='10') {
                        $s_day=1;
                    } else {
                   //桌曆外包的工廠約交日為客戶約交日扣 3 天
                        $s_day=3;
                    }
                } elseif (substr($value,0,1)=='S' and $print_array[$value][PDT_GP]!='G001') {
                    $s_day=2;
                } else {
                    $s_day=1;
                }
                $sub_day=1;
                $k=1;
                $real_sday=0;
          			while ($k <= $s_day) {
          					$chk_h=$obj->chkholday($obj->nextday($print_array[$value][BOSHIPTIME],$sub_day,'B'));
          					if ($chk_h) {
          							$real_sday++;
          					} else {
          							$k++;
          					}
          					$sub_day++;
          			}
          			$tol_sub_day=$s_day + $real_sday;
           			$fac_sendday=$obj->nextday($print_array[$value][BOSHIPTIME],$tol_sub_day,'B');
                //============================貼紙、無框畫======================================================
                $sub_day=1;
                $k=1;
                $real_sday=0;
          			while ($k <= 2) {
          					$chk_h=$obj->chkholday($obj->nextday($print_array[$value][BOSHIPTIME],$sub_day,'B'));
          					if ($chk_h) {
          							$real_sday++;
          					} else {
          							$k++;
          					}
          					$sub_day++;
          			}
          			$tol_sub_day= 2 + $real_sday;
           			$fac_sendday2=$obj->nextday($print_array[$value][BOSHIPTIME],$tol_sub_day,'B');
             //============================================================================================
             //沒有存約交日，用原本的計算方式
            } else {
								$fac_sendday=$print_array[$value][BOSHIPTIME];
            }
            $sub_html.='<td>'.date("Y/m/d",$fac_sendday).'</td>';
        }
				$sub_html.='<td>'.$TYPE.'<BR>'.$print_array["$value"]["VVBOID"].'<BR>'.$print_array["$value"]["EXBOID"].'</td>';

        //2011/07/20 Arvin 增加判斷BOINVTYPE 發票型態，若為21則在BOMEMO內加上二聯式捐贈，25為電子發票
        //2011/08/04 Arvin 判斷就算BOINVTYPE 為21或25，若是 0 元兌換券不顯示
        if ($print_array[$value][BOINVTYPE]=='21') {
            $SHOW_BOINVTYPE='二聯式捐贈。';
        } elseif ($print_array[$value][BOINVTYPE]=='25') {
            $SHOW_BOINVTYPE='電子發票。';
        } elseif ($print_array[$value][BOINVTYPE]=='26') {
            $SHOW_BOINVTYPE='電子發票捐贈。';
        } else {
            $SHOW_BOINVTYPE='';
        }
        //2013/06/10  0元兌換券若有加購包裝盒或其他收費加工就顯示發票捐贈的備註
        if ($print_array[$value][CPBONUS]!='' and $print_array[$value][CPBONUS]==0 and $print_array[$value][BOPRICE]==0) {
            $SHOW_BOINVTYPE='';
        }
        $s_memo="";
        switch (strtoupper($print_array["$value"][BOCOUPON])) {
            case '33AD3F67':
                $s_memo="神腦試作";
                break;
            case '70E71CD5':
                $s_memo="母親節活動專案";
                break;
            case '18888888':
                $s_memo="父親節活動專案";
                break;
        }
				//2013/07/23 Arvin 無蓋ipad mini加送觸控筆+試鏡布
        if ($S_BTYPE=='72') {
            $s_memo="加送觸控筆+試鏡布";
        }
        $sub_html.='<td>'.$SHOW_BOINVTYPE.$print_array["$value"]["WORD"].$print_array["$value"]["BOMEMO"].$s_memo.'</td>'; //備註
				$sub_html.='</tr>';

				$check_group_ary=$obj->search_sales($value);



				//2014/1/23 Arvin 增加喜來登名片尺寸備註
				if (($print_array["$value"]["BOVATNO"]=='86517976' or $check_group_ary[ggid]=='54639913') and $print_array[$value][WBFLOW]=='3' and ($S_BTYPE=='83' or $S_BTYPE=='84')) {
						$s_memo="尺寸 90*50mm";
				}
				/********************************************************************************************
				*  外部用清冊
				*********************************************************************************************/
				//判斷原本廠商直送的產品，若客戶選自取要將送件資訊改為萬華的資料
				if(trim($print_array[$value][BOSEND])=='C') {
						$SHOW_ADDR='10857台北市萬華區艋舺大道85號B1';
						$SHOW_TEL   ='02-2306-1958#151';
						$SHOW_REC   ='賀蓉蓉';
				} else {
						switch ($S_BTYPE) {
								case 48:
								case 50:
								case 74:
								case 75:
								case ($S_BTYPE>=274 and $S_BTYPE<=283):
								case 396:
								case 397:
										$SHOW_ADDR='10857台北市萬華區艋舺大道85號B1';
										$SHOW_TEL   ='02-2306-1958#151';
										$SHOW_REC   ='賀蓉蓉';
										break;
								default:
										$SHOW_ADDR=$print_array["$value"]["BORADDR"];
										$SHOW_TEL   =$print_array["$value"]["BORPHONE"];
										$SHOW_REC   =$print_array["$value"]["BORNAME"];
										break;
						}
				}
        //無框畫
        if ($S_BTYPE>='142' and $S_BTYPE<='163') {
            $fac_html ='<tr>';
    				$fac_html.='<td>'.($paint_num).'</td>';
            $fac_html.='<td>'.$print_array["$value"]["YFPBOID"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["COVER"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["TITLE"].'</td>';
            $fac_html.='<td>'.date("Y-m-d H:i:s",$print_array[$value]["BOTIME"]).'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BMEMO"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BONUM"].'</td>';
            $fac_html.='<td>大圖油畫布</td>';
            $fac_html.='<td>'.date("Y/m/d",$fac_sendday2).'</td>';
            $fac_html.='<td>外包小雅</td>';
            $paint_num++;
				//工商日誌
				} elseif ($S_BTYPE>='270' and $S_BTYPE<='272') {
						$fac_html ='<tr>';
    				$fac_html.='<td>'.($num).'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BOID"].'<br>'.$print_array["$value"]["YFPBOID"].'</td>';
            $fac_html.='<td>'.date("Y-m-d H:i:s",$print_array["$value"]["BOTIME"]).'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BMEMO"].'-'.$print_array["$value"]["PPNAME"];
						$fac_html.='<BR>'.$print_array["$value"]["hole"];
						if ($print_array["$value"]["gold_kind"]!="") {
								$fac_html.='<BR>'.$print_array["$value"]["gold_position"];
								$fac_html.='<BR>'.$print_array["$value"]["gold_kind"];
						}
						$fac_html.='</td>';
            $fac_html.='<td>'.$print_array["$value"]["BONUM"].'</td>';
            $fac_html.='<td>'.$SHOW_ADDR.'</td>';
     				$fac_html.='<td>'.$SHOW_TEL.'</td>';
            $fac_html.='<td>'.$SHOW_REC.'</td>';
            $fac_html.='<td>'.date("Y/m/d",$fac_sendday).'</td>';
				//新版厚無框畫
				} elseif ($S_BTYPE>='232' and $S_BTYPE<='241') {
						$fac_html ='<tr>';
    				$fac_html.='<td>'.($new_paint_num).'</td>';
            $fac_html.='<td>'.$print_array["$value"]["YFPBOID"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["COVER"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["TITLE"].'</td>';
            $fac_html.='<td>'.date("Y-m-d H:i:s",$print_array[$value]["BOTIME"]).'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BMEMO"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BONUM"].'</td>';
            $fac_html.='<td>大圖油畫布</td>';
            $fac_html.='<td>'.date("Y/m/d",$fac_sendday2).'</td>';
            $fac_html.='<td>外包健隆</td>';
            $new_paint_num++;
        //名片產品
        } elseif ($S_BTYPE>='82' and $S_BTYPE<='84') {
            //依您印名片
            if ($print_array[$value][FACID]=='23') {
                $fac_html ='<tr>';
        				$fac_html.='<td>'.($num).'</td>';
        				$fac_html.='<td>'.$print_array["$value"]["BOID"].'<br>'.$print_array["$value"]["YFPBOID"].'<BR>';

    						$fac_html.='<table border="1" cellpadding="0" cellspacing="0" width="100%">';
    						$fac_html.='<tr align="center"><td width="50%">檔案</td><td width="50%">數量</td></tr>';
    						$p_ii=0;
    						foreach ($print_array["$value"]["PDF"] as $pdf_value) {
    								$fac_html.='<tr align="center">';
    								$fac_html.='<td>X'.$pdf_value.'</td>';
    								$fac_html.='<td>'.$print_array["$value"]["PDF2"]["$p_ii"].'</td>';
    								$fac_html.='</tr>';
    								$p_ii++;
    						}
    						$fac_html.='</table>';
                $fac_html.='</td>';
                $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                $fac_html.='<td align="center">'.$print_array[$value]["PPNAME"].'</td>';
                $fac_html.='<td align="center">'.$work_process.'</td>';
                $fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>';
                $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
								$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
								$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
								$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
								$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
                $fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>'; //備註
            } else {
								$fac_html ='<tr>';
								$fac_html.='<td>'.($num).'</td>';
								$fac_html.='<td>'.$print_array["$value"]["BOID"].'<br>'.$print_array["$value"]["YFPBOID"].'<BR>';
                $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
								$fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
                $fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
     				    $fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
								$fac_html.='<td align="center">'.$check_group_ary["ugname"].'</td>';
                $fac_html.='<td align="center">'.$SHOW_REC.'</td>';
            }
        //蝴蝶裝產品  2012/05/25 增加
        } elseif ($print_array[$value]["BIND"]=='804' or $print_array[$value]["BIND"]=='805') {
            $fac_html ='<tr style="font-size:24px">';
    				$fac_html.='<td>'.($beautifly_num).'</td>';
            $fac_html.='<td>'.$print_array["$value"]["YFPBOID"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BMEMO"].'</td>';
            $fac_html.='<td>'.$print_array["$value"]["BONUM"].'</td>';
            $fac_html.='<td>&nbsp;</td>';
            $fac_html.='<td>&nbsp;</td>';
            $fac_html.='<td><B>'.date("Y/m/d",$fac_sendday).'</B></td>';
            $fac_html.='<td>&nbsp;</td>';
            $beautifly_num++;
        //其他產品
        } else {
    				$fac_html ='<tr>';
    				$fac_html.='<td>'.($num).'</td>';
						//影印紙廠商增加條碼
						if ($print_array[$value][FACID]==21 or $print_array[$value][FACID]==18) {
								$fac_html.='<td>&nbsp;&nbsp;&nbsp;&nbsp;<span style=\'font-family:"PSMC39HrP24DhTt";font-size:36px;line-height: 130%;\'>*AVF30853*</span>&nbsp;&nbsp;&nbsp;&nbsp;</td>';
						}
            if ($print_array[$value][FACID]==98 or $print_array[$value][FACID]==97 or $print_array[$value][FACID]==96 or $print_array[$value][FACID]==95) { //科谷實業只要顯示一次，因為不進ERP所以兩個號碼會一樣
                $fac_html.='<td>'.$print_array["$value"]["BOID"].'</td>';
            } else {
                $fac_html.='<td>'.$print_array["$value"]["BOID"].'<br>'.$print_array["$value"]["YFPBOID"].'</td>';
            }
            switch ($S_BTYPE) {
								/********************************************************************************************
        				*  塑料面紙 (廠商標頭不同所以分開寫)
        				*********************************************************************************************/
								case 105:
										$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["PPNAME"].'</td>'; //抽數
										$fac_html.='<td align="center">&nbsp;</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>'; //檔案名稱
										$fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>'; //數量					
										$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
										$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
                    $fac_html.='<td align="center">&nbsp;</td>'; //備註
										break;
								case 106:
                    $fac_html.='<td align="center"> '.$print_array["$value"]["BMEMO"].'</td>';
                    switch ($print_array["$value"]["PPID"]) {
                        case "652":
														$show_t='8';
														break;
												case "653":
														$show_t='10';
														break;
                    }
                    $fac_html.='<td align="center">'.$show_t.'抽</td>'; //抽數
										$fac_html.='<td align="center">&nbsp;</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>'; //檔案名稱
										$fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>'; //數量					
										$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
                    $fac_html.='<td align="center">&nbsp;</td>'; //備註
                    break;
                /********************************************************************************************
        				*  廣告面紙
        				*********************************************************************************************/
                case 109:
                    $fac_html.='<td align="center"> '.$print_array["$value"]["BMEMO"].'</td>';
                    switch ($print_array["$value"]["PPID"]) {
                        case "650":
                            $show_t='5';
                            break;
                        case "651":
                            $show_t='7';
                            break;
                    }
                    $fac_html.='<td align="center">'.$show_t.'</td>'; //抽數
                    $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>'; //數量
                    $fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>'; //檔案名稱
                    $fac_html.='<td align="center">永豐紙業</td>'; //訂貨廠商
                    $fac_html.='<td align="center">月結</td>'; //結帳方式
                    $fac_html.='<td align="center">美和盛代送</td>'; //送貨方式
										$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
                    $fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'出貨</td>'; //約交日 +4
                    $fac_html.='<td align="center">&nbsp;</td>'; //備註
                    break;
                /********************************************************************************************
        				*  T-shirt 尺寸數量表格
        				*********************************************************************************************/
                case 110:
								case 107:
                    $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"].'<br> ';
        						foreach ($print_array[$value][PDF] as $pdf_value) {
        								$fac_html.='<table border="1" cellpadding="0" cellspacing="0" width="100%">';
        								$fac_html.='<tr align="center"><td width="37%">正面</td><td width="37%">背面</td><td width="26%">件</td></tr>';
        								$fac_html.='<tr>';
        								$fac_html.='<td align="center">'.$T_SHIRT_FILE["$value"]["$pdf_value"]['FRONT'].'</td>'; //正面檔名
        								$fac_html.='<td align="center">'.$T_SHIRT_FILE["$value"]["$pdf_value"]['BACK'].'</td>';  //背面檔名
        								$fac_html.='<td>';
        								$fac_html.='<table border="1" cellpadding="0" cellspacing="0" width="100%">';
        								foreach ($T_SHIRT_FILE["$value"]["$pdf_value"]["SIZE"] as $s_key =>$s_value) {
        									  $fac_html.='<tr align="center">';
        										switch (strtolower($s_key)) {
        												case "f":
        														if ($S_BTYPE=='107') {
																				$show_kind='亞規成人';
																		} else {
																				$show_kind='中性';
																		}
        														break;
        												case "w";
        														if ($S_BTYPE=='107') {
																				$show_kind='修身女版';
																		} else {
																				$show_kind='女版';
																		}
        														break;
        										}
        										$fac_html.='<td align="center">'.$show_kind.'</td><td>數量</td>';
        										$fac_html.='</tr>';
        										foreach ($s_value as $s_key1 => $s_value1) {
        												$fac_html.='<tr align="center"><td>'.strtoupper($s_key1).'</td><td>'.$s_value1.'</td></tr>';
        										}
        								}
        								$fac_html.='</table></td>';
        								$fac_html.='</tr></table>';
        								$fac_html.='</td>';
                        $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
        						}
										$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
                    if($print_array[$value][CPBONUS]!='' and $print_array[$value][CPBONUS]==0) {
                        $fac_html.='<td align="center">合作方案(團購網)，'.date("Y/m/d",$fac_sendday).'出貨</td>'; //備註
                    } else {
                        $fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'出貨</td>'; //備註
                    }
                    break;
                 /********************************************************************************************
        				* Iphone殼
        				*********************************************************************************************/
                case 70:
                case 71:
								case 220:
										if ($S_BTYPE=='220') {
												$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"].'</td>';
												// $fac_html.='<td align="center"><img src="cover.jpg"></td>';
												// $fac_html.='<td align="center">'.$print_array["$value"]["OBJID"].'</td>';
										} else {
												$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"]."-".$work_process.'</td>';
												$fac_html.='<td align="center">&nbsp;</td>';
												$fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>';
										}
                    $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
										$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
										if ($print_array[$value][FACID]=='23') {
												$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
										}
                    $fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>'; //備註
                    break;
                /********************************************************************************************
        				* 紙袋
        				*********************************************************************************************/
                case ($S_BTYPE>='130' and $S_BTYPE<='139'):
                    $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                    $fac_html.='<td align="center">'.$print_array[$value]["PPNAME"].'</td>';
                    $fac_html.='<td align="center">'.$work_process.'、繩子顏色：'.$print_array["$value"]["COLOR"].'</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
                    $fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
										$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>'; //備註
                    break;
								 /********************************************************************************************
        				* 畫鐘、複製畫
        				*********************************************************************************************/		
								case ($S_BTYPE>='274' and $S_BTYPE<='283'):
								case '396':
								case '397':
										$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
										if ($S_BTYPE=='396' or $S_BTYPE=='397') {
												$work_process.="規格：".$print_array[$value]["PPNAME"].'，印刷品項：'.$print_array["$value"]["DBTITLE"];
												if ($print_array["$value"]["PAINTBOX"]!="") {
														$work_process.='，畫框種類：'.$print_array["$value"]["PAINTBOX"];
												} else {
														$work_process.='，畫框種類：無框畫';
												}
										} 
										$fac_html.='<td align="center">'.$work_process.'</td>';
										$fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
										$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
										$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
										break;
								
                /********************************************************************************************
        				* iphone5保護殼、行動電源、圓形鏡盒、吸水杯墊、note2保護殼
        				*********************************************************************************************/
								case ($S_BTYPE>='170' and $S_BTYPE<='177'):
                case ($S_BTYPE>='180' and $S_BTYPE<='203'):
								case ($S_BTYPE>='115' and $S_BTYPE<='122'):
								case '66':
								case '67':
								case '43':
                case '44':
                case '45':
                case '46':
                case '47':
                case '60':
                case '61':
								case '62':
                case '68':
                case '69':
                case '72':
                case '73':
								case '108':
								case '76':
										switch ($S_BTYPE) {
												//複寫聯單、信封套、木質面紙盒、Samsung S3、拼圖、note2
												case ($S_BTYPE>='170' and $S_BTYPE<='177'):
												case ($S_BTYPE>='180' and $S_BTYPE<='203'):
												case ($S_BTYPE>='115' and $S_BTYPE<='122'):
												case '66':
												case '67':
												case '108':
														$SHOW_FILENAME=$print_array["$value"]["FILENAME"];
														$SHOW_PROCESS =$work_process;
														break;
												case '76':
													  $SHOW_FILENAME=$print_array["$value"]["FILENAME"];
														$SHOW_PROCESS ="頭出左拉";
														break;
												default:
														$SHOW_FILENAME="&nbsp;";
														$SHOW_PROCESS ="&nbsp;";
														break;
										}
                    $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].$print_array[$value][COVER_KIND].'</td>';
										if ($print_array[$value][FACID]!='19' and ($S_BTYPE!='121' and $S_BTYPE!='122')) {
												$fac_html.='<td align="center">'.$print_array["$value"]["PPNAME"].'</td>';
										}
                    $fac_html.='<td align="center">'.$SHOW_PROCESS.'</td>';
                    $fac_html.='<td align="center">'.$SHOW_FILENAME.'</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
										$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
										$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
										$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
										//依您印要多約交日資訊
										if ($print_array[$value][FACID]=='23') {
												$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
										}
                    $fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>'; //備註
                    break;
                /********************************************************************************************
        				* 桌曆、掛曆
        				*********************************************************************************************/
                case 31:case 32:case 30:case 33:case 38:case 39:
                    if ($MAIL_N=='out') {
                        $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                        $fac_html.='<td align="center">'.$print_array[$value]["PPNAME"].'</td>';
                        $fac_html.='<td align="center">'.$work_process.'</td>';
                        $fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>';
                        $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
                        $fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
												$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
												$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
												$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
												$fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>'; //備註
                    } else {
                        $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                        $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
                        $fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
												$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
												$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
                    }
                    break;
                /********************************************************************************************
        				* 貼紙、無痕壁掛貼、停車証貼
        				*********************************************************************************************/
								case 48:
								case 50:
                case 74:
                case 75:
                    $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array[$value]["PPNAME"].'</td>';
                    $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
										$fac_html.='<td align="center">'.$print_array["$value"]["SIZE"].'</td>';
										$fac_html.='<td align="center">'.$work_process.'&nbsp;'.$sub_work_process.'</td>';
										$fac_html.='<td align="center">'.$WTP_ARRAY["$value"]["BOPRICEM3"].'</td>';
                    $fac_html.='<td align="center">10857台北市萬華區艋舺大道85號B1</td>';
										$fac_html.='<td align="center">02-2306-1958#151</td>';
										$fac_html.='<td align="center">賀蓉蓉</td>';
                    $fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday2).'出貨</td>';//備註
                    break;
                default:
                    switch ($print_array[$value][FACID]) {
                        //新竹物流-科谷實業表頭不同
                        case 98:
												case 97:
												case 96:
												case 95:
												case 26:
												case 27:
												case 35:
                            $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
                            $fac_html.='<td align="center">'.$print_array["$value"]["BMEMO2"].'</td>';
                            $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].$print_array["$value"]["PRODUCT_UNIT"].'</td>';
                            $fac_html.='<td align="center">'.$print_array["$value"]["UDNAME"].'</td>';
														$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
														$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
														$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
                            break;
												case 23:
														$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
														$fac_html.='<td align="center">'.$print_array[$value]["PPNAME"].'</td>';
														$fac_html.='<td align="center">'.$work_process.'</td>';
														$fac_html.='<td align="center">'.$print_array["$value"]["FILENAME"].'</td>';
														$fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
														$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
														$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
														$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
														$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
														$fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>'; //備註
														break;
                        default:
                            if (substr($value,0,1)=='S') {
                                $fac_html.='<td align="center">'.$print_array["$value"]["PDT_NAME"].'</td>';
                                $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'&nbsp;'.$print_array["$value"]["UNIT"].'</td>';
                            } else {
																if ($print_array["$value"]["BMEMO2"]!='') { //企業產品有規格資訊的帶入產品名稱後顯示
																		$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'-'.$print_array["$value"]["BMEMO2"].'</td>';
																} else {
																		$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
																}
                                $fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].$print_array["$value"]["PRODUCT_UNIT"].'</td>';
                            }
                            $fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
                            $fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
														$fac_html.='<td align="center">'.$check_group_ary["ugname"].'</td>';
                            $fac_html.='<td align="center">'.$SHOW_REC.'</td>';
                            if ($print_array["$value"]["FACID"]=='25' or $print_array["$value"]["FACID"]=='38'){
                                $fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>';
                            }
                            break;
                    }
                    break;
            }
    				$fac_html.='</tr>';
        }
				//2013/12/03 Arvin 燙金黑板要另外送給依您印，在後面將工廠清單轉換掉
				if ($print_array["$value"]["GOLD"]=='Y' and (($S_BTYPE>='124' and $S_BTYPE<='129') or $S_BTYPE==30 or $S_BTYPE==31)) {
						$fac_html='<tr>';
						$fac_html.='<td>&nbsp;</td>';
						$fac_html.='<td>'.$print_array["$value"]["BOID"].'<br>'.$print_array["$value"]["YFPBOID"].'</td>';
						$fac_html.='<td align="center">'.$print_array["$value"]["BMEMO"].'</td>';
						$fac_html.='<td align="center">'.$print_array[$value]["PPNAME"].'</td>';
						$fac_html.='<td align="center">'.$work_process.'</td>';
						$fac_html.='<td align="center">'.$print_array["$value"]["YFPBOID"].'</td>';
						$fac_html.='<td align="center">'.$print_array["$value"]["BONUM"].'</td>';
						$fac_html.='<td align="center">'.$SHOW_ADDR.'</td>';
						$fac_html.='<td align="center">'.$SHOW_TEL.'</td>';
						$fac_html.='<td align="center">'.$SHOW_REC.'</td>';
						$fac_html.='<td align="center">'.date("Y/m/d",$fac_sendday).'</td>';
						$fac_html.='<td align="center">'.$print_array["$value"]["BOMEMO"].'</td>';
				}

				$num++;
				$SQL = 'Update pordereg set FLAGPO=\'F\',TSEND=\''.$obj->date_time().'\' ';
				$SQL.= ' where BOID=\''.$print_array[$value][BOID].'\' and TSNEW=\''.$insert_time.'\'';
				$query=mssql_query($SQL,$GB_dblk);

        $SQL =' Select BOID from pordereport where boid=\''.$print_array[$value][BOID].'\' and FLAGPO=\'N\' and TSNEW=\''.$insert_time.'\' ';
        $query=mssql_query($SQL,$GB_dblk);
        $num_rows=mssql_num_rows($query);

				$S_PAPER=0;
				$D_PAPER=0;

				if ($print_array["$value"]["S_FACE"]!='') {
						$S_PAPER_TMP=explode(",",$print_array["$value"]["S_FACE"]);
						foreach ($S_PAPER_TMP as $S_PAPER_VAL) {
								$S_PAPER_TMP2=explode("_",$S_PAPER_VAL);
								$S_PAPER+=$S_PAPER_TMP2[0];
						}
				}
				if ($print_array["$value"]["D_FACE"]!='') {
						$D_PAPER_TMP=explode(",",$print_array["$value"]["D_FACE"]);
						foreach ($D_PAPER_TMP as $D_PAPER_VAL) {
								$D_PAPER_TMP2=explode("_",$D_PAPER_VAL);
								$D_PAPER+=$D_PAPER_TMP2[0];
						}
				}


        //if ($print_array["$value"]["S_FACE"]=='') $print_array["$value"]["S_FACE"]=0;
        //if ($print_array["$value"]["D_FACE"]=='') $print_array["$value"]["D_FACE"]=0;
				if ($JUST_ORDER) {
						$NOCOMBIN="Y";
				} else {
						$NOCOMBIN="";
				}
        if ($num_rows < 1) {
            $SQL =' Insert into pordereport (BOID,DATA1,DATA2,DATA1_A,DATA1_B,DATA1_C,DATA1_D,DATA1_E,DATA1_F,DATA1_G,DATA1_H,DATA1_I,DATA1_J,DATA1_K,';
            $SQL.=' DATA1_L,TSNEW,FLAGPO,S_FACE,D_FACE,NOCOMBIN) values (\''.$print_array[$value][BOID].'\',\''.base64_encode(trim($sub_html)).'\',';
            $SQL.=' \''.base64_encode(trim($fac_html)).'\',\''.$print_array[$value]["YFPBOID"].'\','.$obj->EB_sqluniencode(trim($print_array["$value"]["TITLE"])).',';
            $SQL.=' '.$obj->EB_sqluniencode(trim($print_array["$value"]["BMEMO"])).',\''.$print_array["$value"]["BONUM"].'\',';
            $SQL.=' '.$obj->EB_sqluniencode($work_process.$sub_work_process).','.$obj->EB_sqluniencode(trim($print_array["$value"]["BORADDR"])).',';
            $SQL.=' '.$obj->EB_sqluniencode(trim($print_array["$value"]["BORPHONE"])).','.$obj->EB_sqluniencode(trim($print_array["$value"]["BORNAME"])).',';
            $SQL.=' '.$obj->EB_sqluniencode(date("Y/m/d",$fac_sendday)).','.$obj->EB_sqluniencode(trim($TYPE)).',\''.trim($print_array["$value"]["VVBOID"]).'\',';
            $SQL.=' '.$obj->EB_sqluniencode($SHOW_BOINVTYPE.$print_array["$value"]["BOMEMO"].$s_memo).',\''.$insert_time.'\',\'N\',\''.$S_PAPER.'\',\''.$D_PAPER.'\',\''.$NOCOMBIN.'\')';
        } else {
            $SQL  = 'Update pordereport set DATA1=\''.base64_encode(trim($sub_html)).'\',DATA2=\''.base64_encode(trim($fac_html)).'\',';
            $SQL .= ' DATA1_A=\''.$print_array[$value]["YFPBOID"].'\',DATA1_B='.$obj->EB_sqluniencode(trim($print_array["$value"]["TITLE"])).',';
            $SQL .= ' DATA1_C='.$obj->EB_sqluniencode(trim($print_array["$value"]["BMEMO"])).',DATA1_D=\''.$print_array["$value"]["BONUM"].'\',';
            $SQL .= ' DATA1_E='.$obj->EB_sqluniencode($work_process.$sub_work_process).',DATA1_F='.$obj->EB_sqluniencode(trim($print_array["$value"]["BORADDR"])).',';
            $SQL .= ' DATA1_G='.$obj->EB_sqluniencode(trim($print_array["$value"]["BORPHONE"])).',DATA1_H='.$obj->EB_sqluniencode(trim($print_array["$value"]["BORNAME"])).',';
            $SQL .= ' DATA1_I='.$obj->EB_sqluniencode(date("Y/m/d",$fac_sendday)).',DATA1_J='.$obj->EB_sqluniencode(trim($TYPE)).',';
            $SQL .= ' DATA1_K=\''.$print_array["$value"]["VVBOID"].'\',DATA1_L='.$obj->EB_sqluniencode($SHOW_BOINVTYPE.$print_array["$value"]["BOMEMO"].$s_memo).',';
            $SQL .= ' S_FACE=\''.$S_PAPER.'\',D_FACE=\''.$D_PAPER.'\',NOCOMBIN=\''.$NOCOMBIN.'\' ';
            $SQL .= ' where BOID=\''.$print_array[$value][BOID].'\' and FLAGPO=\'N\' and TSNEW=\''.$insert_time.'\' ';
        }
				$query=mssql_query($SQL,$GB_dblk);
				//記錄用紙數量
				$SQL_PAGE =' Select BOID from PORDERBOM where boid=\''.$print_array[$value][BOID].'\' ';
				$query=mssql_query($SQL_PAGE,$GB_dblk);
				$num_rows1=mssql_num_rows($query);
				if ($num_rows1 < 1) {
						$SQL1 =' Insert into PORDERBOM (BOID,A31P,A32P) values(\''.$print_array[$value][BOID].'\',\''.$S_PAPER.'\',\''.$D_PAPER.'\') ';
				} else {
						$SQL1 =' Update PORDERBOM set A31P=\''.$S_PAPER.'\',A32P=\''.$D_PAPER.'\' where BOID=\''.$print_array[$value][BOID].'\' ';
				}
				$query=mssql_query($SQL1,$GB_dblk);
		}
	/**********************************************************************************************************************************
 	*  轉檔清冊-TOTAL for EC 用
 	**********************************************************************************************************************************/
  //判斷外包主機訂單要拆成零售及一般訂單兩個轉檔清冊
  if ($MAIL_N=='out') {
      $mail_loop=array("0","S");
  } else {
      $mail_loop=array("0");
  }
	$array_btype=array(); //依您印產品分類用
  foreach ($mail_loop as $mail_value) {

      $SQL =' Select a.BOID,a.DATA1,a.DATA2,a.DATA3,b.bovatno,b.BORNAME,b.YFPBOID,(Case When E.PPID is null then \'999\' else E.PPID end) as PPID,';
      $SQL.=' b.FACID,C.FACMAN,C.FACNAME, ';
      $SQL.=' (Case When D.BTYPE is null then \'400\' else D.BTYPE end) as BTYPE, ';
      $SQL.=' (Select top 1 isnull(GGID,\'XXXXXXX\') from pusergroup where VAT=b.bovatno) as GGID ';
      $SQL.=' from PORDEREPORT a join PORDER B on a.boid=b.boid left join WBOOK D on b.bid=d.wbid ';
      $SQL.=' join W2PFAC C on B.FACID=C.FACID ';
      $SQL.=' left join W2POP E on A.BOID=E.BOID and E.ITEM=\'PPID\' and E.V1 <\'700\' where a.FLAGPO=\'N\' and a.BOID in (';
      $SUB_MAIL='';
      foreach ($LOOP_ARRAY as $boid_value1) {
          if ($SUB_MAIL=='') {
              $SUB_MAIL.='\''.$boid_value1.'\' ';
          } else {
              $SUB_MAIL.=',\''.$boid_value1.'\' ';
          }
      }
      $SQL.=$SUB_MAIL;
      $SQL.=') ';
      if ($mail_value=='S') {
          $SQL.=' and (left(a.BOID,1)=\'S\' or B.FACID=\'25\') ';
          $SHOW_MAIL_TITLE="零售產品-";
          $MAIL_N='out_s';
      } else {
          $SHOW_MAIL_TITLE=$B_MAIL_TITLE;
          $SQL.=' and (left(a.BOID,1) <> \'S\' and B.FACID<>\'25\') ';
      }
      $SQL.=' order by b.BORNAME,B.YFPBOID';

    	$query=mssql_query($SQL,$GB_dblk);
    	$count=mssql_num_rows($query);

    	$sub_html='';
      $last_borname="";
    	if ($count > 0) {
    			$html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    			$html.='<html xmlns="http://www.w3.org/1999/xhtml">';
    			$html.='<head>';
    			$html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    			$html.='<title>W2P轉檔清冊'.date("Y-m-d H:i",$insert_time).'</title>';
    			$html.='</head>';
    			$html.='<body>';
    			$html.='<center>總訂單筆數='.$count.'筆';
    			while($result= mssql_fetch_array($query)) {
    					$FACID    = trim($result[FACID]);
    					$FACNAME  = iconv("BIG5","UTF-8",trim($result[FACNAME]));
    					$BOID     = trim($result[BOID]);
    					$FACMAN   = iconv("BIG5","UTF-8",trim($result[FACMAN]));
    					$BORNAME  = trim($result[BORNAME]);
              $YFPBOID  = trim($result[YFPBOID]);
              $BTYPE    = trim($result[BTYPE]);
              $sub_html = base64_decode(trim($result[DATA1]));
              if ($last_borname=='') {
                  $last_borname=$BORNAME;
                  $head=$obj->list_mail_head($MAIL_N,$BTYPE);
                  $html.=$head;
              }
              if ($BORNAME!=$last_borname) {
                  $html.='</table>';
                  $html.='<BR><BR>';
                  $head=$obj->list_mail_head($MAIL_N,$BTYPE);
                  $html.=$head;
                  $last_borname=$BORNAME;
              }
              $html.=$sub_html;
    					$totl_send_array[]=$BOID;
							if ($FACID==23) {
									$array_btype[$BTYPE][]=$BOID;
							}
						
              if ($BTYPE>='142' and $BTYPE<='163') {
                  $PAINT_SEND_ARRAY[$FACID][]=$BOID;
							} elseif ($BTYPE>='232' and $BTYPE <='236') {
									$NEW_PAINT_SEND_ARRAY[$FACID][]=$BOID;
              } elseif ($print_array[$BOID]["BIND"]=='804' or $print_array[$BOID]["BIND"]=='805') {
                  $BEAUTIFLY_SEND_ARRAY[$FACID][]=$BOID;
              } else {
                  switch ($BTYPE) {
                      //拼圖雖然FACID=10但是還是要發轉檔清冊給英傑特
                      case ($BTYPE>='115' and $BTYPE<='122'):
													if ($FACID=='10') {
															if ($BTYPE=='121' or $BTYPE=='122') {
																	$S_FACID='19';
															} else {
																	$S_FACID='23';
																	$array_btype[$BTYPE][]=$BOID;
															}
															$SQL="select * from W2PFAC where FACID='".$S_FACID."' ";
															$query1=mssql_query($SQL,$GB_dblk);
															while($result1= mssql_fetch_array($query1)) {
																	$MAP_ARRAY[NAME][$S_FACID] = iconv("BIG5","UTF-8",trim($result1[FACNAME]));
																	$MAP_ARRAY[FACMAN][$S_FACID]= iconv("BIG5","UTF-8",trim($result1[FACMAN]));
															}
															$FAC_SEND_ARRAY[$S_FACID][]=$BOID;
													} else {
															$FAC_SEND_ARRAY[$FACID][]=$BOID;
													}
                          break;
											//判斷喜帖有燙金要額外傳送資訊給依您印
											case ($BTYPE>='124' and $BTYPE<='129'):
											case 30:
											case 31:
													if ($print_array[$BOID]["GOLD"]=='Y' and $FACID!='23') {
															$SQL='Select * from W2PFAC where FACID=\'23\'';
															$query1=mssql_query($SQL,$GB_dblk);
															while($result1= mssql_fetch_array($query1)) {
																	$MAP_ARRAY[NAME]['23'] = iconv("BIG5","UTF-8",trim($result1[FACNAME]));
																	$MAP_ARRAY[FACMAN]['23']= iconv("BIG5","UTF-8",trim($result1[FACMAN]));
															}
															$FAC_SEND_ARRAY['23'][]=$BOID;
															$array_btype["GOLD"][]=$BOID;
													}
													$FAC_SEND_ARRAY[$FACID][]=$BOID;
													break;
											//判斷京城銀行的企業下單產品(BTYPE=40)與依您印原本的產品拆開來顯示
											case 40:
													if ($FACID!=23) {
															$FAC_SEND_ARRAY[$FACID][]=$BOID;
													} else {
															$EXTRA_BANK_ARRAY[$FACID][]=$BOID;
													}
													break;
                      default:
                          $FAC_SEND_ARRAY[$FACID][]=$BOID;
                          break;
                  }
              }
    					$MAP_ARRAY[NAME][$FACID]=$FACNAME;
    					$MAP_ARRAY[DATA][$BOID]=base64_decode(trim($result[DATA2]));
              $MAP_ARRAY[BTYPE][$BOID]=$BTYPE;
    					$MAP_ARRAY[FACMAN][$FACID]=$FACMAN;
              if (trim($result[DATA3])!='') {
                  $MAP_WORK_ARRAY[DATA][$BOID]=base64_decode(trim($result[DATA3]));
              }
    			}
    			$html.='</table>';
    			$html.='</body>';
    			$html.='</html>';

          $s_html = $DATETIME."_total_".$MAIL_N.".html";
    	    $fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
    	    fseek($fileopen,0);
    	    fwrite($fileopen,$html);
    	    fclose($fileopen);

        	$title =$SHOW_MAIL_TITLE.'W2P轉檔清冊'.date("Y-m-d H:i",$insert_time);
          $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

					$boundary = uniqid( "");

					$headers ="From: webmaster@cloudw2p.com"."\r\n";
					$headers.="Cc:".$INI_SET[master]."\r\n";
					$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
					$headers.="X-Priority: 1"."\r\n";
					$headers.="X-MSMail-Priority: High"."\r\n";
					$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

					$read = base64_encode($html);
					$read = chunk_split($read);

					$emailBody = '--'.$boundary."\n";
					$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

					$emailBody.= $read."\n"; // 本文內容

					$emailBody.= '--'.$boundary."\r\n";
					$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_total_'.$MAIL_N.'.html'."\r\n";
					$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
					$emailBody.= $read."\r\n";
					$emailBody.="--$boundary--";

					if ($mail_value=='S') {
							$mail_adr=trim($INI_SET[MailCato]).",herlonglong@email.yfp.com.tw";
					} else {
							$mail_adr=trim($INI_SET[MailCato]);
					}

    			$result=mail($mail_adr, $subject, $emailBody, $headers);
    			if ($result) {
    					$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊".date("Y-m-d H:i",$insert_time)."寄送完成");
    					if (is_array($totl_send_array)) {
    							foreach ($totl_send_array as $value) {
    									$SQL ='Update PORDEREPORT set TSMAIL=\''.$obj->date_time().'\',FLAGPO=\'O\' where BOID=\''.$value.'\' ';
    									$query=mssql_query($SQL,$GB_dblk);

    									$SQL = 'Update pordereg set FLAGPO=\'F\',TSEND=\''.$obj->date_time().'\' ';
    									$SQL.= ' where BOID=\''.$value.'\' and TSNEW=\''.$insert_time.'\'';
    									$query=mssql_query($SQL,$GB_dblk);
    							}
    					}
    			} else {
    					$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊".date("Y-m-d H:i",$insert_time)."寄送失敗");
    			}
    	}
  }
  /**********************************************************************************************************************************
 	*  轉檔清冊 for 蝴蝶裝
 	**********************************************************************************************************************************/
  if (count($BEAUTIFLY_SEND_ARRAY) > 0) {
			foreach ($BEAUTIFLY_SEND_ARRAY as $bkey =>$bvalue) {
					$sum=count($BEAUTIFLY_SEND_ARRAY[$bkey]);
					$beautifly_html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
					$beautifly_html.='<html xmlns="http://www.w3.org/1999/xhtml">';
					$beautifly_html.='<head>';
					$beautifly_html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
					$beautifly_html.='<title>W2P轉檔清冊'.date("Y-m-d H:i",$insert_time).'-'.$MAP_ARRAY[NAME][$bkey].'</title>';
					$beautifly_html.='</head>';
					$beautifly_html.='<body>';
					$beautifly_html.='<center><font size="32">'.date("Y-m-d",$insert_time).'訂單   共'.$sum.'筆</font>';
					$beautifly_html.='<table border="1" cellpadding="0" width="100%">';
					$beautifly_html.='<tr align="center" style="font-size:24px">';
					$beautifly_html.='<td><strong>項</strong></td>';
					$beautifly_html.='<td><strong>永豐編號</strong></td>';
					$beautifly_html.='<td><strong>產品類型</strong></td>';
					$beautifly_html.='<td><strong>份數</strong></td>';
					$beautifly_html.='<td><strong>大圖</strong></td>';
					$beautifly_html.='<td><strong>內頁</strong></td>';
					$beautifly_html.='<td><strong>約交日</strong></td>';
					$beautifly_html.='<td><strong>備註</strong></td>';
					$beautifly_html.='</tr>';

					foreach ($BEAUTIFLY_SEND_ARRAY[$bkey] as $bvalue1) {
						 $beautifly_html.=$MAP_ARRAY[DATA][$bvalue1];
					}
					$beautifly_html.='</table>';
					$beautifly_html.='</body>';
					$beautifly_html.='</html>';
					$s_html = $DATETIME."_".$bkey."_1.html";
					$fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
					fseek($fileopen,0);
					fwrite($fileopen,$beautifly_html);
					fclose($fileopen);
					$title =$B_MAIL_TITLE.'W2P轉檔清冊-蝴蝶裝';
					$subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

					$boundary = uniqid( "");

					$headers ="From: webmaster@cloudw2p.com"."\r\n";
					//$headers.="Cc:junior@email.yfp.com.tw"."\r\n";
					$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
					$headers.="X-Priority: 1"."\r\n";
					$headers.="X-MSMail-Priority: High"."\r\n";
					$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

					$read = base64_encode($beautifly_html);
					$read = chunk_split($read);

					$emailBody = '--'.$boundary."\n";
					$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

					$emailBody.= $read."\n"; // 本文內容

					$emailBody.= '--'.$boundary."\r\n";
					$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_mail_'.$pkey.'_1.html'."\r\n";
					$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
					$emailBody.= $read."\r\n";
					$emailBody.="--$boundary--";

					//$result=mail("herlonglong@email.yfp.com.tw", $subject, $emailBody, $headers);
					if ($result) {
							$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊-蝴蝶裝寄送完成");
					} else {
							$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊-蝴蝶裝寄送失敗");
					}
			}
  }


	/**********************************************************************************************************************************
 	*  轉檔清冊 for 無框畫
 	**********************************************************************************************************************************/
  if (count($PAINT_SEND_ARRAY) > 0) {
      foreach ($PAINT_SEND_ARRAY as $pkey =>$pvalue) {
					$sum=count($PAINT_SEND_ARRAY[$pkey]);
          $paint_html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    			$paint_html.='<html xmlns="http://www.w3.org/1999/xhtml">';
    			$paint_html.='<head>';
    			$paint_html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    			$paint_html.='<title>W2P轉檔清冊'.date("Y-m-d H:i",$insert_time).'-'.$MAP_ARRAY[NAME][$key].'</title>';
    			$paint_html.='</head>';
    			$paint_html.='<body>';
    			$paint_html.='<center>'.date("Y-m-d H:i",$insert_time).'訂單   共'.$sum.'筆';
    			$paint_html.='<table border="1" cellpadding="0" width="100%">';
          $paint_html.='<tr>';
    			$paint_html.='<td><p align="center"><strong>項</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>永豐編號</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>檔案名稱</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>作品名稱</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>訂購時間</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>產品類型</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>份數</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>大圖</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>約交日</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>備註</strong></p></td>';
          $paint_html.='</tr>';

          foreach ($PAINT_SEND_ARRAY[$pkey] as $pvalue1) {
    	       $paint_html.=$MAP_ARRAY[DATA][$pvalue1];
          }
    			$paint_html.='</table>';
    			$paint_html.='</body>';
    			$paint_html.='</html>';
      	  $s_html = $DATETIME."_".$pkey."_2.html";
    	    $fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
    	    fseek($fileopen,0);
    	    fwrite($fileopen,$paint_html);
    	    fclose($fileopen);
        	$title =$B_MAIL_TITLE.'W2P轉檔清冊-無框畫';
          $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

					$boundary = uniqid( "");

					$headers ="From: webmaster@cloudw2p.com"."\r\n";
					//$headers.="Cc:junior@email.yfp.com.tw"."\r\n";
					$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
					$headers.="X-Priority: 1"."\r\n";
					$headers.="X-MSMail-Priority: High"."\r\n";
					$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

					$read = base64_encode($paint_html);
					$read = chunk_split($read);

					$emailBody = '--'.$boundary."\n";
					$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

					$emailBody.= $read."\n"; // 本文內容

					$emailBody.= '--'.$boundary."\r\n";
					$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_mail_'.$pkey.'_2.html'."\r\n";
					$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
					$emailBody.= $read."\r\n";
					$emailBody.="--$boundary--";


    			//$result=mail("herlonglong@email.yfp.com.tw", $subject, $emailBody, $headers);
    			if ($result) {
    					$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊-無框畫寄送完成");
    			} else {
    					$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊-無框畫寄送失敗");
    			}
      }
  }

	/**********************************************************************************************************************************
 	*  轉檔清冊 for 新版無框畫
 	**********************************************************************************************************************************/
	/*
  if (count($NEW_PAINT_SEND_ARRAY) > 0) {
      foreach ($NEW_PAINT_SEND_ARRAY as $pkey =>$pvalue) {
					$sum=count($NEW_PAINT_SEND_ARRAY[$pkey]);
          $paint_html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    			$paint_html.='<html xmlns="http://www.w3.org/1999/xhtml">';
    			$paint_html.='<head>';
    			$paint_html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    			$paint_html.='<title>W2P轉檔清冊'.date("Y-m-d H:i",$insert_time).'-'.$MAP_ARRAY[NAME][$key].'</title>';
    			$paint_html.='</head>';
    			$paint_html.='<body>';
    			$paint_html.='<center>'.date("Y-m-d H:i",$insert_time).'訂單   共'.$sum.'筆';
    			$paint_html.='<table border="1" cellpadding="0" width="100%">';
          $paint_html.='<tr>';
    			$paint_html.='<td><p align="center"><strong>項</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>永豐編號</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>檔案名稱</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>作品名稱</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>訂購時間</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>產品類型</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>份數</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>大圖</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>約交日</strong></p></td>';
    			$paint_html.='<td><p align="center"><strong>備註</strong></p></td>';
          $paint_html.='</tr>';

          foreach ($NEW_PAINT_SEND_ARRAY[$pkey] as $pvalue1) {
    	       $paint_html.=$MAP_ARRAY[DATA][$pvalue1];
          }
    			$paint_html.='</table>';
    			$paint_html.='</body>';
    			$paint_html.='</html>';
      	  $s_html = $DATETIME."_".$pkey."_3.html";
    	    $fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
    	    fseek($fileopen,0);
    	    fwrite($fileopen,$paint_html);
    	    fclose($fileopen);
        	$title =$B_MAIL_TITLE.'W2P轉檔清冊-新厚無框畫';
          $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

					$boundary = uniqid( "");

					$headers ="From: webmaster@cloudw2p.com"."\r\n";
					$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
					$headers.="X-Priority: 1"."\r\n";
					$headers.="X-MSMail-Priority: High"."\r\n";
					$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

					$read = base64_encode($paint_html);
					$read = chunk_split($read);

					$emailBody = '--'.$boundary."\n";
					$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

					$emailBody.= $read."\n"; // 本文內容

					$emailBody.= '--'.$boundary."\r\n";
					$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_mail_'.$pkey.'_2.html'."\r\n";
					$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
					$emailBody.= $read."\r\n";
					$emailBody.="--$boundary--";


    			//$result=mail("herlonglong@email.yfp.com.tw", $subject, $emailBody, $headers);
    			if ($result) {
    					$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊-新版無框畫寄送完成");
    			} else {
    					$obj->ll_echo("[".$obj->show_time()."]".$B_MAIL_TITLE."W2P轉檔清冊-無框畫寄送失敗");
					}
      }
  }
	*/

	/**********************************************************************************************************************************
 	*  轉檔清冊 for 京城銀行  京城銀行的企業下單產品(BTYPE=40)與依您印原本的產品拆開來顯示
 	**********************************************************************************************************************************/

  if (count($EXTRA_BANK_ARRAY) > 0) {
      foreach ($EXTRA_BANK_ARRAY as $bankkey =>$pvalue) {
					$sum=count($EXTRA_BANK_ARRAY[$bankkey]);
          $bank_html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    			$bank_html.='<html xmlns="http://www.w3.org/1999/xhtml">';
    			$bank_html.='<head>';
    			$bank_html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    			$bank_html.='<title>W2P轉檔清冊'.date("Y-m-d H:i",$insert_time).'-'.$MAP_ARRAY[NAME][$key].'</title>';
    			$bank_html.='</head>';
    			$bank_html.='<body>';
    			$bank_html.='<center>'.date("Y-m-d H:i",$insert_time).'訂單   共'.$sum.'筆';
    			$bank_html.='<table border="1" cellpadding="0" width="100%">';

					$bank_html.='<tr>';
					$bank_html.='<td><p align="center"><strong>項</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>平台編號</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>產品類型</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>紙別</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>加工條件</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>檔案名稱</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>數量</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>地址</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>電話</strong></p></td>';
					$bank_html.='<td><p align="center"><strong>收件人 </strong></p></td>';
					$bank_html.='<td><p align="center"><strong>備註 </strong></p></td>';
					$bank_html.='</tr>';

          foreach ($EXTRA_BANK_ARRAY[$bankkey] as $bankvalue1) {
    	       $bank_html.=$MAP_ARRAY[DATA][$bankvalue1];
          }
    			$bank_html.='</table>';
    			$bank_html.='</body>';
    			$bank_html.='</html>';
      	  $s_html = $DATETIME."_".$bankkey."_bank.html";
    	    $fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
    	    fseek($fileopen,0);
    	    fwrite($fileopen,$bank_html);
    	    fclose($fileopen);
        	$title =$B_MAIL_TITLE.'W2P轉檔清冊-京城銀行';
          $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

					$boundary = uniqid("");

					$headers ="From: webmaster@cloudw2p.com"."\r\n";
					$headers.="Cc:herlonglong@email.yfp.com.tw"."\r\n";
					$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
					$headers.="X-Priority: 1"."\r\n";
					$headers.="X-MSMail-Priority: High"."\r\n";
					$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

					$read = base64_encode($bank_html);
					$read = chunk_split($read);

					$emailBody = '--'.$boundary."\n";
					$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

					$emailBody.= $read."\n"; // 本文內容

					$emailBody.= '--'.$boundary."\r\n";
					$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_mail_'.$bankkey.'_bank.html'."\r\n";
					$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
					$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
					$emailBody.= $read."\r\n";
					$emailBody.="--$boundary--";



					$tmp_string=$MAP_ARRAY[FACMAN][$bankkey];
          //有存放收件人才寄
          if ($tmp_string!='') {
        			$tmp_array =explode(",",$tmp_string);
        			$for_count= count($tmp_array);
        			$mail_to ='';
        			$f_i=0;
        			while ($f_i < $for_count) {
        					$f_n=$f_i+1;
        					if ($mail_to=='') {
        							$mail_to  = $tmp_array[$f_n];
        					} else {
        							$mail_to .= ",".$tmp_array[$f_n];
        					}
        					$f_i = $f_i+2;
        			}
        			$result=mail($mail_to, $subject, $emailBody, $headers);
        			if ($result) {
        					$obj->ll_echo("[".$obj->show_time()."]".$T_B_MAIL_TITLE."W2P轉檔清冊-".$MAP_ARRAY[NAME][$key]."(京城銀行)寄送完成");
        			} else {
        					$obj->ll_echo("[".$obj->show_time()."]".$T_B_MAIL_TITLE."W2P轉檔清冊-".$MAP_ARRAY[NAME][$key]."(京城銀行)寄送失敗");
        			}
          }
      }
  }




  if (count($FAC_SEND_ARRAY) > 0) {
    	foreach ($FAC_SEND_ARRAY as $key => $value) {
    			$sum=count($FAC_SEND_ARRAY[$key]);
    			$html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
    			$html.='<html xmlns="http://www.w3.org/1999/xhtml">';
    			$html.='<head>';
    			$html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
    			$html.='<title>W2P轉檔清冊'.date("Y-m-d H:i",$insert_time).'-'.$MAP_ARRAY[NAME][$key].'</title>';
    			$html.='</head>';
    			$html.='<body>';
    			$html.='<center>'.date("Y-m-d H:i",$insert_time).'訂單   共'.$sum.'筆';
    			$html.='<table border="1" cellpadding="0" width="100%">';
          switch ($key) {
              //英傑特
              case 19:
							case 38:
                  $html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
                  if ($key=='38') {
                     $html.='<td><p align="center"><strong>紙別</strong></p></td>';
                  }
                  $html.='<td><p align="center"><strong>加工條件</strong></p></td>';
                  $html.='<td><p align="center"><strong>檔案名稱</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
                  $html.='<td><p align="center"><strong>備註 </strong></p></td>';
            			$html.='</tr>';
									break;
							case 33:
                  $html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
									// $html.='<td><p align="center"><strong>預覽</strong></p></td>';
                  // $html.='<td><p align="center"><strong>物件編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
                  $html.='<td><p align="center"><strong>備註 </strong></p></td>';
            			$html.='</tr>';
                  break;
							//畫鐘廠商
							case 41:
									$html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
									$html.='<td><p align="center"><strong>加工條件</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
                  $html.='<td><p align="center"><strong>約交日 </strong></p></td>';
            			$html.='</tr>';
									break;
              //美和盛(廣告面紙)
              case 13:
                  $html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
                  $html.='<td><p align="center"><strong>抽數</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
                  $html.='<td><p align="center"><strong>檔案名稱</strong></p></td>';
                  $html.='<td><p align="center"><strong>訂貨廠商</strong></p></td>';
                  $html.='<td><p align="center"><strong>結帳方式</strong></p></td>';
                  $html.='<td><p align="center"><strong>送貨方式</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
                	$html.='<td><p align="center"><strong>約交日 </strong></p></td>';
                 	$html.='<td><p align="center"><strong>備註 </strong></p></td>';
            			$html.='</tr>';
                  break;
              //白紗
              case 12:
                  $html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
                  $html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
            			$html.='</tr>';
                  break;
              //新竹物流-科谷實業
              case 98:
							case 97:
							case 96:
							case 95:
							case 26:
							case 27:
							case 35:
                  $html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>訂單編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>品名</strong></p></td>';
                  $html.='<td><p align="center"><strong>規格</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
                  $html.='<td><p align="center"><strong>站所</strong></p></td>';
            			$html.='<td><p align="center"><strong>送貨地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
            			$html.='</tr>';
									//天威要多做外箱標籤
									if ($key==27) {

											$box_file=$ROOT_FOLDER.'./transfer/resource/BOX_STICKER.pdf';
											$sticker_info=$obj->get_pdfdata($box_file);

											$box_stickerfile=$ROOT_FOLDER.'./transfer/box_sticker/'.$DATETIME.".pdf";
											$pdfhw->begin_document($box_stickerfile, "optimize=true compatibility=1.5");
											$pdfhw->set_parameter("errorpolicy", "return");
											$pdfhw->set_parameter("textformat", "utf8");
											$pdfhw->set_info("Creator", "YFP");
											$pdfhw->set_info("Author",  "Arvin");
											$pdfhw->set_info("Title",   "BOX_STICKER");
											$doc = $pdfhw->open_pdi_document($box_file, "");
											$pagehw = $pdfhw->open_pdi_page($doc, 1, "");

											$px=2.83464565;
											$yfpboid_x1=33*$px;
											$yfpboid_x2=173*$px;
											$sticker_yfpboid_y=23*$px;

											$pte_x1=88*$px;
											$pte_x2=228*$px;
											$sticker_pet_y=23*$px;

											$customer_x1=24*$px;
											$customer_x2=164*$px;
											$sticker_customer_y=31*$px;

											$product_title_x1=24*$px;
											$product_title_x2=164*$px;
											$sticker_product_title_y=41*$px;

											$product_info_x1=24*$px;
											$product_info_x2=164*$px;
											$sticker_info_y=50*$px;

											$num_x1=114*$px;
											$num_x2=254*$px;
											$sticker_num_y=58*$px;

									}

                  break;
              case 22:
                  $html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
                  $html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>規格</strong></p></td>';
                  $html.='<td><p align="center"><strong>加工條件</strong></p></td>';
                  $html.='<td><p align="center"><strong>成本</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
                 	$html.='<td><p align="center"><strong>備註 </strong></p></td>';
            			$html.='</tr>';
                  break;
               //工商日誌-邦迪
               case 37:
                  $html.='<tr>';
                  $html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
                  $html.='<td><p align="center"><strong>訂單日期</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
                  $html.='<td><p align="center"><strong>出貨日 </strong></p></td>';
                  break;
							case 18:
							case 21:
									$html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
									$html.='<td><p align="center"><strong>條碼</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
									$html.='<td><p align="center"><strong>訂購單位</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
            			$html.='</tr>';
									break;
							case 14:
							case 25:
							case 33:
							case 34:
							case 88:
									$html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
									$html.='<td><p align="center"><strong>備註 </strong></p></td>';
            			$html.='</tr>';
                  break;
									break;
              default:
                  $html.='<tr>';
            			$html.='<td><p align="center"><strong>項</strong></p></td>';
            			$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
            			$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
            			$html.='<td><p align="center"><strong>數量</strong></p></td>';
            			$html.='<td><p align="center"><strong>地址</strong></p></td>';
            			$html.='<td><p align="center"><strong>電話</strong></p></td>';
									$html.='<td><p align="center"><strong>訂購單位</strong></p></td>';
            			$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
            			$html.='</tr>';
                  break;
          }
    			foreach ($FAC_SEND_ARRAY[$key] as $value1) {
							switch ($key) {
									case "27":
											$html.=$MAP_ARRAY[DATA][$value1];
											$sticker_loop=ceil($print_array["$value1"]["BONUM"]/2);
											$sticker_count_i=1;
											for ($sticker_i=1;$sticker_i<=$sticker_loop;$sticker_i++) {
													$pdfhw->begin_page_ext($sticker_info[width],$sticker_info[height], "topdown");

													$pdfhw->fit_pdi_page($pagehw,0, $sticker_info[height], "boxsize={".$sticker_info[width]." ".$sticker_info[height]."} fitmethod=entire");

													$pdfhw->set_parameter("FontOutline", "font_arial=c:\\windows\\fonts\\simhei.ttf");		// 內建中黑體
													$font = $pdfhw->load_font("font_arial", "unicode", "embedding");
													$pdfhw->setfont($font, 18);
													$pdfhw->setcolor("fillstroke", "cmyk", 1, 1, 1, 1);

													$pdfhw->set_text_pos($yfpboid_x1, $sticker_yfpboid_y);
													$pdfhw->show($print_array["$value1"]["YFPBOID"]);

													$pdfhw->set_text_pos($pte_x1, $sticker_pet_y);
													$pdfhw->show($print_array[$value1]["DM_PTE_NO"]);

													$pdfhw->set_text_pos($customer_x1, $sticker_customer_y);
													$pdfhw->show($print_array["$value1"]["BOINVTITLE"]);

													$pdfhw->set_text_pos($product_title_x1, $sticker_product_title_y);
													$pdfhw->show($print_array["$value1"]["BMEMO"]);

													$pdfhw->set_text_pos($product_info_x1, $sticker_info_y);
													$pdfhw->show($print_array["$value1"]["BMEMO2"]);

													$pdfhw->set_text_pos($num_x1, $sticker_num_y);
													$pdfhw->show($sticker_count_i);

													if ($print_array["$value1"]["BONUM"] >=$sticker_i*2) {
															$pdfhw->set_text_pos($yfpboid_x2, $sticker_yfpboid_y);
															$pdfhw->show($print_array["$value1"]["YFPBOID"]);

															$pdfhw->set_text_pos($pte_x2, $sticker_pet_y);
															$pdfhw->show($print_array[$value1]["DM_PTE_NO"]);

															$pdfhw->set_text_pos($customer_x2, $sticker_customer_y);
															$pdfhw->show($print_array["$value1"]["BOINVTITLE"]);

															$pdfhw->set_text_pos($product_title_x2, $sticker_product_title_y);
															$pdfhw->show($print_array["$value1"]["BMEMO"]);

															$pdfhw->set_text_pos($product_info_x2, $sticker_info_y);
															$pdfhw->show($print_array["$value1"]["BMEMO2"]);

															$pdfhw->set_text_pos($num_x2, $sticker_num_y);
															$pdfhw->show(($sticker_count_i+1));
													}
													$pdfhw->end_page_ext("");
													$sticker_count_i+=2;
											}
											break;
									//case "23": //依您印的清冊下面要重新整理再寄，所以這邊不存清冊資料存入html變數
											//break;
									default:
											$html.=$MAP_ARRAY[DATA][$value1];
											break;
							}
							if ($MAP_ARRAY[BTYPE][$value1]>='115' and $MAP_ARRAY[BTYPE][$value1]<='122') {
                  $show_key=$key."_1";
							} elseif ($MAP_ARRAY[BTYPE][$value1]>='124' and $MAP_ARRAY[BTYPE][$value1]<='129') {
									$show_key=$key."_2";
              } else {
                  $show_key=$key;
              }
    			}
					switch ($key) {
							case "23":
									//$array_btype[$BTYPE]=$BOID;
									foreach ($array_btype as $c_b => $v_ary) {
											$html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
											$html.='<html xmlns="http://www.w3.org/1999/xhtml">';
											$html.='<head>';
											$html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
											$html.='<title>W2P轉檔清冊'.date("Y-m-d H:i",$insert_time).'-'.$MAP_ARRAY[NAME][$key].'</title>';
											$html.='</head>';
											$html.='<body>';
											$html.='<center>'.date("Y-m-d H:i",$insert_time).'訂單   共'.count($v_ary).'筆';
											$html.='<table border="1" cellpadding="0" width="100%">';
											$html.='<tr>';
											$html.='<td><p align="center"><strong>項</strong></p></td>';
											$html.='<td><p align="center"><strong>平台編號</strong></p></td>';
											$html.='<td><p align="center"><strong>產品類型</strong></p></td>';
											$html.='<td><p align="center"><strong>紙別</strong></p></td>';
											$html.='<td><p align="center"><strong>加工條件</strong></p></td>';
											$html.='<td><p align="center"><strong>檔案名稱</strong></p></td>';
											$html.='<td><p align="center"><strong>數量</strong></p></td>';
											$html.='<td><p align="center"><strong>地址</strong></p></td>';
											$html.='<td><p align="center"><strong>電話</strong></p></td>';
											$html.='<td><p align="center"><strong>收件人 </strong></p></td>';
											$html.='<td><p align="center"><strong>約交日 </strong></p></td>';
											$html.='<td><p align="center"><strong>備註 </strong></p></td>';
											$html.='</tr>';
											foreach ($v_ary as $v_data) {
													$html.=$MAP_ARRAY[DATA][$v_data];
											}
											
											$html.='</table>';
											$html.='</body>';
											$html.='</html>';
											
											$s_html = $DATETIME."_".$c_b."_".$show_key.".html";
											$fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
											fseek($fileopen,0);
											fwrite($fileopen,$html);
											fclose($fileopen);
											
											$title ='外包產品-W2P轉檔清冊-'.$MAP_ARRAY[NAME][$key];
											$subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

											$BCC=$INI_SET[BCC];

											$boundary = uniqid("");

											$headers ="From: webmaster@cloudw2p.com"."\r\n";
											$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
											$headers.="X-Priority: 1"."\r\n";
											$headers.="X-MSMail-Priority: High"."\r\n";
											$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

											$read = base64_encode($html);
											$read = chunk_split($read);

											$emailBody = '--'.$boundary."\n";
											$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
											$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

											$emailBody.= $read."\n"; // 本文內容

											$emailBody.= '--'.$boundary."\r\n";
											$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_mail_'.$show_key.'.html'."\r\n";
											$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
											$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
											$emailBody.= $read."\r\n";
											
											$emailBody.="--$boundary--";
											$tmp_string=$MAP_ARRAY[FACMAN][$key];
											//判斷是否從後台的【只轉單不轉檔】按鈕執行的，若是不寄發通知給廠商，將mail轉到自己身上
											if ($JUST_ORDER) {
													$tmp_string="A,arvin.chen@email.yfp.com.tw";
											}
											//有存放收件人才寄
											if ($tmp_string!='') {
													$tmp_array =explode(",",$tmp_string);
													$for_count= count($tmp_array);
													$mail_to ='';
													$f_i=0;
													while ($f_i < $for_count) {
															$f_n=$f_i+1;
															if ($mail_to=='') {
																	$mail_to  = $tmp_array[$f_n];
															} else {
																	$mail_to .= ",".$tmp_array[$f_n];
															}
															$f_i = $f_i+2;
													}
													$result=mail($mail_to, $subject, $emailBody, $headers);
													if ($result) {
															$obj->ll_echo("[".$obj->show_time()."]外包產品-W2P轉檔清冊-".$MAP_ARRAY[NAME][$key]."寄送完成");
													} else {
															$obj->ll_echo("[".$obj->show_time()."]外包產品-W2P轉檔清冊-".$MAP_ARRAY[NAME][$key]."寄送失敗");
													}
											}
									}										
									break;
							default:
									if ($key==27) {
											//天威要多做外箱標籤
											$pdfhw->close_pdi_page( $pagehw);
											$pdfhw->close_pdi_document( $doc);
											$pdfhw->end_document("");
									}
									$html.='</table>';
									$html.='</body>';
									$html.='</html>';
									$s_html = $DATETIME."_".$show_key.".html";
									$fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
									fseek($fileopen,0);
									fwrite($fileopen,$html);
									fclose($fileopen);

									if ($key==19) {
											$T_B_MAIL_TITLE="外包產品-"; //拼圖、吸水杯墊用
									} else {
											$T_B_MAIL_TITLE=$B_MAIL_TITLE;
									}
									if ($key=='98' or $key=='96' or $key=='97' or $key=='95') {
											$title =date("Y-m-d H:i",$insert_time)." 新竹物流每日訂購明細";
									} elseif ($key=='34') {
											$title =date("Y-m-d H:i",$insert_time)." 雲端印刷網每日訂購明細";
									} else {
											$title =$T_B_MAIL_TITLE.'W2P轉檔清冊-'.$MAP_ARRAY[NAME][$key];
									}
									$subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

									$BCC=$INI_SET[BCC];

									$boundary = uniqid("");

									$headers ="From: webmaster@cloudw2p.com"."\r\n";
									//$headers.="BCC:".$BCC."\r\n";
									$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
									$headers.="X-Priority: 1"."\r\n";
									$headers.="X-MSMail-Priority: High"."\r\n";
									$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

									$read = base64_encode($html);
									$read = chunk_split($read);

									$emailBody = '--'.$boundary."\n";
									$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
									$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

									$emailBody.= $read."\n"; // 本文內容

									$emailBody.= '--'.$boundary."\r\n";
									$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_mail_'.$show_key.'.html'."\r\n";
									$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
									$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
									$emailBody.= $read."\r\n";
									switch ($key) {
											case 27:
													$att_file = fopen($box_stickerfile,"r");
													//把要夾的檔案讀出來
													$att_data = fread($att_file,filesize($box_stickerfile));
													fclose($att_file);
													//編碼後以固定長度斷行
													$read1 = chunk_split(base64_encode($att_data));
													$emailBody.= "\r\n";
													$emailBody.= '--'.$boundary."\r\n";
													$emailBody.= 'Content-Type: application/octet-stream; name='.basename($box_stickerfile)."\r\n";
													$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
													$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
													$emailBody.= $read1."\r\n";
													$emailBody.="--$boundary--";
													break;
											// case 33:
													// $prv_3d=$GB_BOOKPATH.$BID."\\cover.jpg";
													// $att_file = fopen($prv_3d,"r");
													//把要夾的檔案讀出來
													// $att_data = fread($att_file,filesize($prv_3d));
													// fclose($att_file);
													//編碼後以固定長度斷行
													// $read1 = chunk_split(base64_encode($att_data));
													// $emailBody.= "\r\n";
													// $emailBody.= '--'.$boundary."\r\n";
													// $emailBody.= 'Content-Type: application/octet-stream; name='.basename($prv_3d)."\r\n";
													// $emailBody.= 'Content-disposition: inline; attachment'."\r\n";
													// $emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
													// $emailBody.= $read1."\r\n";
													// $emailBody.="--$boundary--";
													// break;
											default:
													$emailBody.="--$boundary--";
													break;
									}
									$tmp_string=$MAP_ARRAY[FACMAN][$key];
									//判斷是否從後台的【只轉單不轉檔】按鈕執行的，若是不寄發通知給廠商，將mail轉到自己身上
									if ($JUST_ORDER) {
											$tmp_string="A,arvin.chen@email.yfp.com.tw";
									}
									//有存放收件人才寄
									if ($tmp_string!='') {
											$tmp_array =explode(",",$tmp_string);
											$for_count= count($tmp_array);
											$mail_to ='';
											$f_i=0;
											while ($f_i < $for_count) {
													$f_n=$f_i+1;
													if ($mail_to=='') {
															$mail_to  = $tmp_array[$f_n];
													} else {
															$mail_to .= ",".$tmp_array[$f_n];
													}
													$f_i = $f_i+2;
											}
											$result=mail($mail_to, $subject, $emailBody, $headers);
											if ($result) {
													$obj->ll_echo("[".$obj->show_time()."]".$T_B_MAIL_TITLE."W2P轉檔清冊-".$MAP_ARRAY[NAME][$key]."寄送完成");
											} else {
													$obj->ll_echo("[".$obj->show_time()."]".$T_B_MAIL_TITLE."W2P轉檔清冊-".$MAP_ARRAY[NAME][$key]."寄送失敗");
											}
									}
									break;
					}
    			//$html.='</table>';
    			//$html.='</body>';
    			//$html.='</html>';
      	  
      }
  }
  $FAC_SEND_ARRAY=array();

  /**********************************************************************************************************************************
 	*  工作單寄送
 	**********************************************************************************************************************************/
  if (count($MAP_WORK_ARRAY) > 0) {
      $html ='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
  		$html.='<html xmlns="http://www.w3.org/1999/xhtml">';
  		$html.='<head>';
  		$html.='<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
  		$html.='<title>W2P工作單'.date("Y-m-d H:i",$insert_time).'</title>';
  		$html.='</head>';
  		$html.='<body>';
  		$html.='<center>訂單總筆數='.count($MAP_WORK_ARRAY[DATA]).'筆';

      $head = $obj->work_mail_head();

      foreach ($MAP_WORK_ARRAY[PAPER] as $p_key => $p_value) {
          foreach ($MAP_WORK_ARRAY["$p_key"] as $b_key => $b_value) {
              switch ($b_key) {
                  case "801":
                      $BNAME="平裝";
                      break;
                  case "802":
                      $BNAME="精裝";
                      break;
                  case "803":
                      $BNAME="騎馬釘";
                      break;
                  case "804":
                      $BNAME="蝴蝶頁(厚)";
                      break;
                  case "805":
                      $BNAME="蝴蝶頁(薄)";
                      break;
                  default:
                      $BNAME="";
                      break;
              }
              $html.='<br><center>'.$BNAME.'訂單筆數='.count($MAP_WORK_ARRAY["$p_key"]["$b_key"]).'筆</center>';
              $html.= $head;
              foreach ($MAP_WORK_ARRAY["$p_key"]["$b_key"] as $s_value) {
                    if (!in_array($s_value,$book_array)) {
                        $html .= trim($MAP_WORK_ARRAY[DATA][$s_value]);
                        $book_array[]=$s_value;
                    }
              }
         	    $html.=' </table>';
          }
      }
     	$html.='</table>';
			$html.='</body>';
			$html.='</html>';

      $s_html = $DATETIME."_work.html";
	    $fileopen = fopen($ROOT_FOLDER.'./transfer/mail/'.$DIR_NAME.'/'.$s_html,"w+");
	    fseek($fileopen,0);
	    fwrite($fileopen,$html);
	    fclose($fileopen);

      //$emailBody = $html;

    	$title ='W2P工作單'.date("Y-m-d H:i",$insert_time);
      $subject = "=?UTF-8?B?" . base64_encode($title) . "?=";

			$boundary = uniqid( "");

			$headers ="From: webmaster@cloudw2p.com"."\r\n";
			$headers.="Cc:".$INI_SET[master]."\r\n";
			$headers.="Reply-To: webmaster@cloudw2p.com"."\r\n";
			$headers.="X-Priority: 1"."\r\n";
			$headers.="X-MSMail-Priority: High"."\r\n";
			$headers.="Content-type: multipart/mixed; boundary=\"$boundary\"";

			$read = base64_encode($html);
			$read = chunk_split($read);

			$emailBody = '--'.$boundary."\n";
			$emailBody.= 'Content-type: text/html; charset="utf-8"'."\n";
			$emailBody.= 'Content-transfer-encoding: base64'."\n\n";

			$emailBody.= $read."\n"; // 本文內容

			$emailBody.= '--'.$boundary."\r\n";
			$emailBody.= 'Content-Type: application/octet-stream; name='.$DATETIME.'_work.html'."\r\n";
			$emailBody.= 'Content-disposition: inline; attachment'."\r\n";
			$emailBody.= 'Content-transfer-encoding: base64'."\r\n\r\n";
			$emailBody.= $read."\r\n";
			$emailBody.="--$boundary--";


			$result=mail(trim($INI_SET[MailCato]), $subject, $emailBody, $headers);

      if ($result) {
					$obj->ll_echo("[".$obj->show_time()."]W2P工作單".date("Y-m-d H:i",$insert_time)."寄送完成");
			} else {
					$obj->ll_echo("[".$obj->show_time()."]W2P工作單".date("Y-m-d H:i",$insert_time)."寄送失敗");
			}
  }
?>
