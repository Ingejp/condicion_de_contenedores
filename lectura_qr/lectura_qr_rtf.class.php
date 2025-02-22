<?php

class lectura_qr_rtf
{
   var $Db;
   var $Erro;
   var $Ini;
   var $Lookup;
   var $nm_data;
   var $Texto_tag;
   var $Arquivo;
   var $Tit_doc;
   var $sc_proc_grid; 
   var $NM_cmp_hidden = array();

   //---- 
   function __construct()
   {
      $this->nm_data   = new nm_data("en_us");
      $this->Texto_tag = "";
   }

   //---- 
   function monta_rtf()
   {
      $this->inicializa_vars();
      $this->gera_texto_tag();
      $this->grava_arquivo_rtf();
      if ($this->Ini->sc_export_ajax)
      {
          $this->Arr_result['file_export']  = NM_charset_to_utf8($this->Rtf_f);
          $this->Arr_result['title_export'] = NM_charset_to_utf8($this->Tit_doc);
          $Temp = ob_get_clean();
          if ($Temp !== false && trim($Temp) != "")
          {
              $this->Arr_result['htmOutput'] = NM_charset_to_utf8($Temp);
          }
          $oJson = new Services_JSON();
          echo $oJson->encode($this->Arr_result);
          exit;
      }
      else
      {
          $this->progress_bar_end();
      }
   }

   //----- 
   function inicializa_vars()
   {
      global $nm_lang;
      $dir_raiz          = strrpos($_SERVER['PHP_SELF'],"/") ;  
      $dir_raiz          = substr($_SERVER['PHP_SELF'], 0, $dir_raiz + 1) ;  
      $this->nm_location = $this->Ini->sc_protocolo . $this->Ini->server . $dir_raiz; 
      require_once($this->Ini->path_aplicacao . "lectura_qr_total.class.php"); 
      $this->Tot      = new lectura_qr_total($this->Ini->sc_page);
      $this->prep_modulos("Tot");
      $Gb_geral = "quebra_geral_" . $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['SC_Ind_Groupby'];
      if (method_exists($this->Tot,$Gb_geral))
      {
          $this->Tot->$Gb_geral();
          $this->count_ger = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['tot_geral'][1];
      }
      if (!$this->Ini->sc_export_ajax) {
          require_once($this->Ini->path_lib_php . "/sc_progress_bar.php");
          $this->pb = new scProgressBar();
          $this->pb->setRoot($this->Ini->root);
          $this->pb->setDir($_SESSION['scriptcase']['lectura_qr']['glo_nm_path_imag_temp'] . "/");
          $this->pb->setProgressbarMd5($_GET['pbmd5']);
          $this->pb->initialize();
          $this->pb->setReturnUrl("./");
          $this->pb->setReturnOption('volta_grid');
          $this->pb->setTotalSteps($this->count_ger);
      }
      $this->Arquivo    = "sc_rtf";
      $this->Arquivo   .= "_" . date("YmdHis") . "_" . rand(0, 1000);
      $this->Arquivo   .= "_lectura_qr";
      $this->Arquivo   .= ".rtf";
      $this->Tit_doc    = "lectura_qr.rtf";
   }
   //---- 
   function prep_modulos($modulo)
   {
      $this->$modulo->Ini    = $this->Ini;
      $this->$modulo->Db     = $this->Db;
      $this->$modulo->Erro   = $this->Erro;
      $this->$modulo->Lookup = $this->Lookup;
   }


   //----- 
   function gera_texto_tag()
   {
     global $nm_lang;
      global $nm_nada, $nm_lang;

      $_SESSION['scriptcase']['sc_sql_ult_conexao'] = ''; 
      $this->sc_proc_grid = false; 
      $nm_raiz_img  = ""; 
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_name']))
      {
          $this->Arquivo = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_name'];
          $this->Tit_doc = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_name'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_name']);
      }
      if (isset($_SESSION['scriptcase']['sc_apl_conf']['lectura_qr']['field_display']) && !empty($_SESSION['scriptcase']['sc_apl_conf']['lectura_qr']['field_display']))
      {
          foreach ($_SESSION['scriptcase']['sc_apl_conf']['lectura_qr']['field_display'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['usr_cmp_sel']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['usr_cmp_sel']))
      {
          foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['usr_cmp_sel'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['php_cmp_sel']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['php_cmp_sel']))
      {
          foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['php_cmp_sel'] as $NM_cada_field => $NM_cada_opc)
          {
              $this->NM_cmp_hidden[$NM_cada_field] = $NM_cada_opc;
          }
      }
      $this->sc_where_orig   = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['where_orig'];
      $this->sc_where_atual  = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['where_pesq'];
      $this->sc_where_filtro = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['where_pesq_filtro'];
      if (isset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['campos_busca']) && !empty($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['campos_busca']))
      { 
          $Busca_temp = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['campos_busca'];
          if ($_SESSION['scriptcase']['charset'] != "UTF-8")
          {
              $Busca_temp = NM_conv_charset($Busca_temp, $_SESSION['scriptcase']['charset'], "UTF-8");
          }
          $this->idcita = $Busca_temp['idcita']; 
          $tmp_pos = strpos($this->idcita, "##@@");
          if ($tmp_pos !== false && !is_array($this->idcita))
          {
              $this->idcita = substr($this->idcita, 0, $tmp_pos);
          }
          $this->cita_codigo_cita = $Busca_temp['cita_codigo_cita']; 
          $tmp_pos = strpos($this->cita_codigo_cita, "##@@");
          if ($tmp_pos !== false && !is_array($this->cita_codigo_cita))
          {
              $this->cita_codigo_cita = substr($this->cita_codigo_cita, 0, $tmp_pos);
          }
          $this->cita_descripcion = $Busca_temp['cita_descripcion']; 
          $tmp_pos = strpos($this->cita_descripcion, "##@@");
          if ($tmp_pos !== false && !is_array($this->cita_descripcion))
          {
              $this->cita_descripcion = substr($this->cita_descripcion, 0, $tmp_pos);
          }
          $this->cita_fechainicio = $Busca_temp['cita_fechainicio']; 
          $tmp_pos = strpos($this->cita_fechainicio, "##@@");
          if ($tmp_pos !== false && !is_array($this->cita_fechainicio))
          {
              $this->cita_fechainicio = substr($this->cita_fechainicio, 0, $tmp_pos);
          }
          $this->cita_fechainicio_2 = $Busca_temp['cita_fechainicio_input_2']; 
      } 
      $this->arr_export = array('label' => array(), 'lines' => array());
      $this->arr_span   = array();

      $this->Texto_tag .= "<table>\r\n";
      $this->Texto_tag .= "<tr>\r\n";
      foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['field_order'] as $Cada_col)
      { 
          $SC_Label = (isset($this->New_label['idcita'])) ? $this->New_label['idcita'] : "Id Cita"; 
          if ($Cada_col == "idcita" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_codigo_cita'])) ? $this->New_label['cita_codigo_cita'] : "Codigo Cita"; 
          if ($Cada_col == "cita_codigo_cita" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_descripcion'])) ? $this->New_label['cita_descripcion'] : "Descripcion"; 
          if ($Cada_col == "cita_descripcion" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_fechainicio'])) ? $this->New_label['cita_fechainicio'] : "Fecha Inicio"; 
          if ($Cada_col == "cita_fechainicio" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_horainicio'])) ? $this->New_label['cita_horainicio'] : "Hora Inicio"; 
          if ($Cada_col == "cita_horainicio" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_idcontenedor'])) ? $this->New_label['cita_idcontenedor'] : "Contenedor"; 
          if ($Cada_col == "cita_idcontenedor" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_idcabezal'])) ? $this->New_label['cita_idcabezal'] : "Cabezal"; 
          if ($Cada_col == "cita_idcabezal" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_idestado'])) ? $this->New_label['cita_idestado'] : "Estado"; 
          if ($Cada_col == "cita_idestado" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
          $SC_Label = (isset($this->New_label['cita_fechafin'])) ? $this->New_label['cita_fechafin'] : "Fecha Fin"; 
          if ($Cada_col == "cita_fechafin" && (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off"))
          {
              if (!NM_is_utf8($SC_Label))
              {
                  $SC_Label = sc_convert_encoding($SC_Label, "UTF-8", $_SESSION['scriptcase']['charset']);
              }
              $SC_Label = str_replace('<', '&lt;', $SC_Label);
              $SC_Label = str_replace('>', '&gt;', $SC_Label);
              $this->Texto_tag .= "<td>" . $SC_Label . "</td>\r\n";
          }
      } 
      $this->Texto_tag .= "</tr>\r\n";
      $this->nm_field_dinamico = array();
      $this->nm_order_dinamico = array();
      $nmgp_select_count = "SELECT count(*) AS countTest from " . $this->Ini->nm_tabela; 
      if (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_sybase))
      { 
          $nmgp_select = "SELECT CONCAT(\"CUC_\",cita.idCita) as idcita, cita.codigo_cita as cita_codigo_cita, cita.descripcion as cita_descripcion, str_replace (convert(char(10),cita.fechaInicio,102), '.', '-') + ' ' + convert(char(8),cita.fechaInicio,20) as cita_fechainicio, str_replace (convert(char(10),cita.horaInicio,102), '.', '-') + ' ' + convert(char(8),cita.horaInicio,20) as cita_horainicio, cita.idContenedor as cita_idcontenedor, cita.idcabezal as cita_idcabezal, cita.idestado as cita_idestado, str_replace (convert(char(10),cita.fechaFin,102), '.', '-') + ' ' + convert(char(8),cita.fechaFin,20) as cita_fechafin from " . $this->Ini->nm_tabela; 
      } 
      elseif (in_array(strtolower($this->Ini->nm_tpbanco), $this->Ini->nm_bases_mysql))
      { 
          $nmgp_select = "SELECT CONCAT(\"CUC_\",cita.idCita) as idcita, cita.codigo_cita as cita_codigo_cita, cita.descripcion as cita_descripcion, cita.fechaInicio as cita_fechainicio, cita.horaInicio as cita_horainicio, cita.idContenedor as cita_idcontenedor, cita.idcabezal as cita_idcabezal, cita.idestado as cita_idestado, cita.fechaFin as cita_fechafin from " . $this->Ini->nm_tabela; 
      } 
      else 
      { 
          $nmgp_select = "SELECT CONCAT(\"CUC_\",cita.idCita) as idcita, cita.codigo_cita as cita_codigo_cita, cita.descripcion as cita_descripcion, cita.fechaInicio as cita_fechainicio, cita.horaInicio as cita_horainicio, cita.idContenedor as cita_idcontenedor, cita.idcabezal as cita_idcabezal, cita.idestado as cita_idestado, cita.fechaFin as cita_fechafin from " . $this->Ini->nm_tabela; 
      } 
      $nmgp_select .= " " . $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['where_pesq'];
      $nmgp_select_count .= " " . $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['where_pesq'];
      $nmgp_order_by = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['order_grid'];
      $nmgp_select .= $nmgp_order_by; 
      $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nmgp_select_count;
      $rt = $this->Db->Execute($nmgp_select_count);
      if ($rt === false && !$rt->EOF && $GLOBALS["NM_ERRO_IBASE"] != 1)
      {
         $this->Erro->mensagem(__FILE__, __LINE__, "banco", $this->Ini->Nm_lang['lang_errm_dber'], $this->Db->ErrorMsg());
         exit;
      }
      $this->count_ger = $rt->fields[0];
      $rt->Close();
      $_SESSION['scriptcase']['sc_sql_ult_comando'] = $nmgp_select;
      $rs = $this->Db->Execute($nmgp_select);
      if ($rs === false && !$rs->EOF && $GLOBALS["NM_ERRO_IBASE"] != 1)
      {
         $this->Erro->mensagem(__FILE__, __LINE__, "banco", $this->Ini->Nm_lang['lang_errm_dber'], $this->Db->ErrorMsg());
         exit;
      }
      $this->SC_seq_register = 0;
      $PB_tot = (isset($this->count_ger) && $this->count_ger > 0) ? "/" . $this->count_ger : "";
      while (!$rs->EOF)
      {
         $this->SC_seq_register++;
         if (!$this->Ini->sc_export_ajax) {
             $Mens_bar = $this->Ini->Nm_lang['lang_othr_prcs'];
             if ($_SESSION['scriptcase']['charset'] != "UTF-8") {
                 $Mens_bar = sc_convert_encoding($Mens_bar, "UTF-8", $_SESSION['scriptcase']['charset']);
             }
             $this->pb->setProgressbarMessage($Mens_bar . ": " . $this->SC_seq_register . $PB_tot);
             $this->pb->addSteps(1);
         }
         $this->Texto_tag .= "<tr>\r\n";
         $this->idcita = $rs->fields[0] ;  
         $this->cita_codigo_cita = $rs->fields[1] ;  
         $this->cita_descripcion = $rs->fields[2] ;  
         $this->cita_fechainicio = $rs->fields[3] ;  
         $this->cita_horainicio = $rs->fields[4] ;  
         $this->cita_idcontenedor = $rs->fields[5] ;  
         $this->cita_idcontenedor = (string)$this->cita_idcontenedor;
         $this->cita_idcabezal = $rs->fields[6] ;  
         $this->cita_idcabezal = (string)$this->cita_idcabezal;
         $this->cita_idestado = $rs->fields[7] ;  
         $this->cita_idestado = (string)$this->cita_idestado;
         $this->cita_fechafin = $rs->fields[8] ;  
         //----- lookup - cita_idcontenedor
         $this->look_cita_idcontenedor = $this->cita_idcontenedor; 
         $this->Lookup->lookup_cita_idcontenedor($this->look_cita_idcontenedor, $this->cita_idcontenedor) ; 
         $this->look_cita_idcontenedor = ($this->look_cita_idcontenedor == "&nbsp;") ? "" : $this->look_cita_idcontenedor; 
         //----- lookup - cita_idcabezal
         $this->look_cita_idcabezal = $this->cita_idcabezal; 
         $this->Lookup->lookup_cita_idcabezal($this->look_cita_idcabezal, $this->cita_idcabezal) ; 
         $this->look_cita_idcabezal = ($this->look_cita_idcabezal == "&nbsp;") ? "" : $this->look_cita_idcabezal; 
         //----- lookup - cita_idestado
         $this->look_cita_idestado = $this->cita_idestado; 
         $this->Lookup->lookup_cita_idestado($this->look_cita_idestado, $this->cita_idestado) ; 
         $this->look_cita_idestado = ($this->look_cita_idestado == "&nbsp;") ? "" : $this->look_cita_idestado; 
         $this->sc_proc_grid = true; 
         foreach ($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['field_order'] as $Cada_col)
         { 
            if (!isset($this->NM_cmp_hidden[$Cada_col]) || $this->NM_cmp_hidden[$Cada_col] != "off")
            { 
                $NM_func_exp = "NM_export_" . $Cada_col;
                $this->$NM_func_exp();
            } 
         } 
         $this->Texto_tag .= "</tr>\r\n";
         $rs->MoveNext();
      }
      $this->Texto_tag .= "</table>\r\n";
      if(isset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['export_sel_columns']['field_order']))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['field_order'] = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['export_sel_columns']['field_order'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['export_sel_columns']['field_order']);
      }
      if(isset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['export_sel_columns']['usr_cmp_sel']))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['usr_cmp_sel'] = $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['export_sel_columns']['usr_cmp_sel'];
          unset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['export_sel_columns']['usr_cmp_sel']);
      }
      $rs->Close();
   }
   //----- idcita
   function NM_export_idcita()
   {
         $this->idcita = html_entity_decode($this->idcita, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->idcita = strip_tags($this->idcita);
         if (!NM_is_utf8($this->idcita))
         {
             $this->idcita = sc_convert_encoding($this->idcita, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->idcita = str_replace('<', '&lt;', $this->idcita);
         $this->idcita = str_replace('>', '&gt;', $this->idcita);
         $this->Texto_tag .= "<td>" . $this->idcita . "</td>\r\n";
   }
   //----- cita_codigo_cita
   function NM_export_cita_codigo_cita()
   {
         $this->cita_codigo_cita = html_entity_decode($this->cita_codigo_cita, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->cita_codigo_cita = strip_tags($this->cita_codigo_cita);
         if (!NM_is_utf8($this->cita_codigo_cita))
         {
             $this->cita_codigo_cita = sc_convert_encoding($this->cita_codigo_cita, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->cita_codigo_cita = str_replace('<', '&lt;', $this->cita_codigo_cita);
         $this->cita_codigo_cita = str_replace('>', '&gt;', $this->cita_codigo_cita);
         $this->Texto_tag .= "<td>" . $this->cita_codigo_cita . "</td>\r\n";
   }
   //----- cita_descripcion
   function NM_export_cita_descripcion()
   {
         $this->cita_descripcion = html_entity_decode($this->cita_descripcion, ENT_COMPAT, $_SESSION['scriptcase']['charset']);
         $this->cita_descripcion = strip_tags($this->cita_descripcion);
         if (!NM_is_utf8($this->cita_descripcion))
         {
             $this->cita_descripcion = sc_convert_encoding($this->cita_descripcion, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->cita_descripcion = str_replace('<', '&lt;', $this->cita_descripcion);
         $this->cita_descripcion = str_replace('>', '&gt;', $this->cita_descripcion);
         $this->Texto_tag .= "<td>" . $this->cita_descripcion . "</td>\r\n";
   }
   //----- cita_fechainicio
   function NM_export_cita_fechainicio()
   {
         $conteudo_x =  $this->cita_fechainicio;
         nm_conv_limpa_dado($conteudo_x, "YYYY-MM-DD");
         if (is_numeric($conteudo_x) && strlen($conteudo_x) > 0) 
         { 
             $this->nm_data->SetaData($this->cita_fechainicio, "YYYY-MM-DD  ");
             $this->cita_fechainicio = $this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", "ddmmaaaa"));
         } 
         if (!NM_is_utf8($this->cita_fechainicio))
         {
             $this->cita_fechainicio = sc_convert_encoding($this->cita_fechainicio, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->cita_fechainicio = str_replace('<', '&lt;', $this->cita_fechainicio);
         $this->cita_fechainicio = str_replace('>', '&gt;', $this->cita_fechainicio);
         $this->Texto_tag .= "<td>" . $this->cita_fechainicio . "</td>\r\n";
   }
   //----- cita_horainicio
   function NM_export_cita_horainicio()
   {
         $conteudo_x =  $this->cita_horainicio;
         nm_conv_limpa_dado($conteudo_x, "HH:II:SS");
         if (is_numeric($conteudo_x) && strlen($conteudo_x) > 0) 
         { 
             $this->nm_data->SetaData($this->cita_horainicio, "HH:II:SS  ");
             $this->cita_horainicio = $this->nm_data->FormataSaida($this->nm_data->FormatRegion("HH", "hhiiss"));
         } 
         if (!NM_is_utf8($this->cita_horainicio))
         {
             $this->cita_horainicio = sc_convert_encoding($this->cita_horainicio, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->cita_horainicio = str_replace('<', '&lt;', $this->cita_horainicio);
         $this->cita_horainicio = str_replace('>', '&gt;', $this->cita_horainicio);
         $this->Texto_tag .= "<td>" . $this->cita_horainicio . "</td>\r\n";
   }
   //----- cita_idcontenedor
   function NM_export_cita_idcontenedor()
   {
         nmgp_Form_Num_Val($this->look_cita_idcontenedor, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         if (!NM_is_utf8($this->look_cita_idcontenedor))
         {
             $this->look_cita_idcontenedor = sc_convert_encoding($this->look_cita_idcontenedor, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->look_cita_idcontenedor = str_replace('<', '&lt;', $this->look_cita_idcontenedor);
         $this->look_cita_idcontenedor = str_replace('>', '&gt;', $this->look_cita_idcontenedor);
         $this->Texto_tag .= "<td>" . $this->look_cita_idcontenedor . "</td>\r\n";
   }
   //----- cita_idcabezal
   function NM_export_cita_idcabezal()
   {
         nmgp_Form_Num_Val($this->look_cita_idcabezal, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         if (!NM_is_utf8($this->look_cita_idcabezal))
         {
             $this->look_cita_idcabezal = sc_convert_encoding($this->look_cita_idcabezal, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->look_cita_idcabezal = str_replace('<', '&lt;', $this->look_cita_idcabezal);
         $this->look_cita_idcabezal = str_replace('>', '&gt;', $this->look_cita_idcabezal);
         $this->Texto_tag .= "<td>" . $this->look_cita_idcabezal . "</td>\r\n";
   }
   //----- cita_idestado
   function NM_export_cita_idestado()
   {
         nmgp_Form_Num_Val($this->look_cita_idestado, $_SESSION['scriptcase']['reg_conf']['grup_num'], $_SESSION['scriptcase']['reg_conf']['dec_num'], "0", "S", "2", "", "N:" . $_SESSION['scriptcase']['reg_conf']['neg_num'] , $_SESSION['scriptcase']['reg_conf']['simb_neg'], $_SESSION['scriptcase']['reg_conf']['num_group_digit']) ; 
         if (!NM_is_utf8($this->look_cita_idestado))
         {
             $this->look_cita_idestado = sc_convert_encoding($this->look_cita_idestado, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->look_cita_idestado = str_replace('<', '&lt;', $this->look_cita_idestado);
         $this->look_cita_idestado = str_replace('>', '&gt;', $this->look_cita_idestado);
         $this->Texto_tag .= "<td>" . $this->look_cita_idestado . "</td>\r\n";
   }
   //----- cita_fechafin
   function NM_export_cita_fechafin()
   {
         $conteudo_x =  $this->cita_fechafin;
         nm_conv_limpa_dado($conteudo_x, "YYYY-MM-DD");
         if (is_numeric($conteudo_x) && strlen($conteudo_x) > 0) 
         { 
             $this->nm_data->SetaData($this->cita_fechafin, "YYYY-MM-DD  ");
             $this->cita_fechafin = $this->nm_data->FormataSaida($this->nm_data->FormatRegion("DT", "ddmmaaaa"));
         } 
         if (!NM_is_utf8($this->cita_fechafin))
         {
             $this->cita_fechafin = sc_convert_encoding($this->cita_fechafin, "UTF-8", $_SESSION['scriptcase']['charset']);
         }
         $this->cita_fechafin = str_replace('<', '&lt;', $this->cita_fechafin);
         $this->cita_fechafin = str_replace('>', '&gt;', $this->cita_fechafin);
         $this->Texto_tag .= "<td>" . $this->cita_fechafin . "</td>\r\n";
   }

   //----- 
   function grava_arquivo_rtf()
   {
      global $nm_lang, $doc_wrap;
      $this->Rtf_f = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $rtf_f       = fopen($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo, "w");
      require_once($this->Ini->path_third      . "/rtf_new/document_generator/cl_xml2driver.php"); 
      $text_ok  =  "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n"; 
      $text_ok .=  "<DOC config_file=\"" . $this->Ini->path_third . "/rtf_new/doc_config.inc\" >\r\n"; 
      $text_ok .=  $this->Texto_tag; 
      $text_ok .=  "</DOC>\r\n"; 
      $xml = new nDOCGEN($text_ok,"RTF"); 
      fwrite($rtf_f, $xml->get_result_file());
      fclose($rtf_f);
   }

   function nm_conv_data_db($dt_in, $form_in, $form_out)
   {
       $dt_out = $dt_in;
       if (strtoupper($form_in) == "DB_FORMAT")
       {
           if ($dt_out == "null" || $dt_out == "")
           {
               $dt_out = "";
               return $dt_out;
           }
           $form_in = "AAAA-MM-DD";
       }
       if (strtoupper($form_out) == "DB_FORMAT")
       {
           if (empty($dt_out))
           {
               $dt_out = "null";
               return $dt_out;
           }
           $form_out = "AAAA-MM-DD";
       }
       nm_conv_form_data($dt_out, $form_in, $form_out);
       return $dt_out;
   }
   function progress_bar_end()
   {
      unset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_file']);
      if (is_file($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_file'] = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      }
      $path_doc_md5 = md5($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr'][$path_doc_md5][0] = $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr'][$path_doc_md5][1] = $this->Tit_doc;
      $Mens_bar = $this->Ini->Nm_lang['lang_othr_file_msge'];
      if ($_SESSION['scriptcase']['charset'] != "UTF-8") {
          $Mens_bar = sc_convert_encoding($Mens_bar, "UTF-8", $_SESSION['scriptcase']['charset']);
      }
      $this->pb->setProgressbarMessage($Mens_bar);
      $this->pb->setDownloadLink($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $this->pb->setDownloadMd5($path_doc_md5);
      $this->pb->completed();
   }
   //---- 
   function monta_html()
   {
      global $nm_url_saida, $nm_lang;
      include($this->Ini->path_btn . $this->Ini->Str_btn_grid);
      unset($_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_file']);
      if (is_file($this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo))
      {
          $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr']['rtf_file'] = $this->Ini->root . $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      }
      $path_doc_md5 = md5($this->Ini->path_imag_temp . "/" . $this->Arquivo);
      $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr'][$path_doc_md5][0] = $this->Ini->path_imag_temp . "/" . $this->Arquivo;
      $_SESSION['sc_session'][$this->Ini->sc_page]['lectura_qr'][$path_doc_md5][1] = $this->Tit_doc;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
            "http://www.w3.org/TR/1999/REC-html401-19991224/loose.dtd">
<HTML<?php echo $_SESSION['scriptcase']['reg_conf']['html_dir'] ?>>
<HEAD>
 <TITLE><?php echo $this->Ini->Nm_lang['lang_othr_grid_titl'] ?> -  :: RTF</TITLE>
 <META http-equiv="Content-Type" content="text/html; charset=<?php echo $_SESSION['scriptcase']['charset_html'] ?>" />
<?php
if ($_SESSION['scriptcase']['proc_mobile'])
{
?>
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
<?php
}
?>
  <META http-equiv="Expires" content="Fri, Jan 01 1900 00:00:00 GMT"/>
  <META http-equiv="Last-Modified" content="<?php echo gmdate("D, d M Y H:i:s"); ?> GMT"/>
  <META http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate"/>
  <META http-equiv="Cache-Control" content="post-check=0, pre-check=0"/>
  <META http-equiv="Pragma" content="no-cache"/>
 <link rel="shortcut icon" href="../_lib/img/scriptcase__NM__ico__NM__favicon.ico">
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export.css" /> 
  <link rel="stylesheet" type="text/css" href="../_lib/css/<?php echo $this->Ini->str_schema_all ?>_export<?php echo $_SESSION['scriptcase']['reg_conf']['css_dir'] ?>.css" /> 
 <?php
 if(isset($this->Ini->str_google_fonts) && !empty($this->Ini->str_google_fonts))
 {
 ?>
    <link rel="stylesheet" type="text/css" href="<?php echo $this->Ini->str_google_fonts ?>" />
 <?php
 }
 ?>
  <link rel="stylesheet" type="text/css" href="../_lib/buttons/<?php echo $this->Ini->Str_btn_css ?>" /> 
</HEAD>
<BODY class="scExportPage">
<?php echo $this->Ini->Ajax_result_set ?>
<table style="border-collapse: collapse; border-width: 0; height: 100%; width: 100%"><tr><td style="padding: 0; text-align: center; vertical-align: middle">
 <table class="scExportTable" align="center">
  <tr>
   <td class="scExportTitle" style="height: 25px">RTF</td>
  </tr>
  <tr>
   <td class="scExportLine" style="width: 100%">
    <table style="border-collapse: collapse; border-width: 0; width: 100%"><tr><td class="scExportLineFont" style="padding: 3px 0 0 0" id="idMessage">
    <?php echo $this->Ini->Nm_lang['lang_othr_file_msge'] ?>
    </td><td class="scExportLineFont" style="text-align:right; padding: 3px 0 0 0">
     <?php echo nmButtonOutput($this->arr_buttons, "bexportview", "document.Fview.submit()", "document.Fview.submit()", "idBtnView", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bdownload", "document.Fdown.submit()", "document.Fdown.submit()", "idBtnDown", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "", "");
 ?>
     <?php echo nmButtonOutput($this->arr_buttons, "bvoltar", "document.F0.submit()", "document.F0.submit()", "idBtnBack", "", "", "", "", "", "", $this->Ini->path_botoes, "", "", "", "", "", "only_text", "text_right", "", "", "", "", "", "", "");
 ?>
    </td></tr></table>
   </td>
  </tr>
 </table>
</td></tr></table>
<form name="Fview" method="get" action="<?php echo $this->Ini->path_imag_temp . "/" . $this->Arquivo ?>" target="_blank" style="display: none"> 
</form>
<form name="Fdown" method="get" action="lectura_qr_download.php" target="_blank" style="display: none"> 
<input type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<input type="hidden" name="nm_tit_doc" value="lectura_qr"> 
<input type="hidden" name="nm_name_doc" value="<?php echo $path_doc_md5 ?>"> 
</form>
<FORM name="F0" method=post action="./"> 
<INPUT type="hidden" name="script_case_init" value="<?php echo NM_encode_input($this->Ini->sc_page); ?>"> 
<INPUT type="hidden" name="script_case_session" value="<?php echo NM_encode_input(session_id()); ?>"> 
<INPUT type="hidden" name="nmgp_opcao" value="volta_grid"> 
</FORM> 
</BODY>
</HTML>
<?php
   }
   function nm_gera_mask(&$nm_campo, $nm_mask)
   { 
      $trab_campo = $nm_campo;
      $trab_mask  = $nm_mask;
      $tam_campo  = strlen($nm_campo);
      $trab_saida = "";
      $mask_num = false;
      for ($x=0; $x < strlen($trab_mask); $x++)
      {
          if (substr($trab_mask, $x, 1) == "#")
          {
              $mask_num = true;
              break;
          }
      }
      if ($mask_num )
      {
          $ver_duas = explode(";", $trab_mask);
          if (isset($ver_duas[1]) && !empty($ver_duas[1]))
          {
              $cont1 = count(explode("#", $ver_duas[0])) - 1;
              $cont2 = count(explode("#", $ver_duas[1])) - 1;
              if ($cont2 >= $tam_campo)
              {
                  $trab_mask = $ver_duas[1];
              }
              else
              {
                  $trab_mask = $ver_duas[0];
              }
          }
          $tam_mask = strlen($trab_mask);
          $xdados = 0;
          for ($x=0; $x < $tam_mask; $x++)
          {
              if (substr($trab_mask, $x, 1) == "#" && $xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_campo, $xdados, 1);
                  $xdados++;
              }
              elseif ($xdados < $tam_campo)
              {
                  $trab_saida .= substr($trab_mask, $x, 1);
              }
          }
          if ($xdados < $tam_campo)
          {
              $trab_saida .= substr($trab_campo, $xdados);
          }
          $nm_campo = $trab_saida;
          return;
      }
      for ($ix = strlen($trab_mask); $ix > 0; $ix--)
      {
           $char_mask = substr($trab_mask, $ix - 1, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               $trab_saida = $char_mask . $trab_saida;
           }
           else
           {
               if ($tam_campo != 0)
               {
                   $trab_saida = substr($trab_campo, $tam_campo - 1, 1) . $trab_saida;
                   $tam_campo--;
               }
               else
               {
                   $trab_saida = "0" . $trab_saida;
               }
           }
      }
      if ($tam_campo != 0)
      {
          $trab_saida = substr($trab_campo, 0, $tam_campo) . $trab_saida;
          $trab_mask  = str_repeat("z", $tam_campo) . $trab_mask;
      }
   
      $iz = 0; 
      for ($ix = 0; $ix < strlen($trab_mask); $ix++)
      {
           $char_mask = substr($trab_mask, $ix, 1);
           if ($char_mask != "x" && $char_mask != "z")
           {
               if ($char_mask == "." || $char_mask == ",")
               {
                   $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
               }
               else
               {
                   $iz++;
               }
           }
           elseif ($char_mask == "x" || substr($trab_saida, $iz, 1) != "0")
           {
               $ix = strlen($trab_mask) + 1;
           }
           else
           {
               $trab_saida = substr($trab_saida, 0, $iz) . substr($trab_saida, $iz + 1);
           }
      }
      $nm_campo = $trab_saida;
   } 
}

?>
