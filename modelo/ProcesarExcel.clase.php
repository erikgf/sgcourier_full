<?php 

class ProcesarExcel{
	public $data_de_filas_excel;
	public $id_pedido;

  private $PREFIJO_CODIGO_REMITO_LEONISA;

  public function __construct($BD = null){
      try {
          $this->PREFIJO_CODIGO_REMITO_LEONISA = "0".GlobalVariables::$ID_LEONISA;
      } catch (Exception $e) {
          throw new Exception($e->getMessage());
      }
      
  }

  public function fuxion(){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 2; /*0: NULA, 1: CABECERA*/
        $ultimaColumna = 23; /*VOLUMEN*/

        $campos = [
            "id_pedido",
            "codigo_remito",
            "codigo_tracking",
            "codigo_exigo",
            "envoltura",
            "fecha_courier",
            "numero_envio",
            "id_envio",
            "peso_paquetes",
            "numero_paquetes",
            "numero_documento_destinatario",
            "destinatario",
            "direccion_uno",
            "distrito",
            "provincia",
            "region",
            "pais",
            "referencia",
            "correo_contacto",
            "telefono_contacto",
            "celular_contacto",
            "contacto",
            "documentos",
            "rotulo_courier",
            "volumen"
        ];

        /*
        array(25) {
          [0]=>
          string(9) "SG-708814"
          [1]=>
          string(7) "3901649"
          [2]=>
          string(7) "7075136"
          [3]=>
          string(16) "2 CAJA MEDIANA 2"
          [4]=>
          string(18) "24-09-2020 8:15:25"
          [5]=>
          float(2)
          [6]=>
          float(708814)
          [7]=>
          float(1.9)
          [8]=>
          float(1)
          [9]=>
          float(1060888)
          [10]=>
          string(30) "Gloria Elena Valdizán Herrera"
          [11]=>
          string(22) "Jiron Jorge Chavez 681"
          [12]=>
          string(7) "TOCACHE"
          [13]=>
          string(7) "TOCACHE"
          [14]=>
          string(10) "SAN MARTIN"
          [15]=>
          string(4) "PERU"
          [16]=>
          string(69) "Una cuadra antes de llegar al hospital de tocache  (LLAMAR 935712900)"
          [17]=>
          string(25) "elenavaldizan11@gmail.com"
          [18]=>
          string(9) "935712900"
          [19]=>
          string(9) "935712900"
          [20]=>
          string(22) "Jarubi Lopez Velazquez"
          [21]=>
          string(14) "BOL 911-86660,"
          [22]=>
          string(13) "SG CARGO EIRL"
          [23]=>
          float(0.04001)
          [24]=>
          NULL
        }
        */

        if ($cantidad < $filaInicio - 1){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $primera_fila = $this->data_de_filas_excel[0];
        $segunda_fila = $this->data_de_filas_excel[1];
        if (!($primera_fila[0] == NULL &&
                ($segunda_fila[0] != NULL && 
                $segunda_fila[$ultimaColumna] != NULL && 
                $segunda_fila[$ultimaColumna + 1] == NULL)) ){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            $fila = [$this->id_pedido];

            for ($j=0; $j <= $ultimaColumna ; $j++) {
                switch($j){
                    case 10:
                    case 11:
                    case 16:
                    case 17:
                    case 20:
                    $tmp_fila[$j] = str_replace("'", "", $tmp_fila[$j]);
                    break;
                }

                array_push($fila, $tmp_fila[$j]);
            }

            array_push($valores, $fila);
        }

        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
  }

  public function leonisa(){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 1; /*0: CABECERA*/
        $ultimaColumna = 16; /*EMBARQ*/

        $campos = [
            "id_pedido",
            "numero_paquetes",
            "zona",
            "campana",
            "rotulo_courier",
            "codigo_exigo",
            "codigo_tracking",
            "codigo_remito",
            "codigo_guia",
            "numero_documento_destinatario",
            "destinatario",
            "telefono_uno_destinatario",
            "telefono_dos_destinatario",
            "celular_destinatario",
            "direccion_uno",
            "referencia",
            "distrito",
            "provincia",
            "region",
            "correo_contacto",
            "fecha_courier"
        ];

        /*
        array(25) {
          [0]=>
          string(9) "ZONA"
          [1]=>
          string(7) "CAMPAÑA"
          [2]=>
          string(16) "TRANSPORTISTA"
          [3]=>
          string(18) "NUMERO RDEN"
          [4]=>
          string(18) "CODIGO GUIA"
          [5]=>
          DNI
          [6]=>
          APELLIDOS y NOMBRES
          [7]=>
           TELEFONO 1 
          [8]=>
          TELEFONO 2 
          [9]=>
          string(30) CELULAR
          [10]=>
          string(22) DIRECCION
          [11]=>
          string(7) URBANIZACIÓN / BARRIO / ASOC.
          [12]=>
          string(7)  DISTRITO / CIUDAD
          [13]=>
          string(10)  DPTO. + PROVINCIA 
          [14]=>
          string(4) Correo
          [15]=>
          string(4) Fecha Embarque
        }
        */
        $primera_fila = $this->data_de_filas_excel[0];

        if ($cantidad <= $filaInicio){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }   

        if ($primera_fila[$ultimaColumna] != NULL &&  $primera_fila[$ultimaColumna + 1] == NULL){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            //$codigo_remito = $this->PREFIJO_CODIGO_REMITO_LEONISA."-".$this->id_pedido."-".str_pad($i, 6, '0', STR_PAD_LEFT);
            //$codigo_tracking = $codigo_remito;
            $numero_paquetes = 1;
            $fila = [$this->id_pedido, $numero_paquetes];

            for ($j=0; $j <= $ultimaColumna ; $j++) {
                switch($j){
                    case 5:
                    case 9:
                    case 10:
                    case 11:
                    case 13:
                    $tmp_fila[$j] = mb_strtoupper(str_replace("'", "", $tmp_fila[$j]), 'UTF-8');
                    if ($j == 3){
                        array_push($fila, $tmp_fila[$j]); //CODIGO EXIGO
                                                          //ABAJO YA CODIGO TRACKING
                    }
                    if ($j == 4){
                        array_push($fila, $tmp_fila[$j]); //CODIGO REMITO
                                                          //ABAJO YA CODIGO GUIA
                    }
                    if ($j == 13){
                        $tmp_arr = explode("-",$tmp_fila[$j]);
                        array_push($fila, $tmp_arr[1]);
                        $tmp_fila[$j] = $tmp_arr[0];
                    }
                    break;
                    case 15: 
                    $tmp_fila[$j] = $this->convertirFechaExcelAFecha($tmp_fila[$j]);
                    break;
                }
                array_push($fila, $tmp_fila[$j]);
            }

            array_push($valores, $fila);
        }


        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
    }
    
  public function leonisa_catalogos(){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 1; /*0: CABECERA*/
        $ultimaColumna = 12; 

        $campos = [
            "id_pedido",
            "codigo_remito",
            "codigo_tracking",
            "numero_paquetes",
            "zona",
            "catalogo",
            "rotulo_courier",
            "numero_documento_destinatario",
            "destinatario",
            "telefono_uno_destinatario",
            "telefono_dos_destinatario",
            "celular_destinatario",
            "campana",
            "direccion_uno",
            "referencia",
            "distrito",
            "provincia",
            "region"
        ];

        /*
          [0]=>
          string(9) "ZONA"
          [1]=>
          string(7) "catalogo"
          [2]=>
          string(16) "TRANSPORTISTA"
          [3]=>
          DNI
          [4]=>
          APELLIDOS y NOMBRES
          [5]=>
           TELEFONO 1 
          [6]=>
          TELEFONO 2 
          [7]=>
          string(30) CELULAR
          [8]=>
          string(30) CAMPANA
          [9]=>
          string(22) DIRECCION
          [10]=>
          string(7) URBANIZACIÓN / BARRIO / ASOC.
          [11]=>
          string(7)  DISTRITO / CIUDAD
          [12]=>
          string(10)  DPTO. + PROVINCIA 
        }
        */
        $primera_fila = $this->data_de_filas_excel[0];

        if ($cantidad <= $filaInicio){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }   

        if ($primera_fila[$ultimaColumna] != NULL &&  count($primera_fila) > ($ultimaColumna + 1)){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            $codigo_remito = $this->PREFIJO_CODIGO_REMITO_LEONISA."-".$this->id_pedido."-".str_pad($i, 6, '0', STR_PAD_LEFT);
            $codigo_tracking = $codigo_remito;
            $numero_paquetes = 1;
            $fila = [$this->id_pedido, $codigo_tracking, $codigo_remito, $numero_paquetes];

            for ($j=0; $j <= $ultimaColumna ; $j++) {
                switch($j){
                    case 5:
                    case 9:
                    case 10:
                    case 11:
                    case 12:
                    $tmp_fila[$j] = mb_strtoupper(str_replace("'", "", $tmp_fila[$j]), 'UTF-8');
                    if ($j == 12){
                        $tmp_arr = explode("-",$tmp_fila[$j]);
                        array_push($fila, $tmp_arr[1]);
                        $tmp_fila[$j] = $tmp_arr[0];
                    }
                    break;
                    break;
                }
                array_push($fila, $tmp_fila[$j]);
            }

            array_push($valores, $fila);
        }


        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
    }

  public function minedu(){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 2; /*0: vacía, 1: CABECERA. */
        $ultimaColumna = 11; 

        $campos = [
            "id_pedido",
            "numero_paquetes",
            "codigo_tracking", /*NUMERO FICHA DE COURIER*/
            "minedu_tipo_documento", /*tipo doc*/
            "minedu_nro_doc",
            "codigo_remito", /*NUMERO DOCUMENTO*/
            "destinatario",
            "distrito",
            "provincia",
            "region",
            "direccion_uno",
            "minedu_cobertura",
            "minedu_prioridad",
            "minedu_oficina"
        ];

        /*Nº   0
            N° Ficha Courier 1
            Tipo Doc    2
            Nro. Doc.    3 4  
            Destinatario  5  
            Ubigeo  6
            Dirección   7
            Cobertura   8
            Prioridad   9
            Oficina 10*/

        $primera_fila = $this->data_de_filas_excel[$filaInicio - 1];

        if ($cantidad <= $filaInicio){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }   

        if ($primera_fila[$ultimaColumna] != NULL &&  $primera_fila[$ultimaColumna + 1] == NULL){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            //$codigo_remito = $this->PREFIJO_CODIGO_REMITO_LEONISA."-".$this->id_pedido."-".str_pad($i, 6, '0', STR_PAD_LEFT);
            //$codigo_tracking = $codigo_remito;
            $numero_paquetes = 1;
            $fila = [$this->id_pedido, $numero_paquetes];

            $columnaInicio = 1; /*0 = numero orden*/
            for ($j=$columnaInicio; $j <= $ultimaColumna ; $j++) {
                if ($j == 4){ /*Se saltea la 4 porque la columna 3 (NRO DOC, OCUPA DOS COLUMNAS)*/
                    continue;
                }
                switch($j){
                    case 3:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    case 9:
                    case 10:
                    $tmp_fila[$j] = mb_strtoupper(str_replace("'", "", $tmp_fila[$j]), 'UTF-8');
                    if ($j == 6){
                        $tmp_arr = explode("-",$tmp_fila[$j]); /* ubigeo_ region - provincia distrito */
                        array_push($fila, $tmp_arr[2]);
                        array_push($fila, $tmp_arr[1]);
                        $tmp_fila[$j] = $tmp_arr[0];
                    }
                    break;
                }

                if ($j == 3 ){
                    array_push($fila, $tmp_fila[$j]);
                }

                array_push($fila, $tmp_fila[$j]);
            }

            array_push($valores, $fila);
        }

        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
  }

  public function pronabec(){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 1; /*0: CABECERA. */
        $ultimaColumna = 13; 

        $campos = [
            "id_pedido",
            "numero_paquetes",
            "item",
            "fecha_ingreso",
            "pronabec_sigedo",
            "codigo_remito", /*CODIGO*/
            "codigo_tracking",
            "pronabec_oficina",
            "pronabec_orden",
            "pronabec_correlativo",
            "destinatario",
            "direccion_uno",
            "distrito",
            "provincia",
            "region",
            "fecha_salida"
        ];

        /*
        "item", 0
        "fecha_ingreso", 1
        "pronabec_sigedo", 2
        "codigo_remito", 3  3 |R
        "codigo_tracking", 4 3|R
        "pronabec_oficina" 4
        "pronabec_orden", 5
        "pronabec_correlativo", 6
        "destinatario", 7
        "direccion_uno", 8
        "distrito", 9
        "provincia", 10
        "region", 11
        "fecha_salida" 12
        */

        $primera_fila = $this->data_de_filas_excel[$filaInicio - 1];

        if ($cantidad <= $filaInicio){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }   

        if ($primera_fila[$ultimaColumna] != NULL &&  $primera_fila[$ultimaColumna + 1] == NULL){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            //$codigo_remito = $this->PREFIJO_CODIGO_REMITO_LEONISA."-".$this->id_pedido."-".str_pad($i, 6, '0', STR_PAD_LEFT);
            //$codigo_tracking = $codigo_remito;
            $numero_paquetes = 1;
            $fila = [$this->id_pedido, $numero_paquetes];

            $columnaInicio = 0;
            for ($j=$columnaInicio; $j <= $ultimaColumna ; $j++) {
                switch($j){
                    case 5:
                    case 8:
                    case 9:
                    case 10:
                    $tmp_fila[$j] = mb_strtoupper(str_replace("'", "", $tmp_fila[$j]), 'UTF-8');
                    break;
                    case 1: 
                    case 12: 
                    $tmp_fila[$j] = $this->convertirFechaExcelAFecha($tmp_fila[$j]);
                    break;
                }

                if ($j == 3 ){
                    array_push($fila, $tmp_fila[$j]);
                }

                array_push($fila, $tmp_fila[$j]);
            }

            array_push($valores, $fila);
        }

        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
  }

  public function pronied(){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 1; /*0: CABECERA. */
        $ultimaColumna = 13; 

        $campos = [
            "id_pedido",
            "numero_paquetes",
            "item",
            "forma_envio",
            "tipo_envio",
            "numero_expediente",
            "codigo_remito", /*n documento*/
            "pronied_unidad_organica",
            "distrito",
            "provincia",
            "region",
            "destinatario",
            "direccion_uno",
            "fecha_ingreso",
            "costo_envio",
            "ticket_factura",
            "fecha_salida"
        ];


        /*
        ITEM 0
         FORMA DE ENVIO 1
          TIPO DE ENVIO  2
          Nº EXPEDIENTE  3
          Nº DOCUMENTO   4
          UNIDAD ORGANICA  5
          UBICACIÓN  6
          DESTINATARIO   7
          DIRECCION DESTINATARIO   8
          FECHA DE INGRESO  9
          COSTO DE ENVIO   10
          TICKET FACTURA  11
          ENTREGADO AL COURIER 12

        */

        $primera_fila = $this->data_de_filas_excel[$filaInicio - 1];

        if ($cantidad <= $filaInicio){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }   

        if ($primera_fila[$ultimaColumna] != NULL &&  $primera_fila[$ultimaColumna + 1] == NULL){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            //$codigo_remito = $this->PREFIJO_CODIGO_REMITO_LEONISA."-".$this->id_pedido."-".str_pad($i, 6, '0', STR_PAD_LEFT);
            //$codigo_tracking = $codigo_remito;
            $numero_paquetes = 1;
            $fila = [$this->id_pedido, $numero_paquetes];

            $columnaInicio = 0;
            for ($j=$columnaInicio; $j <= $ultimaColumna ; $j++) {
                switch($j){
                    case 1:
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    case 6:
                    case 7:
                    case 8:
                    $tmp_fila[$j] = mb_strtoupper(str_replace("'", "", $tmp_fila[$j]), 'UTF-8');

                   if ($j == 6){
                      /*seperando la columna ubicación | para UBIGEO*/
                        $tmp_arr = explode("-",$tmp_fila[$j]);
                        array_push($fila, $tmp_arr[2]);
                        array_push($fila, $tmp_arr[1]);
                        $tmp_fila[$j] = $tmp_arr[0];
                    }

                    break;
                    case 9: 
                    case 12: 
                    $tmp_fila[$j] = $this->convertirFechaExcelAFecha($tmp_fila[$j]);
                    break;
                }

               
                array_push($fila, $tmp_fila[$j]);
            }

            array_push($valores, $fila);
        }

        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
  }

  private function convertirFechaExcelAFecha($fecha_excel){
    return gmdate("Y-m-d H:i:s", ($fecha_excel - 25569) * 86400);
  }
  

    public function poder_judicial($id_area){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 0; /*0: NULA, 1: CABECERA*/
        $ultimaColumna = 6; /**/
    
        $campos = [
            "id_area",//0
            "id_usuario_registro",
            "fecha_recepcion", //0
            "numero_guia",
            "remitente",
            "dependencia",
            "consignatario",
            "destino",
            "fecha_entrega", //6
            "fue_devuelto"
        ];
    
        if ($cantidad < $filaInicio - 1){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[], "campos_valores"=>[]];
        }
    
        $valores = [];
        $campos_valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            $fila = [$id_area, $this->id_usuario_registro];
    
            $formatoFechaExcelABaseDatos = "Y-m-d";
            
            for ($j=0; $j <= $ultimaColumna ; $j++) {
                $agregar = true;
                switch($j){
                    case 0:
                    if ($tmp_fila[$j] == "" || $tmp_fila[$j] == NULL){ 
                        $agregar = false;
                    } 
                    else {
                        $tmp_fila[$j] = $this->convertirFechaExcelAFecha($tmp_fila[$j], $formatoFechaExcelABaseDatos);
                    }
                    break;
                    case 2:
                    case 3:
                    case 4:
                    case 5:
                    $tmp_fila[$j] = strtoupper(str_replace("'", "", $tmp_fila[$j]));
                    break;
                    case 6: 
                    $item = $tmp_fila[$j];
                    $fue_devuelto = 0;
                    if (!($item == NULL || strlen($item) <= 0)){
                      if (!is_numeric($item)){
                        $fue_devuelto = 1;
                        $item = NULL;
                      } else{
                        $item = $this->convertirFechaExcelAFecha($item, $formatoFechaExcelABaseDatos);
                      }
                    }
    
                    $tmp_fila[$j] = $item;
                    array_push($fila, $tmp_fila[$j]);
                    $tmp_fila[$j] = $fue_devuelto;
                    break;
                }
                
                if ($agregar){
                    array_push($fila, $tmp_fila[$j]);  
                }
            }
    
            if (!($fila[2] == NULL || $fila[2] == "")){
                array_push($campos_valores, 
                    ["id_area"=>$fila[0],
                    "id_usuario_registro"=>$fila[1],
                    "fecha_recepcion"=>$fila[2],
                    "numero_guia"=>$fila[3],
                    "remitente"=>$fila[4],
                    "dependencia"=>$fila[5],
                    "consignatario"=>$fila[6],
                    "destino"=>$fila[7],
                    "fecha_entrega"=>$fila[8],
                    "fue_devuelto"=>$fila[9],
                        ]);
                
                // array_push($valores, $fila);
            }
        }
    
        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores, "campos_valores"=>$campos_valores];
    }
  
  public function preliquidaciones_importacion(){
        $cantidad = count($this->data_de_filas_excel);
        $filaInicio = 1; /*0: sin cabcera, 1: CABECERA*/
        $ultimaColumna = 10; /*cantidad de columnas*/

        if ($cantidad < $filaInicio - 1){
            return ["formato_valido"=>false, "registros"=>[]];
        }

        $registros = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $this->data_de_filas_excel[$i];
            $fila = [];

            //$formatoFechaExcelABaseDatos = "Y-m-d";
            for ($j=0; $j < $ultimaColumna ; $j++) {
                switch($j){
                    case 3:
                      if ($tmp_fila[$j] == NULL || $tmp_fila[$j] == ""){
                        $tmp_fila[$j] = "1";
                      }
                      break;
                    case 4:
                      $tipo_paquete = strtoupper($tmp_fila[$j]);
                      $tmp_fila[$j] =  "";
                      
                      switch($tipo_paquete){
                        case "":
                        case "PAQUETE":
                             $tmp_fila[$j] = "1";
                             break;
                        case "SOBRE":
                             $tmp_fila[$j] = "2";
                             break;
                        case "CAJA":
                             $tmp_fila[$j] = "3";
                             break;
                        case "MUEBLE":
                             $tmp_fila[$j] = "4";
                             break;
                        case "MUDANZA":
                             $tmp_fila[$j] = "5";
                             break;
                        case "PAQUETON":
                             $tmp_fila[$j] = "6";
                             break;
                        case "SACO":
                             $tmp_fila[$j] = "7";
                             break;
                        case "CARGA":
                             $tmp_fila[$j] = "8";
                             break;
                        case "OTRO":
                             $tmp_fila[$j] = "9";
                             break;
                        default:
                             $tmp_fila[$j] = "1";
                      }
                      break;
                    case 9: 
                      if ($tmp_fila[$j] == NULL || $tmp_fila[$j] == ""){
                        $tmp_fila[$j] = "0.00";
                      }
                    break;
                }

                $tmp_fila[$j] = strtoupper(str_replace("'", "", $tmp_fila[$j]));
                array_push($fila, $tmp_fila[$j]);
            }
            
            array_push($registros, 
                        [ "cliente"=>$fila[0],
                          "cliente_interno"=>$fila[1],
                          "documento_guia"=>$fila[2],
                          "cantidad"=>$fila[3],
                          "tipo_paquete"=>$fila[4],
                          "peso"=>$fila[5],
                          "volumen"=>$fila[6],
                          "direccion_entrega"=>$fila[7],
                          "lugar_entrega"=>$fila[8],
                          "costo_unitario"=>$fila[9],
                          "subtotal"=>$fila[3] * $fila[9]
                          ]);
        }

        return ["formato_valido"=>true, "registros"=>$registros];
  }

    public function nuevoLeonisa(){
    $cantidad = count($this->data_de_filas_excel);
    $filaInicio = 1; /*0: CABECERA*/
    $ultimaColumna = 15; /*EMBARQ Pos 15*/

    $campos = [
        "id_pedido",
        "zona",
        "campaña",
        "rotulo_courier",
        "codigo_numero_orden",
        "codigo_tracking",
        "codigo_guia",
        "numero_documento_destinatario",
        "destinatario",
        "telefono_uno_destinatario",
        "telefono_dos_destinatario",
        "celular_destinatario",
        "direccion_uno",
        "referencia",
        "distrito",
        "provincia",
        "departamento",
        "correo_contacto",
        "fecha_courier"
    ];

    $primera_fila = $this->data_de_filas_excel[0];

    if ($cantidad <= $filaInicio){
        return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
    }   

    if ($primera_fila[$ultimaColumna] != NULL &&  count($primera_fila) > ($ultimaColumna + 1)){
        return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
    }

    $valores = [];
    for ($i=$filaInicio; $i < $cantidad; $i++) { 
        $tmp_fila = $this->data_de_filas_excel[$i];
        //$codigo_remito = $this->PREFIJO_CODIGO_REMITO_LEONISA."-".$this->id_pedido."-".str_pad($i, 6, '0', STR_PAD_LEFT);
        //$codigo_tracking = $codigo_remito;
        $fila = [$this->id_pedido];

        for ($j=0; $j <= $ultimaColumna ; $j++) {
            switch($j){
                case 4:
                    array_push($fila, $tmp_fila[$j]); 
                    break;
                case 6:
                case 10:
                case 12:
                case 13:
                case 14:
                    $tmp_fila[$j] = mb_strtoupper(str_replace("'", "", $tmp_fila[$j]), 'UTF-8');

                    if ($j == 13){
                        $tmp_arr = explode("-",$tmp_fila[$j]); // separara provincia + dep
                        array_push($fila, $tmp_arr[1]);
                        $tmp_fila[$j] = $tmp_arr[0];
                    }
                break;
                case 15: 
                $tmp_fila[$j] = $this->convertirFechaExcelAFecha($tmp_fila[$j]);
                break;
            }
            array_push($fila, $tmp_fila[$j]);
        }

        array_push($valores, $fila);
    }


    return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
    }

}