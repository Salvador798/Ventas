<?php 

    namespace app\controllers;
    use app\models\mainModel;

    class productController extends mainModel{

        /*----------  Controlador registrar producto  ----------*/
		public function registrarProductoControlador(){

			# Almacenando datos#
		    $codigo=$this->limpiarCadena($_POST['producto_codigo']);
		    $nombre=$this->limpiarCadena($_POST['producto_nombre']);

		    $precio_compra=$this->limpiarCadena($_POST['producto_precio_compra']);
		    $precio_venta=$this->limpiarCadena($_POST['producto_precio_venta']);
		    $stock=$this->limpiarCadena($_POST['producto_stock']);

		    $marca=$this->limpiarCadena($_POST['producto_marca']);
		    $modelo=$this->limpiarCadena($_POST['producto_modelo']);
		    $unidad=$this->limpiarCadena($_POST['producto_unidad']);
		    $categoria=$this->limpiarCadena($_POST['producto_categoria']);

		    # Verificando campos obligatorios #
            if($codigo=="" || $nombre=="" || $precio_compra=="" || $precio_venta=="" || $stock==""){
            	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No has llenado todos los campos que son obligatorios",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            # Verificando integridad de los datos #
		    if($this->verificarDatos("[a-zA-Z0-9- ]{1,77}",$codigo)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El CODIGO no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,$#\-\/ ]{1,100}",$nombre)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El NOMBRE no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($this->verificarDatos("[0-9.]{1,25}",$precio_compra)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El PRECIO DE COMPRA no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($this->verificarDatos("[0-9.]{1,25}",$precio_venta)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El PRECIO DE VENTA no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($this->verificarDatos("[0-9]{1,22}",$stock)){
		    	$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El STOCK O EXISTENCIAS no coincide con el formato solicitado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    if($marca!=""){
		    	if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}",$marca)){
			    	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La MARCA no coincide con el formato solicitado",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
			    }
		    }

		    if($modelo!=""){
		    	if($this->verificarDatos("[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ().,#\- ]{1,30}",$modelo)){
			    	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"El MODELO no coincide con el formato solicitado",
						"icono"=>"error"
					];
					return json_encode($alerta);
			        exit();
			    }
		    }

		    # Comprobando presentacion del producto #
			if(!in_array($unidad, PRODUCTO_UNIDAD)){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La PRESENTACION DEL PRODUCTO no es correcta o no la ha seleccionado",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			# Verificando categoria #
		    $check_categoria=$this->ejecutarConsulta("SELECT categoria_id FROM categoria WHERE categoria_id='$categoria'");
		    if($check_categoria->rowCount()<=0){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"La categoría seleccionada no existe en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    # Verificando stock total o existencias #
            if($stock<=0){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No puedes registrar un producto con stock o existencias en 0, debes de agregar al menos una unidad",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            # Comprobando precio de compra del producto #
            $precio_compra=number_format($precio_compra,MONEDA_DECIMALES,'.','');
            if($precio_compra<=0){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El PRECIO DE COMPRA no puede ser menor o igual a 0",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
            }

            # Comprobando precio de venta del producto #
            $precio_venta=number_format($precio_venta,MONEDA_DECIMALES,'.','');
            if($precio_venta<=0){
                $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El PRECIO DE VENTA no puede ser menor o igual a 0",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			# Comprobando precio de compra y venta del producto #
			if($precio_compra>$precio_venta){
				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El precio de compra del producto no puede ser mayor al precio de venta",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
			}

			# Comprobando codigo de producto #
		    $check_codigo=$this->ejecutarConsulta("SELECT producto_codigo FROM producto WHERE producto_codigo='$codigo'");
		    if($check_codigo->rowCount()>=1){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"El código de producto que ha ingresado ya se encuentra registrado en el sistema",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    # Comprobando nombre de producto #
		    $check_nombre=$this->ejecutarConsulta("SELECT producto_nombre FROM producto WHERE producto_codigo='$codigo' AND producto_nombre='$nombre'");
		    if($check_nombre->rowCount()>=1){
		        $alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"Ya existe un producto registrado con el mismo nombre y código de barras",
					"icono"=>"error"
				];
				return json_encode($alerta);
		        exit();
		    }

		    # Directorios de imagenes #
			$img_dir='../views/productos/';

			# Comprobar si se selecciono una imagen #
    		if($_FILES['producto_foto']['name']!="" && $_FILES['producto_foto']['size']>0){

    			# Creando directorio #
		        if(!file_exists($img_dir)){
		            if(!mkdir($img_dir,0777)){
		            	$alerta=[
							"tipo"=>"simple",
							"titulo"=>"Ocurrió un error inesperado",
							"texto"=>"Error al crear el directorio",
							"icono"=>"error"
						];
						return json_encode($alerta);
		                exit();
		            } 
		        }

		        # Verificando formato de imagenes #
		        if(mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/jpeg" && mime_content_type($_FILES['producto_foto']['tmp_name'])!="image/png"){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La imagen que ha seleccionado es de un formato no permitido",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

		        # Verificando peso de imagen #
		        if(($_FILES['producto_foto']['size']/1024)>5120){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"La imagen que ha seleccionado supera el peso permitido",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

		        # Nombre de la foto #
		        $foto=$codigo."_".rand(0,100);

		        # Extension de la imagen #
		        switch(mime_content_type($_FILES['producto_foto']['tmp_name'])){
		            case 'image/jpeg':
		                $foto=$foto.".jpg";
		            break;
		            case 'image/png':
		                $foto=$foto.".png";
		            break;
		        }

		        chmod($img_dir,0777);

		        # Moviendo imagen al directorio #
		        if(!move_uploaded_file($_FILES['producto_foto']['tmp_name'],$img_dir.$foto)){
		        	$alerta=[
						"tipo"=>"simple",
						"titulo"=>"Ocurrió un error inesperado",
						"texto"=>"No podemos subir la imagen al sistema en este momento",
						"icono"=>"error"
					];
					return json_encode($alerta);
		            exit();
		        }

    		}else{
    			$foto="";
    		}

    		$producto_datos_reg=[
				[
					"campo_nombre"=>"producto_codigo",
					"campo_marcador"=>":Codigo",
					"campo_valor"=>$codigo
				],
				[
					"campo_nombre"=>"producto_nombre",
					"campo_marcador"=>":Nombre",
					"campo_valor"=>$nombre
				],
				[
					"campo_nombre"=>"producto_stock_total",
					"campo_marcador"=>":Stock",
					"campo_valor"=>$stock
				],
				[
					"campo_nombre"=>"producto_tipo_unidad",
					"campo_marcador"=>":Unidad",
					"campo_valor"=>$unidad
				],
				[
					"campo_nombre"=>"producto_precio_compra",
					"campo_marcador"=>":PrecioCompra",
					"campo_valor"=>$precio_compra
				],
				[
					"campo_nombre"=>"producto_precio_venta",
					"campo_marcador"=>":PrecioVenta",
					"campo_valor"=>$precio_venta
				],
				[
					"campo_nombre"=>"producto_marca",
					"campo_marcador"=>":Marca",
					"campo_valor"=>$marca
				],
				[
					"campo_nombre"=>"producto_modelo",
					"campo_marcador"=>":Modelo",
					"campo_valor"=>$modelo
				],
				[
					"campo_nombre"=>"producto_estado",
					"campo_marcador"=>":Estado",
					"campo_valor"=>"Habilitado"
				],
				[
					"campo_nombre"=>"producto_foto",
					"campo_marcador"=>":Foto",
					"campo_valor"=>$foto
				],
				[
					"campo_nombre"=>"categoria_id",
					"campo_marcador"=>":Categoria",
					"campo_valor"=>$categoria
				]
			];

			$registrar_producto=$this->guardarDatos("producto",$producto_datos_reg);

			if($registrar_producto->rowCount()==1){
				$alerta=[
					"tipo"=>"limpiar",
					"titulo"=>"Producto registrado",
					"texto"=>"El producto ".$nombre." se registro con exito",
					"icono"=>"success"
				];
			}else{
				
				if(is_file($img_dir.$foto)){
		            chmod($img_dir.$foto,0777);
		            unlink($img_dir.$foto);
		        }

				$alerta=[
					"tipo"=>"simple",
					"titulo"=>"Ocurrió un error inesperado",
					"texto"=>"No se pudo registrar el producto, por favor intente nuevamente",
					"icono"=>"error"
				];
			}

			return json_encode($alerta);
		}


		/*----------  Controlador listar producto  ----------*/
		public function listarProductoControlador($pagina,$registros,$url,$busqueda,$categoria){

			$pagina=$this->limpiarCadena($pagina);
			$registros=$this->limpiarCadena($registros);
			$categoria=$this->limpiarCadena($categoria);

			$url=$this->limpiarCadena($url);
			if($categoria>0){
				$url=APP_URL.$url."/".$categoria."/";
			}else{
				$url=APP_URL.$url."/";
			}

			$busqueda=$this->limpiarCadena($busqueda);
			$tabla="";

			$pagina = (isset($pagina) && $pagina>0) ? (int) $pagina : 1;
			$inicio = ($pagina>0) ? (($pagina * $registros)-$registros) : 0;

			$campos="producto.producto_id,producto.producto_codigo,producto.producto_nombre,producto_stock_total,producto.producto_precio_venta,producto.producto_foto,categoria.categoria_nombre";

			if(isset($busqueda) && $busqueda!=""){

				$consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%' OR producto_marca LIKE '%$busqueda%' OR producto_modelo LIKE '%$busqueda%' ORDER BY producto_nombre ASC LIMIT $inicio,$registros";

				$consulta_total="SELECT COUNT(producto_id) FROM producto WHERE producto_codigo LIKE '%$busqueda%' OR producto_nombre LIKE '%$busqueda%' OR producto_marca LIKE '%$busqueda%' OR producto_modelo LIKE '%$busqueda%'";

			}elseif($categoria>0){

		        $consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id WHERE producto.categoria_id='$categoria' ORDER BY producto.producto_nombre ASC LIMIT $inicio,$registros";

		        $consulta_total="SELECT COUNT(producto_id) FROM producto WHERE categoria_id='$categoria'";

		    }else{

				$consulta_datos="SELECT $campos FROM producto INNER JOIN categoria ON producto.categoria_id=categoria.categoria_id ORDER BY producto_nombre ASC LIMIT $inicio,$registros";

				$consulta_total="SELECT COUNT(producto_id) FROM producto";

			}

			$datos = $this->ejecutarConsulta($consulta_datos);
			$datos = $datos->fetchAll();

			$total = $this->ejecutarConsulta($consulta_total);
			$total = (int) $total->fetchColumn();

			$numeroPaginas =ceil($total/$registros);

            $tabla.='
		        <div class="table-container">
		        <table class="table is-bordered is-striped is-narrow is-hoverable is-fullwidth">
		            <thead>
		                <tr>
		                    <th class="has-text-centered">#</th>
		                    <th class="has-text-centered">Codigo</th>
		                    <th class="has-text-centered">Nombre</th>
		                    <th class="has-text-centered">Precio de Vta</th>
		                    <th class="has-text-centered">Cantidad</th>
		                    <th class="has-text-centered">Categoria</th>
                            <th class="has-text-centered">Producto</th>
                            <th class="has-text-centered">Actualizar</th>
                            <th class="has-text-centered">Eliminar</th>
		                </tr>
		            </thead>
		            <tbody>
		    ';

		    if($total>=1 && $pagina<=$numeroPaginas){
				$contador=$inicio+1;
				$pag_inicio=$inicio+1;
				foreach($datos as $rows){
		            $tabla.='</p>
                        <tr class="has-text-centered">
		                    <td>'.$contador.'</td>
                            <td>'.$rows['producto_codigo'].'</td> 
                            <td>'.$rows['producto_nombre'].'</td>
		                    <td>$'.$rows['producto_precio_venta'].'</td>
		                    <td>'.$rows['producto_stock_total'].'</td>
		                    <td>'.$rows['categoria_nombre'].'</td>
                            <td>
		                        <a href="'.APP_URL.'productPhoto/'.$rows['producto_id'].'/" class="button is-info is-rounded is-small">
			                    	<i class="far fa-image fa-fw"></i>
			                    </a>
                            </td>
                            <td>
		                        <a href="'.APP_URL.'productUpdate/'.$rows['producto_id'].'/" class="button is-success is-rounded is-small">
		                        	<i class="fas fa-sync fa-fw"></i>
		                        </a>
                            </td>
                            <td>
		                        <form class="FormularioAjax is-inline-block" action="'.APP_URL.'app/ajax/productoAjax.php" method="POST" autocomplete="off" >

			                		<input type="hidden" name="modulo_producto" value="eliminar">
			                		<input type="hidden" name="producto_id" value="'.$rows['producto_id'].'">

			                    	<button type="submit" class="button is-danger is-rounded is-small">
			                    		<i class="far fa-trash-alt fa-fw"></i>
			                    	</button>
			                    </form>
		                    </td>
		                </tr>
		            ';
					$contador++;
				}
				$pag_final=$contador-1;
			}else{
				if($total>=1){
					$tabla.='
						<tr class="has-text-centered">
			                <td colspan="6">
			                    <a href="'.$url.'1/" class="button is-link is-rounded is-small mt-4 mb-4">
			                        Haga clic acá para recargar el listado
			                    </a>
			                </td>
                        </tr>
					';
				}else{
					$tabla.='
                        <tr class="has-text-centered">
						    <td colspan="6">
						        No hay productos registrados en esta categoría
                            </td>
                        </tr>
					';
				}
			}

            $tabla.='</tbody></table></div>';

			### Paginacion ###
			if($total>0 && $pagina<=$numeroPaginas){
				$tabla.='<p class="has-text-right">Mostrando productos <strong>'.$pag_inicio.'</strong> al <strong>'.$pag_final.'</strong> de un <strong>total de '.$total.'</strong></p>';

				$tabla.=$this->paginadorTablas($pagina,$numeroPaginas,$url,7);
			}

			return $tabla;
		}
    }