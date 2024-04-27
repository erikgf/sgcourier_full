<?php 

require_once 'Modelo.clase.php';

class BackupImagen extends Modelo{
    private $MAX_ARCHIVOS_ZIP = 50;

	public function __construct($BD = null){
		try {
			parent::__construct("backup_imagen", $BD);	
		} catch (Exception $e) {
			throw new Exception($e->getMessage());
		}
	}

	public function obtenerImagenesNombreArchivos($numero_dia, $numero_mes, $anio){
        try{
            $sql = "SELECT povi.url_img
                        FROM pedido_orden_visita_imagen povi
                        INNER JOIN pedido_orden_visita pov ON pov.id_pedido_orden_visita = povi.id_pedido_orden_visita
                        INNER JOIN pedido_orden po ON po.id_pedido_orden = pov.id_pedido_orden
                        INNER JOIN pedido p ON p.id_pedido = po.id_pedido
                        WHERE year(p.fecha_ingreso) = :1 AND MONTH(p.fecha_ingreso) = :0 AND DAY(p.fecha_ingreso) ".($numero_dia > 1 ? "<= 15" : ">15");
            $data = $this->BD->consultarFilas($sql, [$numero_mes, $anio]);

            return $data;
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }
    
    public function eliminarImagenes($numero_dia, $numero_mes, $anio){
        try{
            $carpeta_buscar = "../img/imagenes_visitas/";
            $obtener_archivos = $this->obtenerImagenesNombreArchivos($numero_dia, $numero_mes, $anio);
            
            $imagenes_totales = count($obtener_archivos);
            $imagenes_cargadas = 0;
            
            for ($j=0; $j < $imagenes_totales ; $j++) { 
                $archivo = $obtener_archivos[$j];
                if (unlink($carpeta_buscar.$archivo["url_img"])){
                    $imagenes_cargadas++;
                }
            }
            
            return ["rpt"=>"Realizado correctamente", "eliminadas"=>$imagenes_cargadas, "totales"=>$imagenes_totales];
            
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

    public function generarZipImagenesPorMesAnio($numero_dia, $numero_mes, $anio){
        try{

            $carpeta_buscar = "../img/imagenes_visitas/";
            $nombre_archivo = "BACKUPIMG_".$numero_dia."_".$numero_mes."_".$anio;
            $obtener_archivos = $this->obtenerImagenesNombreArchivos($numero_dia, $numero_mes, $anio);
            
            $imagenes_cargadas =0;
            $imagenes_totales = count($obtener_archivos);

            $zip = new ZipArchive;
            if ($zip->open("../img/backups/".$nombre_archivo.'.zip', ZipArchive::CREATE) === TRUE)
            {   
                for ($j=0; $j < $imagenes_totales ; $j++) { 
                    $archivo = $obtener_archivos[$j];
                    if ($zip->addFile($carpeta_buscar.$archivo["url_img"])){
                        $imagenes_cargadas++;
                    }
                }
                
                $zip->close();
            }
            return ["rpt"=>"Realizado correctamente",  "cargadas"=>$imagenes_cargadas, "totales"=>$imagenes_totales,"nombre_archivo"=>$nombre_archivo];
        } catch (Exception $exc) {
            throw new Exception($exc->getMessage());
        }
    }

}


