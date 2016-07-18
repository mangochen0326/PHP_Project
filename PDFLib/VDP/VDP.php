<?

class VDP {
   
    private $tmp  = array();
    private $DB   = null;
    private $parse = null;
    private $BTYPE = null;
    private $TYPE  = null;
    private $BID   = null;
    private $MAX_MIN_NUM = null;
    private $data = array();
    public  $px = 2.8346456;
    public  $BOOKPATH_PDF = null;

    private static $instance = false;

    function __construct($DB) {
        $this->DB=$DB;
    }

    static function getInstance($DB) {
        if (!self::$instance) {
          self::$instance = new VDP($DB);
        }
        return self::$instance;
    }
    // database query function
    function query ($query_string,$mode="Select") {
        $this->Parse  = mssql_query($query_string, $this->DB);
        switch ($mode) {
            case "Select":
                $rows= mssql_num_rows($this->Parse);
                if ($rows > 0) {
                    $num_filed = mssql_num_fields($this->Parse);
                    for($i=0;$i<$num_filed;$i++) {
                        $filed=mssql_fetch_field($this->Parse);
                        $key=$filed->name;
                        $this->tmp[ $i ] = strtoupper($key);
                    }
                    $result=$this->record();
                }
                break;
            default:
                if ($this->Parse) {
                    $result=true;
                } else {
                    $result=false;
                }
                break;
        }
        return $result;
    }
    // query result return array
    function record() {
        $num_filed = mssql_num_fields($this->Parse);
        $num_row = mssql_num_rows($this->Parse);
        for($i=0;$i<$num_row;$i++){
           $val = mssql_fetch_array($this->Parse);
           for($j=0;$j<$num_filed;$j++){
              $key=$this->tmp[ $j ];
              $result[ strtoupper($key) ] [ $i ]= trim($val[ $j ]);
           }
        }
        return $result;
    }
    //========================================================
    // 資料寫入DB
    //========================================================
    function ins_db ($_bonum,$_type='VDP') {
        $count=ceil($_bonum / ($this->data['X_MODE']*$this->data['Y_MODE'])); //計算頁數
        $FIT_PAGE=1; //VDP PDF來源起始頁
        $PDF=1;  //生成的生產PDF檔個數
        $PDF_PAGE=1;
        if ($count >= 100) {
            $loop=floor($count/100);
            $PDF=0;
            $start_no++;
            for ($j=0;$j<$loop;$j++) {
                $PDF++;
                for ($xi=1;$xi<=$this->data['X_MODE'];$xi++) {
                    for ($yi=1;$yi<=$this->data['Y_MODE'];$yi++) {
                        
                        switch ($_type) {
                            case "VDP":
                                if ($this->data['PAGES']==4) {
                                    $sub_loop=200;
                                    $MAX[$PDF][$xi][$yi]['MIN']=floor($start_no/2)+1;
                                } else {
                                    $sub_loop=100;
                                    $MAX[$PDF][$xi][$yi]['MIN']=$start_no;
                                }
                                for ($i=1;$i<=$sub_loop;$i++) {
                                    $SQL="Insert into VDP_TEMP (BID,PDF,PDF_PAGE,X,Y,FIT_PAGE) values ('$this->BID','$PDF','$i','$xi','$yi','$FIT_PAGE')";
                                    $this->query($SQL,"insert");
                                    $FIT_PAGE++;
                                    //if ($this->data['PAGES']==4 and $_type=='VDP') {
                                        //$SQL="Insert into VDP_TEMP (BID,PDF,PDF_PAGE,X,Y,FIT_PAGE) values ('$this->BID','$PDF','".($i+100)."','$xi','$yi','".(100+$FIT_PAGE)."')";
                                        //$this->query($SQL,"insert");
                                        //$FIT_PAGE++;
                                    //}
                                    $start_no++;
                                }
                                
                                if ($this->data['PAGES']==4) {
                                    $MAX[$PDF][$xi][$yi][MAX]=($start_no-1)/2;     
                                } else {
                                    $MAX[$PDF][$xi][$yi][MAX]=($start_no-1);
                                }
                                break;
                            default:
                                $MAX[$PDF][$xi][$yi]['MIN']=$start_no;
                                for ($i=1;$i<=100;$i++) {
                                    $SQL="Insert into VDP_TEMP (BID,PDF,PDF_PAGE,X,Y,FIT_PAGE) values ('$this->BID','$PDF','$i','$xi','$yi','$FIT_PAGE')";
                                    $this->query($SQL,"insert");
                                    $FIT_PAGE++;
                                    $start_no++;
                                }
                                $MAX[$PDF][$xi][$yi][MAX]=($start_no-1);
                                break;
                        }
                        /*
                        for ($i=1;$i<=100*$loop;$i++) {
                            $SQL="Insert into VDP_TEMP (BID,PDF,PDF_PAGE,X,Y,FIT_PAGE) values ('$this->BID','$PDF','$i','$xi','$yi','$FIT_PAGE')";
                            $this->query($SQL,"insert");
                            $FIT_PAGE++;
                            if ($this->data['PAGES']==4 and $_type=='VDP') {
                                //$SQL="Insert into VDP_TEMP (BID,PDF,PDF_PAGE,X,Y,FIT_PAGE) values ('$this->BID','$PDF','".($i+100)."','$xi','$yi','".(100+$FIT_PAGE)."')";
                                //$this->query($SQL,"insert");
                                //$FIT_PAGE++;
                            }
                            $start_no++;
                        }*/
                        
                    }
                }
            }
            $PDF++;
        }
        // < 100頁就依照最大頁數出直落PDF
        $count=$count-($loop*100);
        if ($loop < 1) {
            $start_no++;
        }
        for ($xi=1;$xi<=$this->data['X_MODE'];$xi++) {
            for ($yi=1;$yi<=$this->data['Y_MODE'];$yi++) {
                $PDF_PAGE=1;
                if ($start_no <=$_bonum) {
                    $MAX[$PDF][$xi][$yi][MIN]=$start_no;
                    for ($p_i=1;$p_i<=$count;$p_i++) {
                        if ($start_no <=$_bonum) {
                            $SQL="Insert into VDP_TEMP (BID,PDF,PDF_PAGE,X,Y,FIT_PAGE) values ('$this->BID','$PDF','$PDF_PAGE','$xi','$yi','$FIT_PAGE')";
                            $this->query($SQL,"insert");
                            $FIT_PAGE++;
                            $PDF_PAGE++;
                            //如果有背面，背面的X軸座標要映照，所以背面的 X 軸座標為X 總數-當前X +1
                            if ($this->data['PAGES']==4 and $_type=='VDP') {
                                $SQL ='Insert into VDP_TEMP (BID,PDF,PDF_PAGE,X,Y,FIT_PAGE) values (\''.$this->BID.'\',\''.$PDF.'\',\''.$PDF_PAGE.'\',';
                                $SQL.=' \''.($this->data['X_MODE']-$xi+1).'\',\''.$yi.'\',\''.$FIT_PAGE.'\')';
                                $this->query($SQL,"insert");
                                $FIT_PAGE++;
                                $PDF_PAGE++;
                            }
                            $start_no++;
                        }
                    }
                    $MAX[$PDF][$xi][$yi][MAX]=($start_no-1);
                }
            }
        }
        $this->TYPE=$_type;
        $this->MAX_MIN_NUM=$MAX;
    }
    //========================================================
    // 計算落版起點 X 座標
    // $x     = 3 X軸模數
    // $total = 2 X軸總模數
    // $gap   = 1  x 軸 gap 寬度
    //========================================================
    function get_x_point($make_width,$pdf_width,$x,$total,$gap) {
        $tmp_x=($pdf_width-($make_width*$total)-(($total-1)*$gap))/2;
        $pastX = $tmp_x+($x-1)*$make_width+($x-1)*$gap;
        return $pastX;
    }
    //========================================================
    // 計算落版起點 Y 座標
    // $Y     = 1 Y軸模數
    // $total = 3 Y軸總模數
    // $gap   = 1  Y 軸 gap 寬度
    //========================================================
    function get_y_point($make_height,$pdf_height,$y,$total,$gap) {
        $tmp_Y=($pdf_height-($make_height*$total)-(($total-1)*$gap))/2+$make_height;
        $pastY = $tmp_Y+$make_height*($y-1)+($y-1)*$gap;
        return $pastY;
    }
    //========================================================
    //   查詢每個PDF最大頁數
    //========================================================
    function count_page() {
        $SQL="Select PDF,max(CAST(PDF_PAGE as int)) as MAX_PAGE from VDP_TEMP where BID='".$this->BID."' group by PDF";
        $rs=$this->query($SQL);
        foreach ($rs[PDF] as $m_key => $max_page) {
            if ($this->data['PAGES']=='4' and $this->TYPE=='VDP') {
                $result[$max_page]=$rs[MAX_PAGE][$m_key]/2;
            } else {
                $result[$max_page]=$rs[MAX_PAGE][$m_key];
            }
        }
        return $result;
    }
    //========================================================
    //   製作資訊頁
    //========================================================
    function ins_info_page ($pobj,$_page) {
        $pobj->begin_page_ext($this->data['PDF_WIDTH']*$this->px, $this->data['PDF_HEIGHT']*$this->px, "topdown");		//開啟一個新PDF工作頁
        for ($xi=1;$xi<=$this->data['X_MODE'];$xi++) {
            for ($yi=1;$yi<=$this->data['Y_MODE'];$yi++) {
                pdflib_font( $pobj, '黑體', 16, $this->MAX_MIN_NUM[$_page][$xi][$yi][MIN], ($this->data['X'][$xi]+$this->data['WIDTH']/2)*$this->px, ($this->data['Y'][$yi]-$this->data['HEIGHT']/2)*$this->px);
                pdflib_font( $pobj, '黑體', 16, $this->MAX_MIN_NUM[$_page][$xi][$yi][MAX], ($this->data['X'][$xi]+$this->data['WIDTH']/2)*$this->px, ($this->data['Y'][$yi]-$this->data['HEIGHT']/2+15)*$this->px);
            }
        }
        $pobj->end_page_ext("");
        if ($this->data['PAGES']==4 and $this->TYPE=='VDP') {
            $pobj->begin_page_ext($this->data['PDF_WIDTH']*$this->px, $this->data['PDF_HEIGHT']*$this->px, "topdown");		//開啟一個新PDF工作頁
            $pobj->end_page_ext("");
        }
    }
    //========================================================
    //   製作單頁背板：VDP非變動頁面、Block頁面
    //========================================================
    function make_single_pdf($pobj,$ord,$mode) {
        switch ($mode) {
            case 'BACK':
                $sourcepdf = $this->BOOKPATH_PDF."spool/".$this->BID.".pdf";
                $fit_page=2;
                break;
            case 'BLOCK':
                $sourcepdf = $this->BOOKPATH_PDF."spool/BLOCK_".$this->BID.".pdf";
                $fit_page=1;
                break;
        }
        $new_pdf =$this->BOOKPATH_PDF."spool/".$ord."_".$mode.".pdf";
        pdf_head($pobj,$new_pdf);
        $page = LL_NewPage($pobj, $doc, $sourcepdf);
        $pobj->begin_page_ext($this->data['PDF_WIDTH']*$this->px, $this->data['PDF_HEIGHT']*$this->px, "topdown");		//開啟一個新PDF工作頁
        for ($xi=1;$xi<=$this->data['X_MODE'];$xi++) {
            for ($yi=1;$yi<=$this->data['Y_MODE'];$yi++) {
                $pastX=$this->data['X'][$xi];
                $pastY=$this->data['Y'][$yi];
                LL_FitPdiPage($pobj, $doc, $pastX*$this->px, $pastY*$this->px, $this->data['WIDTH']*$this->px, $this->data['HEIGHT']*$this->px, $fit_page);
            }
        }
        $this->cut_line($pobj);
        LL_PdfClosePage($pobj, $doc, $page);
    }
    //========================================================
    //   劃裁切線
    //   預設出血 1mm
    //========================================================
    function cut_line($pobj,$blood=1) {
        switch ($this->BTYPE) {
            //名片規格的要另外劃對位區塊
            case 262:
                $pobj->setcolor("fillstroke", "cmyk", 0, 0, 0, 1.0);
                $pobj->rect(201.5*$this->px, ($this->data['Y'][1]-$this->data['HEIGHT']+$blood-1.5)*$this->px ,40*$this->px, 3*$this->px);
                $pobj->fill_stroke();
                $sub_y=15;
                $sub_x=1;
                break;
            case 263:
                $pobj->setcolor("fillstroke", "cmyk", 0, 0, 0, 1.0);
                $pobj->rect(($this->data['X'][$this->data['X_MODE']]+$this->data['WIDTH']+$blood-1.5)*$this->px,(201.5+40)*$this->px,3*$this->px,40*$this->px);
                $pobj->fill_stroke();
                $sub_y=1;
                $sub_x=15;
                break;
            default:
                $sub_y=1;
                $sub_x=1;
                break;
        }
        $pobj->setlinewidth(0.25);
        foreach ($this->data['X'] as $x_key => $x_value) {
            $pobj->moveto(($x_value+$blood)*$this->px,($this->data['Y'][1]-$this->data['HEIGHT']-$sub_y)*$this->px);
            $pobj->lineto(($x_value+$blood)*$this->px,($this->data['Y'][1]-$this->data['HEIGHT']-($sub_y+5))*$this->px);

            $pobj->moveto(($x_value+$this->data['WIDTH']-$blood)*$this->px,($this->data['Y'][1]-$this->data['HEIGHT']-$sub_y)*$this->px);
            $pobj->lineto(($x_value+$this->data['WIDTH']-$blood)*$this->px,($this->data['Y'][1]-$this->data['HEIGHT']-($sub_y+5))*$this->px);

            $pobj->moveto(($x_value+$blood)*$this->px,($this->data['Y'][$this->data['Y_MODE']]+$sub_y)*$this->px);
            $pobj->lineto(($x_value+$blood)*$this->px,($this->data['Y'][$this->data['Y_MODE']]+($sub_y+5))*$this->px);

            $pobj->moveto(($x_value+$this->data['WIDTH']-$blood)*$this->px,($this->data['Y'][$this->data['Y_MODE']]+$sub_y)*$this->px);
            $pobj->lineto(($x_value+$this->data['WIDTH']-$blood)*$this->px,($this->data['Y'][$this->data['Y_MODE']]+($sub_y+5))*$this->px);
        }
        foreach ($this->data['Y'] as $y_key => $y_value) {
            $pobj->moveto(($this->data['X'][1]-$sub_x)*$this->px,($y_value-$this->data['HEIGHT']+$blood)*$this->px);
            $pobj->lineto(($this->data['X'][1]-($sub_x+5))*$this->px,($y_value-$this->data['HEIGHT']+$blood)*$this->px);

            $pobj->moveto(($this->data['X'][1]-$sub_x)*$this->px,($y_value-$blood)*$this->px);
            $pobj->lineto(($this->data['X'][1]-($sub_x+5))*$this->px,($y_value-$blood)*$this->px);

            $pobj->moveto(($this->data['X'][$this->data['X_MODE']]+$this->data['WIDTH']+$sub_x)*$this->px,($y_value-$this->data['HEIGHT']+$blood)*$this->px);
            $pobj->lineto(($this->data['X'][$this->data['X_MODE']]+$this->data['WIDTH']+($sub_x+5))*$this->px,($y_value-$this->data['HEIGHT']+$blood)*$this->px);

            $pobj->moveto(($this->data['X'][$this->data['X_MODE']]+$this->data['WIDTH']+$sub_x)*$this->px,($y_value-$blood)*$this->px);
            $pobj->lineto(($this->data['X'][$this->data['X_MODE']]+$this->data['WIDTH']+($sub_x+5))*$this->px,($y_value-$blood)*$this->px);

        }
        $pobj->stroke();

    }
    //抓出VDP尺寸及模數
    function get_info($_BID) {
        $BOOK = EB_getbooktype_simple($_BID);
        $this->BTYPE = $BOOK[BTYPE];//產品類別
        $BPAGES = $BOOK[PAGES];//頁數
        $result['PDF_WIDTH']  = 460;
    		$result['PDF_HEIGHT'] = 320;
        $result['PAGES']= $BPAGES;
        switch ($this->BTYPE) {
            case 250: //VDP A3橫
                $result['WIDTH']=422;
                $result['HEIGHT']=299;
                $result['X_MODE'] = 1;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 1;
                $result['Y_GAP']  = 1;
                break;
            case 251: //VDP A3直
                $result['WIDTH']=299;
                $result['HEIGHT']=422;
                $result['X_MODE'] = 1;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 1;
                $result['Y_GAP']  = 1;
                $result['PDF_WIDTH']  = 320;
    		        $result['PDF_HEIGHT'] = 460;
                break;
            case 252: //VDP A4橫
                $result['WIDTH']=299;
                $result['HEIGHT']=212;
                $result['X_MODE'] = 1;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 2;
                $result['Y_GAP']  = 1;
                $result['PDF_WIDTH']  = 320;
    		        $result['PDF_HEIGHT'] = 460;
                break;
            case 253: //VDP A4直
                $result['WIDTH']=212;
                $result['HEIGHT']=299;
                $result['X_MODE'] = 2;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 1;
                $result['Y_GAP']  = 1;
                break;
            case 254: //VDP B4橫
                $result['WIDTH']=355;
                $result['HEIGHT']=252;
                $result['X_MODE'] = 1;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 1;
                $result['Y_GAP']  = 1;
                break;
            case 255: //VDP B4直
                $result['WIDTH']=252;
                $result['HEIGHT']=355;
                $result['X_MODE'] = 1;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 1;
                $result['Y_GAP']  = 1;
                $result['PDF_WIDTH']  = 320;
    		        $result['PDF_HEIGHT'] = 460;
                break;
            case 256: //VDP 門票
                $result['WIDTH']=212;
                $result['HEIGHT']=72;
                $result['X_MODE'] = 1;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 6;
                $result['Y_GAP']  = 1;
								 $result['PDF_WIDTH']  = 320;
    		        $result['PDF_HEIGHT'] = 460;
                break;
            case 258: //VDP 兌換券 橫
                $result['WIDTH']=142;
                $result['HEIGHT']=72;
                $result['X_MODE'] = 2;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 6;
                $result['Y_GAP']  = 1;
                $result['PDF_WIDTH']  = 320;
    		        $result['PDF_HEIGHT'] = 460;
                break;
            case 259: //VDP 兌換券 直
                $result['WIDTH']=72;
                $result['HEIGHT']=142;
                $result['X_MODE'] = 6;
                $result['X_GAP']  = 1;
                $result['Y_MODE'] = 2;
                $result['Y_GAP']  = 1;
                break;
            case 260: //刮刮卡 橫
                $result['WIDTH']=97;
                $result['HEIGHT']=72;
                $result['X_MODE'] = 3;
                $result['X_GAP']  = 2;
                $result['Y_MODE'] = 6;
                $result['Y_GAP']  = 1;
                $result['PDF_WIDTH']  = 320;
    		        $result['PDF_HEIGHT'] = 460;
                break;
            case 261: //刮刮卡 直
                $result['WIDTH']=72;
                $result['HEIGHT']=97;
                $result['X_MODE'] = 6;
                $result['X_GAP'] = 1;
                $result['Y_MODE'] = 3;
                $result['Y_GAP'] = 2;
                break;
            case 262: //折價卡、識別證 橫
                $result['WIDTH']=92;
                $result['HEIGHT']=56;
                $result['X_MODE'] = 3;
                $result['X_GAP'] = 8;
                $result['Y_MODE'] = 7;
                $result['Y_GAP'] = 2;
                $result['PDF_WIDTH']  = 320;
    		        $result['PDF_HEIGHT'] = 460;
                break;
            case 263: //折價卡、識別證 直
                $result['WIDTH']=56;
                $result['HEIGHT']=92;
                $result['X_MODE'] = 7;
                $result['X_GAP'] = 2;
                $result['Y_MODE'] = 3;
                $result['Y_GAP'] = 8;
                break;
        }
        for ($i=1;$i<=$result['X_MODE'];$i++) {
            $result[X][$i]= $this->get_x_point($result['WIDTH'],$result['PDF_WIDTH'],$i,$result['X_MODE'],$result['X_GAP']);
        }
        for ($j=1;$j<=$result['Y_MODE'];$j++) {
            $result[Y][$j]= $this->get_y_point($result['HEIGHT'],$result['PDF_HEIGHT'],$j,$result['Y_MODE'],$result['Y_GAP']);
        }
        $this->data=$result;
        $this->BID =$_BID;
        return $this->data;
    }
    
}


?>