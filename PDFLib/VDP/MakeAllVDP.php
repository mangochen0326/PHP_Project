<?php
    /*=========================================================================================
    *  VDP產品直落拼板併版PDF功能
    *
    *  2013/1/24 Arvin VDP直落
    *
    *
    /*========================================================================================
		* Set timoeout & memory (設定執行timeout時間、使用記憶體)
		*========================================================================================*/
		set_time_limit(0);
		ini_set("memory_limit","300M");
		/*========================================================================================
		*  init
		=========================================================================================*/
    define('Document_root',dirname(dirname(__FILE__)));
    //===========================================================
    // 自動宣告物件
    //===========================================================
    function __autoload($classname) {
        $filename = Document_root."./transfer/class/". $classname .".php";
        if (is_file($filename)) {
            include_once($filename);
        }
    }
    include_once(Document_root."./inc/dblk.php");
    include_once(Document_root.'./album/myapi.php');
    include_once(Document_root.'./album/makepdf_api.php');
    $LOG_DIR  =date("Ymd",time());
   	/*========================================================================================
  	*  Environment Variable Set
		*========================================================================================*/
    $obj = VDP::getInstance($GB_dblk);            //宣告VDP物件
    //$obj->thislog=$thislog;                       //設定LOG
  	$pdfhw = new PDFlib();                        //宣告PDFLib物件
    pdflib_font_parameter($pdfhw);
    try {
        if ($BID=='') {
            //throw new Exception ("empty BID");
        } elseif ($ORD=='') {
            //throw new Exception ("empty ORD");
        }
        /***************************************************************************************
    		*  PDF information
    		****************************************************************************************/
        $info=$obj->get_info($BID);
        $obj->BOOKPATH_PDF=$GB_BOOKPATH_PDF;
        /***************************************************************************************
    		*  Html head
    		****************************************************************************************/
    		$html='';
    		$html.='<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "DTD/xhtml1-strict.dtd">';
    		$html.='<html>';
    		$html.='<head>';
    		$html.='<meta http-equiv="Content-type" content="text/html; charset=utf-8" />';
    		$html.='<meta name="Description" content="an online application for photo album editing." />';
    		$html.='<meta name="Keywords" content="photo,VOW,album,book,w2p,wtp,相本,相簿,線上編輯,印刷" />';
    		$html.='</head>';
    		$html.='<body>';
        /***************************************************************************************
    		*  Process
    		****************************************************************************************/

        //$ORD="VDDC1234";

        $str="Select BONUM from PORDER join WBOOK on PORDER.BID=WBOOK.WBID where YFPBOID='".substr($ORD,0,8)."'";
        $rs=$obj->query($str);
        $BONUM=$rs[BONUM][0];

        //$BONUM=1800;

        $source_array= array("VDP","VDPDATA");
        //$source_array= array("VDP");

        foreach ($source_array as $s_value) {
                //清除舊資料
            $SQL="Delete from VDP_TEMP where BID='$BID'";
            $obj->query($SQL,"DEL");
						
            $sourcepdf = $GB_BOOKPATH_PDF."spool/".$s_value."_".$BID.".pdf";

						if (file_exists($sourcepdf)) {
								$last_pdf = null; //設定初始值
								$last_value= null;
								$COMBIN = null;
								$PAGE   = null;

								$pdfdata = LL_GetPdfPages($sourcepdf);

								$obj->ins_db($BONUM,$s_value); //計算頁數並將資料寫入DB
								//====================================================================================
								//     計算總共幾個檔案
								//====================================================================================
								$SQL="Select PDF from VDP_TEMP where BID='$BID' group by PDF";
								$rs=$obj->query($SQL);
								$pdf_count=count($rs[PDF]);
								//====================================================================================
								//     計算各檔案的總頁數
								//====================================================================================
								$MAX_PAGE=$obj->count_page();
								//====================================================================================
								//     實際產出 PDF
								//====================================================================================
								$SQL="Select * from VDP_TEMP where BID='$BID' order by cast(PDF as int),cast(PDF_PAGE as int),cast (X as int ),cast (Y as int)";
								$rs=$obj->query($SQL);
								foreach ($rs[PDF_PAGE] as $key => $value) {
										if ($rs[PDF][$key]!=$last_pdf) {
												$COMBIN++;
												if ($last_pdf!='') {
														$PAGE++;
														pdflib_font( $pdfhw, '黑體', 12, $PAGE."-".$MAX_PAGE[($COMBIN-1)], 5*$obj->px,5*$obj->px ); //標註頁次 ex: 9-100共100頁的第9頁
														$obj->cut_line($pdfhw);
														LL_PdfClosePage($pdfhw, $doc, $page);
														$create_pdf=true;
														$PAGE=0;//PDF每頁序號
												}
												$new_pdf =$GB_BOOKPATH_PDF."spool/".$ORD."_".$s_value."_".$COMBIN."(".$pdf_count.").pdf"; //大版名稱
												pdf_head($pdfhw,$new_pdf);
												$page = LL_NewPage($pdfhw, $doc, $sourcepdf);
												$obj->ins_info_page ($pdfhw,$COMBIN); //插入資訊頁
										}
										if ($value!=$last_value) {//單頁模數拼完時換頁
												if ($last_value!='' and !$create_pdf) {
														//雙數頁、單面VDP、非完全自製 VDP印件，要在前一頁打上頁次標註
														if ($value%2==0 or $info['PAGES']=='2' or $s_value!='VDP') {
																$PAGE++;
																pdflib_font($pdfhw, '黑體', 12, $PAGE."-".$MAX_PAGE[$COMBIN], 5*$obj->px,5*$obj->px );
														}
														$obj->cut_line($pdfhw);
														$pdfhw->end_page_ext("");
												}
												$pdfhw->begin_page_ext($info['PDF_WIDTH']*$obj->px, $info['PDF_HEIGHT']*$obj->px, "topdown");		//依序貼上各模影像
										}
										$pastX=$info['X'][$rs['X'][$key]];  //各模 Y 軸
										$pastY=$info['Y'][$rs['Y'][$key]];  //各模 X 軸
										$filepage=$rs['FIT_PAGE'][$key];    //貼上來源PDF的頁次

										LL_FitPdiPage($pdfhw, $doc, $pastX*$obj->px, $pastY*$obj->px, $info['WIDTH']*$obj->px, $info['HEIGHT']*$obj->px, $filepage);

										$last_pdf=$rs[PDF][$key];
										$last_value=$value;
										$create_pdf=false; //是否開新PDF Flag
								}
								$PAGE++;
								//雙數頁、單面VDP、非完全自製 VDP印件，要在前一頁打上頁次標註
								if ($value%2!=0 or $info['PAGES']=='2' or $s_value!="VDP") {
										pdflib_font( $pdfhw, '黑體', 12, $PAGE."-".$MAX_PAGE[$COMBIN], 5*$obj->px,5*$obj->px );
								}
								$obj->cut_line($pdfhw); //劃裁切線
								LL_PdfClosePage($pdfhw, $doc, $page);
						}
        }
        //製作VDP固定資料背版
        if ($info['PAGES']=='4') {
            $obj->make_single_pdf($pdfhw,$ORD,"BACK");
        }
        //判斷是否有刮刮模檔案，若有做單頁PDF出來
        $block_file=$GB_BOOKPATH_PDF."spool/BLOCK_".$BID.".pdf";
        if (file_exists($block_file)) {
            $obj->make_single_pdf($pdfhw,$ORD,"BLOCK");
        }

    } catch (PDFlibException $e) {
        $errnum = $e->get_errnum();
        $apiname = $e->get_apiname();
        $errmsg = $e->get_errmsg();
        echo 'Caught PDFLib Exception : ', $e->get_apiname()," ",$e->get_errmsg(), "\n";
        die;
    } catch (Exception $e) {
        echo 'Caught exception: ',  $e->getMessage(), "\n";
        die($e);
    }
    $html.='<span>VDP落版完成!!</span>';
    /***************************************************************************************
    *  Html End
    *****************************************************************************************/
    $html.='</body>';
    $html.='</html>';
    print($html);

    die;

    function pdf_head($obj,$pdf) {
        $obj->begin_document($pdf, "optimize=true compatibility=1.6");
       	$obj->set_parameter("textformat", "utf8");
     		$obj->set_info("Creator", "YFP");
        $obj->set_info("Author",  "Arvin");
        $obj->set_info("Title",   "VDP");
    }

    function	LL_PdfClosePage($pobj, $doc, $page)		{
        $pobj->close_pdi_page( $page);
        $pobj->close_pdi_document( $doc);
        $pobj->end_page_ext("");
        $pobj->end_document("");
    }

    function	LL_NewPage($pobj, &$doc, $pdi)		{
        $doc = $pobj->open_pdi_document($pdi, "");
        $pagehw = $pobj->open_pdi_page($doc, 1, "");
        return	$pagehw;
    }

?>
