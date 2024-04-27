<?php 

$time_start = microtime(true); 
echo "Subiendo imagen\n";

$MAX_SIZE = 10;
$i = 0;
$imagenes = [];
$imagenes_invalidas = 0;

//var_dump($_FILES); exit;

try {
	foreach ($_FILES as $key => $value) {

		echo "Procesando imagen ".$i."\n";
		switch ($value["type"]){
		    case image_type_to_mime_type(IMAGETYPE_GIF):
		    case image_type_to_mime_type(IMAGETYPE_JPEG):
		    case image_type_to_mime_type(IMAGETYPE_PNG):
		    case image_type_to_mime_type(IMAGETYPE_BMP):
		    break;
		    default:
		    $imagenes_invalidas++;
		    break;
		}

		if ($imagenes_invalidas > 0){
		    throw new Exception("No se puede procesar la imagen ".$i.". Seleccione un formato válido; jpg, png, bmp o gif.");
		}

		if ($value["size"] > $MAX_SIZE * 1024 * 1024){ /*Nax 2.5MB*/
		    throw new Exception("No se puede procesar la imagen ".$i." El tamaño máximo por foto es de 5MB");
		}
		
		var_dump($value["size"]);

		array_push($imagenes, 
		        [
		            "nombre"=>$value["name"],
		            "tipo"=>$value["type"],
		            "tamano"=>$value["size"],
		            "archivo"=>$value["tmp_name"]
		        ]
		    );

		echo "Imagen agregada\n";
	}	


	if (count($imagenes) > 0){
        $numero_imagen = 1;
        foreach ($imagenes as $key => $value) {
            $url_img = "_".$numero_imagen."_".md5($value["nombre"]);
            $nombre_imagen_original = $value["nombre"];
            $tamano = $value["tamano"];
            $tipo_imagen = $value["tipo"];
            echo "Comenzando upload...\n";
          
            if (!move_uploaded_file($value["archivo"], "../img/imagenes_test/$url_img")) {
                throw new Exception("Error al subir la imagen ".$numero_imagen.".");
            }
            echo "Finalizar upload\n";
            $numero_imagen++;
        }
    }


} catch (Exception $e) {
	var_dump("error! ".$e->getMessage());
}

$time_end = microtime(true);
//dividing with 60 will give the execution time in minutes otherwise seconds
$execution_time = ($time_end - $time_start)/60;

var_dump("tiempo de ejecución ".$execution_time);