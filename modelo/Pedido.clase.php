<?php 

include_once "../phspreadsheet/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

require_once 'Modelo.clase.php';

class Pedido extends Modelo{

    public $id_usuario; /*cliente*/
    public $id_pedido;

    public $dias_ruta;
    public $dias_gestionando;
    public $fecha_ingreso;
    public $id_cliente;
    public $id_usuario_registro;
    public $archivo;

    public $id_tipo_usuario;
    public $estado;
    public $tipo_pedido;

    private $PREFIJO_CODIGO_REMITO_LEONISA;

    public function __construct($BD = null){
        try {
            parent::__construct("pedido", $BD); 
            $this->PREFIJO_CODIGO_REMITO_LEONISA = "0".GlobalVariables::$ID_LEONISA;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        
    }

    public function listar($fi, $ff, $id_cliente_especifico = NULL, $tipo_pedido = NULL){
        try{

            if (!$this->id_usuario){
                return [];
            }

            $params = [$fi, $ff];
            $sql_cliente_especifico = "";
            if ($id_cliente_especifico != NULL){
                $nParam = count($params);
                array_push($params, $id_cliente_especifico);
                $sql_cliente_especifico = " AND p.id_cliente = :".$nParam;
            }
    
            $sql_tipo_pedido = "";
            if ($tipo_pedido != NULL){
                $nParam = count($params);
                array_push($params, $tipo_pedido);
                $sql_tipo_pedido = " AND p.tipo_pedido = :".$nParam;
            }
            
            $sql = "SELECT LPAD(id_pedido,7,'0') as id_pedido_log, 
                        id_pedido as id, 
                    DATE_FORMAT(fecha_ingreso,'%d-%m-%Y') as fecha_ingreso,
                    numero_documento,
                    COALESCE(c.razon_social, CONCAT(c.nombres,' ',c.apellidos)) as razon_social,
                    COALESCE(c.celular,'-') as celular,
                    cantidad, 
                    cantidad_noasignadas as cantidad_noasignado,
                    cantidad_gestionando as cantidad_gestionando, 
                    cantidad_entregadas as cantidad_entregado, 
                    cantidad_motivadas as cantidad_motivado
                    FROM pedido p
                    LEFT JOIN usuario c ON c.id_usuario = p.id_cliente
                    WHERE p.estado_mrcb AND (p.fecha_ingreso >= :0 AND p.fecha_ingreso <= :1) ".$sql_cliente_especifico." ".$sql_tipo_pedido;
                    
            $data = $this->BD->consultarFilas($sql, $params);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarXUsuario($fi, $ff){
        try{

            if (!$this->id_usuario){
                return [];
            }

            $sql = "SELECT LPAD(id_pedido,7,'0') as id_pedido_log, 
                        id_pedido as id, 
                    DATE_FORMAT(fecha_ingreso,'%d-%m-%Y') as fecha_ingreso,
                    cantidad, cantidad_noasignadas, cantidad_gestionando, cantidad_entregadas, cantidad_motivadas,
                    (SELECT GROUP_CONCAT(codigo_remito,' ') FROM pedido_orden po WHERE po.id_pedido = p.id_pedido) as codigo_remitos
                    FROM pedido p
                    WHERE p.estado_mrcb AND p.id_cliente = :0 AND (p.fecha_ingreso >= :1 AND p.fecha_ingreso <= :2)";
            $data = $this->BD->consultarFilas($sql, [$this->id_usuario, $fi, $ff]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerXIdCliente(){
        try{

            if (!$this->id_usuario){
                return null;
            }

            $sql = "SELECT  LPAD(id_pedido,7,'0') as id_pedido, 
                            DATE_FORMAT(fecha_ingreso,'%d-%m-%Y') as fecha_ingreso,
                            cantidad, 
                            cantidad_noasignadas, 
                            cantidad_gestionando,
                            cantidad_entregadas, 
                            cantidad_motivadas
                            FROM pedido p
                            WHERE p.estado_mrcb AND p.id_cliente = :0 AND p.id_pedido = :1";
            $data = $this->BD->consultarFila($sql, [$this->id_usuario, $this->id_pedido]);

            if ($data == false){
                throw new Exception("ID Pedido no válido.");
            }

            $sql = "SELECT
                    po.id_pedido_orden,
                    COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                    id_pedido,
                    COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                    COALESCE(pov.observaciones,'') as observaciones,
                    codigo_remito,
                    fecha_courier,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    UPPER(COALESCE(direccion_dos,'')) as direccion_dos,
                    referencia,
                    UPPER(CONCAT(distrito,' - ', provincia)) as distrito_provincia,
                    UPPER(region) as region,
                    numero_paquetes,
                    celular_contacto as celular,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'GESTIONANDO' 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN 'MOTIVADO' 
                        ELSE 'EN RUTA' END) as estado,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'info' 
                        WHEN 'E' THEN 'success'
                        WHEN 'M' THEN 'danger' 
                        ELSE 'dark' END) as estado_color,
                    po.estado_actual
                    FROM pedido_orden po
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    WHERE po.estado_mrcb AND po.id_pedido = :0";
            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerXId($id_cliente_especifico){
        try{

            if (!$this->id_usuario){
                return null;
            }

            $data = $this->_leerXIdCabecera();
            $data["registros"] = $this->BD->consultarFilas(
                $this->_getSQLListaPedidosOrdenesXIdAdmin($id_cliente_especifico), 
                [$this->id_pedido]);
                
             /*repartidores*/
            $sql = "SELECT
                    id_usuario as id,
                    CONCAT(nombres,' ',apellidos) as nombres_apellidos
                    FROM usuario u
                    WHERE u.estado_mrcb AND (u.id_tipo_usuario = 3  OR (u.id_tipo_usuario = 2 AND u.es_ejecutivo_repartidor))
                    ORDER BY 2";
            $data["usuarios_asignar"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            /*ciudades*/
            $sql = "SELECT
                    distinct(distrito) as ciudad
                    FROM pedido_orden po
                    WHERE po.estado_mrcb AND po.id_pedido = :0
                    ORDER BY 1";
            $data["ciudades"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);
    

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    private function _leerXIdCabecera(){
        try{
            $sql = "SELECT  LPAD(id_pedido,7,'0') as id_pedido, 
                            DATE_FORMAT(fecha_ingreso,'%d-%m-%Y') as fecha_ingreso,
                            numero_documento,
                            UPPER(COALESCE(c.razon_social, CONCAT(c.nombres,' ',c.apellidos))) as razon_social,
                            UPPER(COALESCE(c.direccion,'-')) as direccion,
                            COALESCE(c.celular,'-') as celular,
                            cantidad, 
                            cantidad_noasignadas,
                            cantidad_gestionando, 
                            cantidad_entregadas, 
                            cantidad_motivadas
                            FROM pedido p
                            LEFT JOIN usuario c ON c.id_usuario = p.id_cliente
                            WHERE p.estado_mrcb AND p.id_pedido = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_pedido]);

            if ($data == false){
                throw new Exception("ID Pedido no válido.");
            }

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarOrdenesXId($id_cliente_especifico = NULL){
        try{

            $sqlEstado = " true "; 
            if ($this->estado != ""){
                $sqlEstado = " po.estado_actual IN ('".$this->estado."') "; 
            }

            $sql = "SELECT
                    po.id_pedido_orden,
                    COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                    id_pedido,
                    COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                    COALESCE(pov.observaciones,'') as observaciones,
                    codigo_remito,
                    fecha_courier,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    UPPER(COALESCE(direccion_dos,'')) as direccion_dos,
                    referencia,
                    UPPER(CONCAT(distrito,' - ', provincia)) as distrito_provincia,
                    UPPER(region) as region,
                    numero_paquetes,
                    celular_contacto as celular,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'GESTIONANDO' 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN 'MOTIVADO' 
                        ELSE 'EN RUTA' END) as estado,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'info' 
                        WHEN 'E' THEN 'success'
                        WHEN 'M' THEN 'danger' 
                        ELSE 'dark' END) as estado_color,
                    po.estado_actual
                    FROM pedido_orden po
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    WHERE po.estado_mrcb AND po.id_pedido = :0 AND $sqlEstado
                    ORDER BY id_pedido_orden";
            $data = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarOrdenesXIdAdmin($id_cliente_especifico = NULL){
        try{

            $sqlEstado = " AND true "; 
            if ($this->estado != ""){
                if ($this->id_tipo_usuario == "4" && $this->estado == "G"){
                    /*considerar tam,bién no asignados*/
                    $sqlEstado = " AND po.estado_actual IN ('N','G') "; 
                } else {
                    $sqlEstado = " AND po.estado_actual IN ('".$this->estado."') "; 
                }
            }

            $sql = $this->_getSQLListaPedidosOrdenesXIdAdmin($id_cliente_especifico).$sqlEstado;

            $data = $this->BD->consultarFilas($sql, [$this->id_pedido]);
            

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerXIdAgencia(){
        try{

            if (!$this->id_usuario){
                return null;
            }

            $sql = "SELECT  LPAD(id_pedido,7,'0') as id_pedido, 
                            DATE_FORMAT(fecha_ingreso,'%d-%m-%Y') as fecha_ingreso,
                            numero_documento,
                            COALESCE(c.razon_social, CONCAT(c.nombres,' ',c.apellidos)) as razon_social,
                            COALESCE(c.direccion,'-') as direccion,
                            COALESCE(c.celular,'-') as celular,
                            cantidad, 
                            cantidad_noasignadas,
                            cantidad_gestionando, 
                            cantidad_entregadas, 
                            cantidad_motivadas
                            FROM pedido p
                            LEFT JOIN usuario c ON c.id_usuario = p.id_cliente
                            WHERE p.estado_mrcb AND p.id_pedido = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_pedido]);

            if ($data == false){
                throw new Exception("ID Pedido no válido.");
            }

            $sql = "SELECT
                    po.id_pedido_orden,
                    COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                    COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                    id_pedido,
                    distrito as ciudad,
                    COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                    COALESCE(pov.observaciones,'') as observaciones,
                    codigo_remito,
                    fecha_courier,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    UPPER(COALESCE(direccion_dos,'')) as direccion_dos,
                    referencia,
                    UPPER(CONCAT(distrito,' - ', provincia)) as distrito_provincia,
                    UPPER(region) as region,
                    numero_paquetes,
                    celular_contacto as celular,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'GESTIONANDO' 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN 'MOTIVADO' 
                        ELSE 'NO ASIGNADO' END) as estado,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'info' 
                        WHEN 'E' THEN 'success'
                        WHEN 'M' THEN 'danger' 
                        ELSE 'dark' END) as estado_color,
                    po.estado_actual,
                    veces_asignado,
                    numero_visitas
                    FROM pedido_orden po
                    LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    WHERE po.estado_mrcb AND po.id_pedido = :0";
            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);


            /*repartidores*/
            $sql = "SELECT
                    id_usuario as id,
                    CONCAT(nombres,' ',apellidos) as nombres_apellidos
                    FROM usuario u
                    WHERE u.estado_mrcb AND (u.id_tipo_usuario = 3  OR (u.id_tipo_usuario = 2 AND u.es_ejecutivo_repartidor))
                    ORDER BY 2";
            $data["usuarios_asignar"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            /*ciudades*/
            $sql = "SELECT
                    distinct(distrito) as ciudad
                    FROM pedido_orden po
                    WHERE po.estado_mrcb AND po.id_pedido = :0
                    ORDER BY 1";
            $data["ciudades"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function listarOrdenesXIdAgencia(){
        try{

            $sqlEstado = " true "; 
            if ($this->estado != ""){
                if ($this->id_tipo_usuario == "4" && $this->estado == "G"){
                    /*considerar tam,bién no asignados*/
                    $sqlEstado = " po.estado_actual IN ('N','G') "; 
                } else {
                    $sqlEstado = " po.estado_actual IN ('".$this->estado."') "; 
                }
            }

            $sql = "SELECT
                    po.id_pedido_orden,
                    COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                    COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                    id_pedido,
                    COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                    COALESCE(pov.observaciones,'') as observaciones,
                    codigo_remito,
                    fecha_courier,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    UPPER(COALESCE(direccion_dos,'')) as direccion_dos,
                    referencia,
                    UPPER(CONCAT(distrito,' - ', provincia)) as distrito_provincia,
                    UPPER(region) as region,
                    numero_paquetes,
                    celular_contacto as celular,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'GESTIONANDO' 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN 'MOTIVADO' 
                        ELSE 'NO ASIGNADO' END) as estado,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'info' 
                        WHEN 'E' THEN 'success'
                        WHEN 'M' THEN 'danger' 
                        ELSE 'dark' END) as estado_color,
                     numero_visitas
                    FROM pedido_orden po
                    LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    WHERE po.estado_mrcb AND po.id_pedido = :0 AND $sqlEstado
                    ORDER BY id_pedido_orden";
            $data = $this->BD->consultarFilas($sql, [$this->id_pedido]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function reporteOrdenesCompletadasFuxion($fi, $ff){ /*Porfecha*/
        try{

            $sqlIDPedido  = " true ";
            $sqlEstado = " true "; 
            $params = [];
            $id_pedido = $this->id_pedido;       

            if (!($id_pedido == NULL || $id_pedido == "")){
                $sqlIDPedido = " p.id_pedido = :0";
                array_push($params, $id_pedido);
            } else {
                $sqlEstado = " p.fecha_ingreso >= :0 AND p.fecha_ingreso <= :1 ";
                array_push($params, $fi);
                array_push($params, $ff);
            }

            $sql = "SELECT
                    po.id_pedido_orden,
                    codigo_remito,
                    codigo_tracking,
                    codigo_exigo,
                    envoltura,
                    fecha_courier,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    pov.fecha_hora_registro as fecha_entrega,
                    (CASE po.estado_actual 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN (SELECT COALESCE(GROUP_CONCAT(m.descripcion),'MOTIVADO')
                                        FROM pedido_orden_visita_motivacion povm 
                                        LEFT JOIN motivacion m ON povm.id_motivacion = m.id_motivacion
                                        WHERE pov.id_pedido_orden_visita = povm.id_pedido_orden_visita)
                        ELSE 'GESTIONANDO' END) as status,
                    UPPER(pov.observaciones) as observaciones,
                    (CASE pov.es_receptor_destinatario 
                        WHEN '1' THEN 'TITULAR'
                        WHEN '0' THEN pov.nombres_receptor
                        ELSE '' END ) as receptor,
                    (CASE pov.es_receptor_destinatario
                        WHEN '0' THEN pov.numero_documento_receptor
                        ELSE po.numero_documento_destinatario END ) as numero_documento_receptor,
                    (SELECT CONCAT(estado_actual,'|',observaciones,'|', DATE_FORMAT(fecha_hora_registro, '%d-%m-%Y'))
                        FROM pedido_orden_visita _pov 
                        WHERE po.id_pedido_orden = _pov.id_pedido_orden AND _pov.numero_visita = 1 AND _pov.estado_mrcb) as visita_uno,
                    (SELECT CONCAT(estado_actual,'|',observaciones,'|', DATE_FORMAT(fecha_hora_registro, '%d-%m-%Y'))
                        FROM pedido_orden_visita _pov 
                        WHERE po.id_pedido_orden = _pov.id_pedido_orden AND _pov.numero_visita = 2 AND _pov.estado_mrcb) as visita_dos,
                    distrito,
                    provincia,
                    region,
                    pais, 
                    UPPER(referencia) as referencia,
                    correo_contacto,
                    telefono_contacto,
                    UPPER(contacto) as contacto,
                    documentos,
                    rotulo_courier,
                    volumen,
                    po.estado_actual
                    FROM pedido_orden po
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    WHERE po.estado_mrcb AND $sqlIDPedido AND $sqlEstado
                    ORDER BY id_pedido_orden";
            $data = $this->BD->consultarFilas($sql, $params);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function leerXIdListar(){/*interno*/
        try{

            $sql = "SELECT  cantidad, 
                            cantidad_noasignadas,
                            cantidad_gestionando, 
                            cantidad_entregadas, 
                            cantidad_motivadas
                            FROM pedido p
                            WHERE p.estado_mrcb AND p.id_pedido = :0";
            $data = $this->BD->consultarFila($sql, [$this->id_pedido]);

            if ($data == false){
                throw new Exception("ID Pedido no válido.");
            }

            $sql = "SELECT
                    po.id_pedido_orden,
                    COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                    COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                    id_pedido,
                    distrito as ciudad,
                    COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                    COALESCE(pov.observaciones,'') as observaciones,
                    codigo_remito,
                    fecha_courier,
                    numero_documento_destinatario,
                    UPPER(destinatario) as destinatario,
                    UPPER(direccion_uno) as direccion_uno,
                    UPPER(COALESCE(direccion_dos,'')) as direccion_dos,
                    referencia,
                    UPPER(CONCAT(distrito,' - ', provincia)) as distrito_provincia,
                    UPPER(region) as region,
                    numero_paquetes,
                    celular_contacto as celular,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'GESTIONANDO' 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN 'MOTIVADO' 
                        ELSE 'NO ASIGNADO' END) as estado,
                    (CASE po.estado_actual 
                        WHEN 'G' THEN 'info' 
                        WHEN 'E' THEN 'success'
                        WHEN 'M' THEN 'danger' 
                        ELSE 'dark' END) as estado_color,
                    po.estado_actual,
                    numero_visitas
                    FROM pedido_orden po
                    LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    WHERE po.estado_mrcb AND po.id_pedido = :0";
            $data["registros"] = $this->BD->consultarFilas($sql, [$this->id_pedido]);
            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    private function procesarExcelFuxion($data_de_filas_excel){
        $cantidad = count($data_de_filas_excel);
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

        $primera_fila = $data_de_filas_excel[0];
        $segunda_fila = $data_de_filas_excel[1];
        if (!($primera_fila[0] == NULL &&
                ($segunda_fila[0] != NULL && 
                $segunda_fila[$ultimaColumna] != NULL && 
                $segunda_fila[$ultimaColumna + 1] == NULL)) ){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $data_de_filas_excel[$i];
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

    private function procesarExcelLeonisa($data_de_filas_excel){
        $cantidad = count($data_de_filas_excel);
        $filaInicio = 1; /*0: CABECERA*/
        $ultimaColumna = 13; /*FECHA COURIER / EMBARQ*/

        $campos = [
            "id_pedido",
            "codigo_remito",
            "codigo_tracking",
            "numero_paquetes",
            "zona",
            "cierre",
            "rotulo_courier",
            "catalogo",
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
            "fecha_courier"
        ];

        /*
        array(25) {
          [0]=>
          string(9) "ZONA"
          [1]=>
          string(7) "CIERRE"
          [2]=>
          string(16) "TRANSPORTISTA"
          [3]=>
          string(18) "CATÁLOGO"
          [4]=>
          DNI
          [5]=>
          APELLIDOS y NOMBRES
          [6]=>
           TELEFONO 1 
          [7]=>
          TELEFONO 2 
          [8]=>
          string(30) CELULAR
          [9]=>
          string(22) DIRECCION
          [10]=>
          string(7) URBANIZACIÓN / BARRIO / ASOC.
          [11]=>
          string(7)  DISTRITO / CIUDAD
          [12]=>
          string(10)  DPTO. + PROVINCIA 
          [13]=>
          string(4) Fecha Embarque
        }
        */
        $primera_fila = $data_de_filas_excel[0];

        if ($cantidad <= $filaInicio){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }   

        if ($primera_fila[$ultimaColumna] != NULL &&  $primera_fila[$ultimaColumna + 1] == NULL){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $data_de_filas_excel[$i];
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
                }
                array_push($fila, $tmp_fila[$j]);
            }

            array_push($valores, $fila);
        }


        return ["formato_valido"=>true, "campos"=>$campos, "valores"=>$valores];
    }

    private function procesarExcelMinedu($data_de_filas_excel){
        $cantidad = count($data_de_filas_excel);
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

        $primera_fila = $data_de_filas_excel[$filaInicio - 1];

        if ($cantidad <= $filaInicio){
            return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }   

        if ($primera_fila[$ultimaColumna] != NULL &&  $primera_fila[$ultimaColumna + 1] == NULL){
           return ["formato_valido"=>false, "campos"=>[], "valores"=>[]];
        }

        $valores = [];
        for ($i=$filaInicio; $i < $cantidad; $i++) { 
            $tmp_fila = $data_de_filas_excel[$i];
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

    public function registrar($necesito_excel_formato = true){
        try
        {   
            $this->BD->beginTransaction();
            /*validar que no se después de hoy*/

            $campos_valores = [
                "dias_ruta"=>$this->dias_ruta,
                "dias_gestionando"=>$this->dias_gestionando,
                "fecha_ingreso"=>$this->fecha_ingreso,
                "id_cliente"=>$this->id_cliente,
                "id_usuario_registro"=>$this->id_usuario_registro,
                "tipo_pedido"=>$this->tipo_pedido,
                "hash_file"=> $necesito_excel_formato ? hash_file("md5",$this->archivo["archivo"]) : NULL
            ];
            
            $this->BD->insert("pedido", $campos_valores);
            $this->id_pedido = $this->BD->getLastID();

            $cantidad_ordenes = 1;
            
            if ($necesito_excel_formato){
                //require_once "../phpexcel/PHPExcel.php";
                
                $partes_ruta = pathinfo($this->archivo["nombre"]);
                $extension = $partes_ruta["extension"];
                $objReader = IOFactory::createReader($extension == "xls" ? "Xls" : 'Xlsx');
                //$objReader->setReadDataOnly(true);
                $objPHPExcel = $objReader->load($this->archivo["archivo"]);
                
                if ($this->id_cliente == GlobalVariables::$ID_LEONISA){
                    if ($this->tipo_pedido == "1"){
                        $objWorksheet = $objPHPExcel->getSheet(1);
                    } else {
                        $objWorksheet = $objPHPExcel->getSheet(1);
                    }
                    
                } else {
                    $objWorksheet = $objPHPExcel->getActiveSheet();
                }

                
                $highestRow = $objWorksheet->getHighestRow();
                $highestColumn = $objWorksheet->getHighestColumn();
                $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);
                $data_de_filas_excel = [];
                for ($row = 1; $row <= $highestRow; ++$row) {
                    $rowObject = [];
                    for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                        $celda = $objWorksheet->getCellByColumnAndRow($col, $row);
                        $valor = $celda->getValue();
                        array_push($rowObject, $valor);
                    }
                    if ($rowObject[0] != NULL){
                        array_push($data_de_filas_excel, $rowObject);
                    }
                }

                $celda = null;
                $valor = null;
                require_once 'ProcesarExcel.clase.php';
                
                $objProcesar = new ProcesarExcel;
                $objProcesar->data_de_filas_excel = $data_de_filas_excel;
                $objProcesar->id_pedido =  $this->id_pedido;

                switch($this->id_cliente){
                    case GlobalVariables::$ID_FUXION_SAC:
                    //$campos_valores_registro = $this->procesarExcelFuxion($data_de_filas_excel);
                    $campos_valores_registro = $objProcesar->fuxion();
                    break;
                    case GlobalVariables::$ID_LEONISA:
                        if ($this->tipo_pedido == "1"){
                            $campos_valores_registro = $objProcesar->leonisa();        
                        } else {
                            $campos_valores_registro = $objProcesar->leonisa_catalogos();        
                        }
                    
                    //$campos_valores_registro = $this->procesarExcelLeonisa($data_de_filas_excel);
                    break;
                    case GlobalVariables::$ID_MINEDU:
                    $campos_valores_registro = $objProcesar->minedu();
                    //$campos_valores_registro = $this->procesarExcelMinedu($data_de_filas_excel);
                    break;
                    case GlobalVariables::$ID_PRONABEC:
                    $campos_valores_registro = $objProcesar->pronabec();
                    //$campos_valores_registro = $this->procesarExcelMinedu($data_de_filas_excel);
                    break;
                    case GlobalVariables::$ID_PRONIED:
                    $campos_valores_registro = $objProcesar->pronied();
                    break;
                    default:
                    $campos_valores_registro = ["valores"=>[], "formato_valido"=>false];
                    break;
                }

                if (!$campos_valores_registro["formato_valido"]){
                      throw new Exception("Formato no reconocible para el cliente seleccionado.");
                }

                $cantidad_ordenes = count($campos_valores_registro["valores"]);

                if ( $cantidad_ordenes <= 0){
                    throw new Exception("No hay registros de órdenes por registrar.");
                }

                $this->BD->insertMultiple("pedido_orden", $campos_valores_registro["campos"], $campos_valores_registro["valores"]);     
            } else {

            }
            
            
            $objPHPExcel->disconnectWorksheets();
            //$objPHPExcel->garbageCollect();
            unset($objPHPExcel);

            $this->BD->update("pedido", ["cantidad"=>$cantidad_ordenes, "cantidad_noasignadas"=>$cantidad_ordenes],
                                        ["id_pedido"=>$this->id_pedido]);

            $this->BD->commit();
            return ["msj"=>"Registrado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function eliminar(){
        try
        {   
            $campos_valores = [
                "estado_mrcb"=>"0"
            ];

            $campos_valores_where = [
                "id_pedido"=>$this->id_pedido
            ];

            $this->BD->update("pedido", $campos_valores, $campos_valores_where);
            return ["msj"=>"Eliminado correctamente."];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    /*LEONISA*/
    public function obtenerPedidosOrdenParaEtiqueta(){
        try
        {   
            $params = [$this->id_pedido];

            $sql = "SELECT razon_social FROM pedido p INNER JOIN usuario u ON u.id_usuario = p.id_cliente WHERE p.id_pedido = :0";
            $razon_social = $this->BD->consultarValor($sql, $params);

            $sql = "SELECT
                    codigo_remito,
                    zona,
                    catalogo as campaña,
                    destinatario,
                    CONCAT(direccion_uno,', ',referencia) as direccion,
                    telefono_uno_destinatario as telefono_uno,
                    telefono_dos_destinatario as telefono_dos,
                    celular_destinatario as celular,
                    distrito,
                    provincia,
                    region
                    FROM pedido_orden po
                    WHERE po.estado_mrcb AND id_pedido = :0
                    ORDER BY id_pedido_orden";
            $registros = $this->BD->consultarFilas($sql, $params);

            return ["razon_social"=>$razon_social, "registros"=>$registros];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    private function _getSQLListaPedidosOrdenesXIdAdmin($id_cliente_especifico = NULL){
        if ($id_cliente_especifico != NULL){
                switch ($id_cliente_especifico) {
                    case GlobalVariables::$ID_FUXION_SAC:
                        $sqlPedidoOrden = "SELECT
                                po.id_pedido_orden,
                                COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                                COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                                id_pedido,
                                COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                                COALESCE(pov.observaciones,'') as observaciones,
                                codigo_remito,
                                fecha_courier,
                                numero_documento_destinatario,
                                UPPER(destinatario) as destinatario,
                                UPPER(direccion_uno) as direccion_uno,
                                UPPER(COALESCE(direccion_dos,'')) as direccion_dos,
                                referencia,
                                UPPER(CONCAT(distrito,' - ', provincia)) as distrito_provincia,
                                UPPER(region) as region,
                                numero_paquetes,
                                celular_contacto as celular,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'GESTIONANDO' 
                                    WHEN 'E' THEN 'ENTREGADO'
                                    WHEN 'M' THEN 'MOTIVADO' 
                                    ELSE 'NO ASIGNADO' END) as estado,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'info' 
                                    WHEN 'E' THEN 'success'
                                    WHEN 'M' THEN 'danger' 
                                    ELSE 'dark' END) as estado_color,
                                po.estado_actual,
                                numero_visitas
                                FROM pedido_orden po
                                LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                                LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                                WHERE po.estado_mrcb AND po.id_pedido = :0"; 
                            break;
                    case GlobalVariables::$ID_LEONISA:
                        $sqlPedidoOrden = "SELECT
                                po.id_pedido_orden,
                                COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                                COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                                id_pedido,
                                COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                                COALESCE(pov.observaciones,'') as observaciones,
                                codigo_remito,
                                fecha_courier,
                                numero_documento_destinatario,
                                UPPER(destinatario) as destinatario,
                                UPPER(direccion_uno) as direccion_uno,
                                referencia,
                                UPPER(CONCAT(distrito,' - ', provincia)) as distrito_provincia,
                                UPPER(region) as region,
                                numero_paquetes,
                                celular_destinatario as celular,
                                telefono_uno_destinatario as telefono,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'GESTIONANDO' 
                                    WHEN 'E' THEN 'ENTREGADO'
                                    WHEN 'M' THEN 'MOTIVADO' 
                                    ELSE 'NO ASIGNADO' END) as estado,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'info' 
                                    WHEN 'E' THEN 'success'
                                    WHEN 'M' THEN 'danger' 
                                    ELSE 'dark' END) as estado_color,
                                po.estado_actual,
                                numero_visitas
                                FROM pedido_orden po
                                LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                                LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                                WHERE po.estado_mrcb AND po.id_pedido = :0"; 
                        break;
                     case GlobalVariables::$ID_MINEDU:
                        $sqlPedidoOrden = "SELECT
                                po.id_pedido_orden,
                                COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                                COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                                id_pedido,
                                COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                                COALESCE(pov.observaciones,'') as observaciones,
                                codigo_remito,
                                minedu_tipo_documento,
                                minedu_nro_doc,
                                UPPER(destinatario) as destinatario,
                                UPPER(direccion_uno) as direccion_uno,
                                UPPER(CONCAT(region,'-',distrito,' - ', provincia)) as ubigeo,
                                minedu_cobertura,
                                minedu_prioridad,
                                minedu_oficina,
                                numero_paquetes,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'GESTIONANDO' 
                                    WHEN 'E' THEN 'ENTREGADO'
                                    WHEN 'M' THEN 'MOTIVADO' 
                                    ELSE 'NO ASIGNADO' END) as estado,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'info' 
                                    WHEN 'E' THEN 'success'
                                    WHEN 'M' THEN 'danger' 
                                    ELSE 'dark' END) as estado_color,
                                po.estado_actual,
                                numero_visitas
                                FROM pedido_orden po
                                LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                                LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                                WHERE po.estado_mrcb AND po.id_pedido = :0"; 
                        break;
                     case GlobalVariables::$ID_PRONABEC:
                        $sqlPedidoOrden = "SELECT
                                po.id_pedido_orden,
                                COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                                COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                                id_pedido,
                                COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                                COALESCE(pov.observaciones,'') as observaciones,
                                codigo_remito,
                                pronabec_sigedo,
                                pronabec_oficina,
                                pronabec_orden,
                                pronabec_correlativo,
                                UPPER(destinatario) as destinatario,
                                UPPER(direccion_uno) as direccion_uno,
                                UPPER(CONCAT(region,'-',distrito,' - ', provincia)) as ubigeo,
                                numero_paquetes,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'GESTIONANDO' 
                                    WHEN 'E' THEN 'ENTREGADO'
                                    WHEN 'M' THEN 'MOTIVADO' 
                                    ELSE 'NO ASIGNADO' END) as estado,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'info' 
                                    WHEN 'E' THEN 'success'
                                    WHEN 'M' THEN 'danger' 
                                    ELSE 'dark' END) as estado_color,
                                po.estado_actual,
                                numero_visitas
                                FROM pedido_orden po
                                LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                                LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                                WHERE po.estado_mrcb AND po.id_pedido = :0"; 
                        break;
                    case GlobalVariables::$ID_PRONIED:
                        $sqlPedidoOrden = "SELECT
                                po.id_pedido_orden,
                                COALESCE(id_usuario_asignado, '') as id_usuario_asignado,
                                COALESCE(CONCAT(u.nombres,' ',u.apellidos),'') as colaborador_asignado,
                                id_pedido,
                                COALESCE(pov.fecha_hora_registro,'') as fecha_hora_atencion,
                                COALESCE(pov.observaciones,'') as observaciones,
                                codigo_remito,
                                forma_envio,
                                ticket_factura,
                                costo_envio,
                                pronied_unidad_organica,
                                UPPER(destinatario) as destinatario,
                                UPPER(direccion_uno) as direccion_uno,
                                UPPER(CONCAT(region,'-',distrito,' - ', provincia)) as ubigeo,
                                numero_paquetes,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'GESTIONANDO' 
                                    WHEN 'E' THEN 'ENTREGADO'
                                    WHEN 'M' THEN 'MOTIVADO' 
                                    ELSE 'NO ASIGNADO' END) as estado,
                                (CASE po.estado_actual 
                                    WHEN 'G' THEN 'info' 
                                    WHEN 'E' THEN 'success'
                                    WHEN 'M' THEN 'danger' 
                                    ELSE 'dark' END) as estado_color,
                                po.estado_actual,
                                numero_visitas
                                FROM pedido_orden po
                                LEFT JOIN usuario u ON u.id_usuario = po.id_usuario_asignado 
                                LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                                WHERE po.estado_mrcb AND po.id_pedido = :0"; 
                        break;
                }

                return $sqlPedidoOrden;
            }

            $sqlPedidoOrden = "";
            return $sqlPedidoOrden;
    }
    
    public function reporteOrdenesCompletadasLeonisa($fi, $ff){ /*Porfecha*/
        try{

            $sqlIDPedido  = " true ";
            $sqlEstado = " true "; 
            $params = [];
            $id_pedido = $this->id_pedido;       

            if (!($id_pedido == NULL || $id_pedido == "")){
                $sqlIDPedido = " p.id_pedido = :0";
                array_push($params, $id_pedido);
            } else {
                $sqlEstado = " p.fecha_ingreso >= :0 AND p.fecha_ingreso <= :1 ";
                array_push($params, $fi);
                array_push($params, $ff);
            }

            $sql = "SELECT
                    campana,
                    codigo_remito as numero_orden,
                    codigo_tracking as guia,
                    numero_documento_destinatario as cedula,
                    UPPER(destinatario) as destinatario,
                    DATE(fecha_hora_asignado) as fecha_embarque,
                    TIME(fecha_hora_asignado) as  hora_embarque,
                    (CASE po.estado_actual 
                        WHEN 'E' THEN 'ENTREGADO'
                        WHEN 'M' THEN (SELECT COALESCE(GROUP_CONCAT(m.descripcion),'MOTIVADO')
                                        FROM pedido_orden_visita_motivacion povm 
                                        LEFT JOIN motivacion m ON povm.id_motivacion = m.id_motivacion
                                        WHERE pov.id_pedido_orden_visita = povm.id_pedido_orden_visita)
                        ELSE 'GESTIONANDO' END) as status,
                    UPPER(pov.observaciones) as observaciones,
                    telefono_uno_destinatario,
                    telefono_dos_destinatario,
                    zona,
                    COALESCE(region, provincia, referencia) as departamento,
                    distrito as ciudad,
                    UPPER(direccion_uno) as direccion_uno,
                    '0' as numero_guia_mas,
                    'N' as entrega_porteria,
                    '0.00' as valor,
                    DATE(pov.fecha_hora_registro) as fecha_entrega,
                    TIME(pov.fecha_hora_registro) as hora_entrega,
                    pov.fecha_hora_registro as fecha_entrega
                    FROM pedido_orden po
                    LEFT JOIN pedido_orden_visita pov ON pov.id_pedido_orden = po.id_pedido_orden AND pov.ultima_visita
                    LEFT JOIN pedido p ON p.id_pedido = po.id_pedido
                    WHERE po.estado_mrcb AND $sqlIDPedido AND $sqlEstado
                    ORDER BY po.id_pedido_orden";
            $data = $this->BD->consultarFilas($sql, $params);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

}