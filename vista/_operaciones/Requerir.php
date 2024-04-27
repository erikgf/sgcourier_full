<?php 

class Requerir{
	public static function CSS($arCss){
		$modo_produccion = MODO_PRODUCCION == "1";
		$css = "";

		foreach ($arCss as $key => $value) {
			switch ($value) {
				case 'googlefonts':
					if ($modo_produccion){
						$css .= '<link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic" rel="stylesheet" type="text/css">';
					}
					break;
				case 'bootstrap':
					$css .= $modo_produccion 
								? '<link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet" />' 
								: '<link rel="stylesheet" href="../../bower_components/bootstrap/dist/css/bootstrap.min.css">';
					break;
				case 'font-awesome':
					$css .=  $modo_produccion ? 
								'<link rel="stylesheet" href="../../bower_components/font-awesome/css/font-awesome.min.css">' : 
								'<link rel="stylesheet" href="../../bower_components/font-awesome/css/font-awesome.min.css">';
					break;
				case 'ionicons':
					$css .=  $modo_produccion ? 
								'<link rel="stylesheet" href="../../bower_components/Ionicons/css/ionicons.min.css' : 
								'<link rel="stylesheet" href="../../bower_components/Ionicons/css/ionicons.min.css">';
					break;
				case 'adminLTE':
					if ($modo_produccion){
						$css .= '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.0/animate.min.css">';
						$css .= '<link rel="stylesheet" href="../../dist/css/AdminLTE.min.css">';
						$css .= '<link rel="stylesheet" href="../../dist/css/skins/_all-skins.min.css">';
					}
					break;
				case 'bootstrap-select':
					$css .=  $modo_produccion ? '<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.10.0/css/bootstrap-select.min.css">' : 
												'<link href="../../plugins/bootstrap-select/css/bootstrap-select.css" rel="stylesheet" />';
					break;
				case 'select2':
					if ($modo_produccion){
						$css .= '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/css/select2.min.css" integrity="sha512-0Cvewd1F2EKKK6qUd9DD/gDo0Y5JqMoDCXms6pIip+Q4sRNPKc16MdlZEPLPAIfzV450aPlKsOuFQjOZ34GzxQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />';
					} else{
						$css .= '<link  type="text/css" rel="stylesheet" href="../../libs/select2/dist/css/select2.min.css">';
					}
					break;
				case 'datatables':
					$css .= '<link type="text/css" href="../../libs/datatables.net-bs4/css/dataTables.bootstrap4.css" rel="stylesheet">';
					$css .=  $modo_produccion 
								? '<link type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" rel="stylesheet">' 
								: '<link href="../../libs/datatables/responsive/responsive.dataTables.min.css" rel="stylesheet" />';

					
					break;
				default:
					break;
			}
		}
		
		$css .= '<link type="text/css" href="../../css/style.min.css" rel="stylesheet">';

	    return $css;
	}

	public static function JS($arJs){
		$modo_produccion = MODO_PRODUCCION == "1";
		$js = "";

		foreach ($arJs as $key => $value) {
			switch ($value) {
				case 'jquery':
					$js .=  $modo_produccion ? 
							'<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>' :
							'<script type="text/javascript" src="../../libs/jquery/dist/jquery.min.js"></script>';
					break;
				case "perfect-scrollbar":
					$js .=  $modo_produccion ? 
							'<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.perfect-scrollbar/0.6.10/js/min/perfect-scrollbar.jquery.min.js" integrity="sha512-ebNY0qErbAT1m/mtiUXFcDVRcG30XEKR/Qf6fiMY6U7MRFX65rzscgev7iaKIJJGbzLpRhZjq/CfglRckLHN7Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>' :
							'<script type="text/javascript" src="../../libs/perfect-scrollbar/dist/perfect-scrollbar.jquery.min.js"></script>';
					break;
				case 'handlebars':
					$js .=  $modo_produccion ? 
							'<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/handlebars.js/4.7.7/handlebars.min.js"></script>' :
							'<script type="text/javascript" src="../../libs/handlebars/handlebars.min.js"></script>';
					break;
				case 'bootstrap':
					$js .=  $modo_produccion ? 
							'<script type="text/javascript" src="https://cdn.usebootstrap.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>' :
							'<script type="text/javascript" src="../../libs/bootstrap/dist/js/bootstrap.min.js"></script>';
					break;
				case 'datatables':
					$js .=  $modo_produccion 
							? ' <script type="text/javascript" src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
								<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>' 
							: '	<script type="text/javascript" src="../../libs/datatables/datatables.min.js"></script>
							  	<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>';

					break;
				case "select2":
					$js.=  $modo_produccion ?  
							'<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.1/js/select2.min.js" integrity="sha512-FT5Xew0eBNlexy+I83S/WzabRD7WsJAOTFuTF8qNKZ4KtPU9Av5a7b0RxJge3IKdMtwDvHFyVKjzaUCIGwj9ug==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>' :
							'<script type="text/javascript" src="../../libs/select2/dist/js/select2.min.js"></script>';
					break;
				case "popper": 
					$js .=  $modo_produccion ? 
							'<script src="../../libs/popper.js/dist/umd/popper.min.js"></script>' : 
							'<script src="../../libs/popper.js/dist/umd/popper.min.js"></script>';
					break;
				case "sparkline":
					$js .=  $modo_produccion ? 
							'<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sparklines/2.1.2/jquery.sparkline.min.js" integrity="sha512-3PRVLmoBYuBDbCEojg5qdmd9UhkPiyoczSFYjnLhFb2KAFsWWEMlAPt0olX1Nv7zGhDfhGEVkXsu51a55nlYmw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>' : 
							'<script src="../../libs/sparkline/sparkline.js"></script>';
					break;
				case "app":
					$js .= '<script type="text/javascript" src="../../js/waves.js"></script>
						    <script type="text/javascript" src="../../js/sidebarmenu.js"></script>
						    <script type="text/javascript" src="../../js/custom.min.js"></script>';
					break;
				case "charts":
					$js .=  $modo_produccion 
							? 
							'<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.min.js" integrity="sha512-eO1AKNIv7KSFl5n81oHCKnYLMi8UV4wWD1TcLYKNTssoECDuiGhoRsQkdiZkl8VUjoms2SeJY7zTSw5noGSqbQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
							<script src="https://cdnjs.cloudflare.com/ajax/libs/flot/0.8.3/jquery.flot.pie.min.js" integrity="sha512-jMP1biHEi+eAK+dGbOLAmabdBzVTUjHpryY1vsILFGYatR5i55+ZuXZXBOdbz/KzvTstnsu6+TJCTZ79/PMjbA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> '
							: 
							'<script type="text/javascript" src="../../libs/flot/jquery.flot.js"></script>
							<script type="text/javascript" src="../../libs/chart/jquery.flot.pie.min.js"></script>';
					break;
				case "vue":
					$js .=  '<script type="text/javascript" src="../../libs/vue.js"></script>';
					break;
				default:
					break;
			}
		}

		$js .= '<script type="text/javascript" src="../../js/util.js"></script>';
	    return $js;
	}
}