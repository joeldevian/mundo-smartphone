<?php 
class GestarBD {
	private $conect;  
	private $base_datos;
	private $servidor;
	private $usuario;
	private $pass;
	private $consulta; // Definición de la propiedad $consulta

	function __construct() {
		include 'config.php';
		$this->servidor = $config['servidor'];
		$this->usuario = $config['usuario'];
		$this->pass = $config['pass']; // Aquí debes colocar tu contraseña
		$this->base_datos = $config['basedatos'];
		$this->conectar_base_datos();
	}

	private function conectar_base_datos() {
		// Verificar la conexión a la base de datos
		if (!$this->conect = mysqli_connect($this->servidor, $this->usuario, $this->pass, $this->base_datos)) {
			echo "Error al conectar: " . mysqli_connect_error(); // Muestra el error de conexión
			exit();
		}
		mysqli_set_charset($this->conect, 'utf8'); // Orden de argumentos correcto
	}

	public function consulta($consulta) {
		// Asegúrate de usar $this->conect
		$this->consulta = mysqli_query($this->conect, $consulta);
		if (!$this->consulta) {
			echo "Error en la consulta: " . mysqli_error($this->conect);
			return false;
		}
	}

	public function mostrar_registros() {
		// Cambié MYSQL_ASSOC a MYSQLI_ASSOC
		if ($row = mysqli_fetch_array($this->consulta, MYSQLI_ASSOC)) {
			return $row;
		} else {
			return false;
		}
	}

	public function mostrar_row() {
		if ($maxrow = mysqli_fetch_row($this->consulta)) {
			$idmaxrow = $maxrow[0];
			return $idmaxrow;
		} else {
			return false;
		}
	}

	public function numeroFilas() {
		if ($fila = mysqli_num_rows($this->consulta)) {
			$num_rows = $fila;
			return $num_rows;
		} else {
			return false;
		}
	}

	public function numero_campos() {
		if ($campos = mysqli_num_fields($this->consulta)) {
			return $campos;
		} else {
			return false;
		}
	}

	public function SelectText($campos, $tabla, $where, $order, $datoOrder, $tipoOrder) {
		$select = "SELECT $campos FROM $tabla ";
		if ($where) {
			$select .= "WHERE $where ";
		}
		if ($order) {
			$select .= "ORDER BY $datoOrder $tipoOrder ";
		}		
		return $select;
	}

	public function InsertText($tabla, $campos, $datos) {
		$insert = "INSERT INTO $tabla ($campos) VALUES ($datos)";
		return $insert;
	}

	public function ActualizarText($tabla, $arraydatos, $where) {
		$update = "UPDATE $tabla SET ";
		foreach ($arraydatos as $key => $value) {
			$update .= "$key = $value, ";
		}
		$update = rtrim($update, ', '); // Eliminar la última coma
		$update .= " WHERE $where";
		return $update;
	}

	public function EliminarText($tabla, $where) {
		$delete = "DELETE FROM $tabla WHERE $where";
		return $delete;
	}

	public function INNER_JOIN3T($datos, $tabla1, $tabla2, $datosT2, $tabla3, $datosT3, $where) {
		$inner_join = "SELECT $datos FROM $tabla1 INNER JOIN $tabla2 ON $datosT2 ";
		if ($tabla3 && $datosT3) {
			$inner_join .= " INNER JOIN $tabla3 ON $datosT3 ";
		}
		if ($where) {
			$inner_join .= " WHERE $where ";
		}
		return $inner_join;
	}

	public function INNER_JOIN($datos, $from, $arrayTablas, $where) {
		$inner_join = "SELECT $datos FROM $from ";
		foreach ($arrayTablas as $tabla => $relacion) {
			$inner_join .= " INNER JOIN $tabla ON $relacion ";
		}
		if ($where) {
			$inner_join .= " WHERE $where ";
		}
		return $inner_join;
	}
}

