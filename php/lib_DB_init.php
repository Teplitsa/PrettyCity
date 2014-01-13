<?php

/*
	Для подключения модуля нужно где-нибудь в начале php-файла подключить его: include_once('lib_DB_init.php');
	Модуль сразу подключается к БД и предоставляет возможность использования следующих функций:
	
	----Функции для общения с БД----
	
	gefdb($q)  - Get Element From Database. Выполняет запрос $q и возвращает первый элемент первой строки результата запроса и "" в случае неудачи
	
	grfdb($q)  - Get Row From Data Base. Выполняет запрос $q и возвращает первую строку результата запроса $q и "" в случае неудачи
	
	gafdb($q)  - Get Array From Data Base. Выполняет запрос $q и возвращает результат запроса $q в виде массива и "" в случае неудачи
	
	geafdb($q) - Get Element Array From Data Base. Выполняет запрос $q и возвращает массив, содержащий только только по первому элементу из запрашиваемых строк
	
	-----Функции для получения служебной информации из БД-----
	
	get_tables() - Возвращает список таблиц в БД
	
	get_table_vars($table_name) - Возвращает список полей таблицы $table_name с указанием, обязательны они или нет: обязательные лежат в массиве 'req', необязательные в 'opt'
	
	----Сопровождающие функции----
	
	get_insert_string($arr) - Из ассоциативного массива $arr составляет строку типа "($k1, $k2, ..) VALUES ('$v1', '$v2', ..)", которую удобно использовать для SQL-запроса INSERT
	
	get_update_string($arr) - Из ассоциативного массива $arr составляет строку  типа "$k1='$v1', $k2='$v2', ..", которую удобно использовать для SQL-запроса UPDATE
 
	isParamsDefs($fact, $req_vars, $opt_vars = array()) - Проверяет наличие обязательных и опциональных параметров в ассоциативном массиве $fact. Возвращает TRUE, если в $fact определены все ненулевые переменные с именами, перечисленными в $req_vars, и некоторые из $opt_vars; иначе - FALSE
	
	__ПРИМЕЧАНИЯ__
	
	Все запросы перед выполнением проходят через функцию my_sql_query($q). Сейчас она пуста, но ее можно будет использовать для настройки безопасности или логгирования всех запросов к базе
*/



//--------------Инициализация работы с БД----------------------//



/*
 * В Auth_data.php должны быть определены параметры:
 *
 * DB (имя БД),
 * db_ipaddr (ip или url БД),
 * db_username (имя пользователя для доступа к БД),
 * db_userpass (пароль для доступа к БД)
 */
    include_once("Auth_data.php");

	//Подключение к СУБД
	$q= tryDBConnect();
	if (!$q) {
		echo "Ошибка соединения с базой данных";
		exit;
	}


//--------------Функции для общения с БД----------------------//


//Попытка подключения к БД
function tryDBConnect() {
	$res= mysqli_connect(db_ipaddr, db_username, db_userpass, DB);
	if ((!$res) || mysqli_connect_errno()) {return FALSE;}
//	$res = mysqli_select_db(DB, $res);
//	if (!$res) {return FALSE;}
	//@mysql_set_charset('utf8_general_ci');
	gefdb("SET NAMES 'utf8_general_ci'");
	
	return $res;
}

function reconnect() {
	global $q;
	$q= tryDBConnect();
	if (!$q) {
		echo "Ошибка соединения с базой данных";
		exit;
	}
}

//Функция, обрабатывающая ВСЕ mySQL запросы в системе к нашей БД
function my_sql_query($query) {
//	if (!(strpos($q, 'INSERT') === false)) {
//		mysql_query("INSERT INTO log  (l) VALUE ('".mysql_escape_string($q)."')");}
	//return mysql_query($q);
    global $q;
    $res = mysqli_query($q, $query);
    return $res;
}

//Get Array From Data Base. Возвращает результат запроса $s и "" в случае неудачи
function gafdb($s) { 
	$gifdb_q = my_sql_query($s);
	if (   !($gifdb_q)  /* or   !is_resource($gifdb_q)*/   ) {return "";}
	$res = Array();
	while ($x = mysqli_fetch_array($gifdb_q)) {
		$res[] = $x;
	}
	return ($res);
}

//Get Row From Data Base. Возвращает первую строку результата запроса $s и "" в случае неудачи
function grfdb($s) { 
	$gifdb_q = my_sql_query($s);
	if (   !($gifdb_q)  /* or   !is_resource($gifdb_q) */  ) {return "";}
	$res = mysqli_fetch_array($gifdb_q);
	return ($res);
}

//Get Element From Data Base. Возвращает первый элемент первой строки результата запроса $s и "" в случае неудачи
function gefdb($s) { 
	$gifdb_q = my_sql_query($s);
	if (   !($gifdb_q)   /*or   !is_resource($gifdb_q)*/   ) {return "";}
	$res = mysqli_fetch_array($gifdb_q);
	return ($res[0]);
}

//Get Element Array From Data Base. Возвращает массив, содержащий только только по одному элементу из запрашиваемых строк
function geafdb($s) {
	$x = gafdb($s);
	$res = array();
	foreach ($x as $k => $v) { $res[] = $v[0]; }
	return ($res);
}

//Insert to database. Возвращает id добавленной записи и 0, если вставка прошла неуспешно
function itdb($table_name, $values) {
    global $q;
    $query = "INSERT INTO ".$table_name." ".get_insert_string($values);
    $res = mysqli_query($q, $query);
    if (!$res) {
        return 0;
    }
    return mysql_insert_id();
}


//--------------Функции для получения служебной информации из БД----------------------//



//Возвращает список полей таблицы с указанием, обязательны они или нет: обязательные лежат в массиве 'req', необязательные в 'opt', поле ID исключается
function get_table_vars($table_name) {
	$res = array (
				'req' => array (),
				'opt' => array ()
			);
	$list_f = mysql_list_fields (DB, $table_name);
	if (!$list_f) {return $res;}
	$n = mysql_num_fields($list_f); 
	
	for($i=0; $i<$n; $i++){ 
		$name_f = mysql_field_name($list_f,$i);
		$flags_str =  mysql_field_flags ($list_f, $i); 
		if ($name_f != 'id'){
			if (in_array("not_null", explode(" ", $flags_str))) {
				$res['req'][] = $name_f;
			} else {
				$res['opt'][] = $name_f;
			}
		}
	}
	return $res;
}

//Возвращает список таблиц в БД
function get_tables() {
	$fRes = gafdb("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA =  '".DB."'");
	$res = array();
	foreach ($fRes as $k => $v) {
		$res[] = $v[0];
	}
	reconnect();
	return $res;
}


//--------------Сопровождающие функции----------------------//


//Составляет строку из ассоциативного массива типа "($k1, $k2, ..) VALUES ('$v1', '$v2', ..)". Точка с запятой в конце не добавляется
function get_insert_string($arr) {
	$s1 = "";
	$s2 = "";
	foreach ($arr as $k => $v) {
		$s1 = $s1.$k.", ";
		$s2 = $s2."'".$v."', ";
	}
	$s1 = substr($s1, 0, strlen($s1)-2);
	$s2 = substr($s2, 0, strlen($s2)-2);
	return "(".$s1.") VALUES (".$s2.")";
}

//Составляет строку из ассоциативного массива типа "$k1='$v1', $k2='$v2', ..". Точка с запятой в конце не добавляется
function get_update_string($arr) {
	$res = "";
	foreach ($arr as $k => $v) {
		$res .= $k."='".$v."', ";
	}
	$res = substr($res, 0, strlen($res)-2);
	return $res;
}

//Возвращает TRUE, если в $fact определены все ненулевые переменные с именами, перечисленными в $req_vars, и некоторые из $opt_vars; иначе - FALSE
function isParamsDefs($fact, $req_vars, $opt_vars = array()) {
	if ( !is_array($fact) or !is_array($req_vars) or !is_array($opt_vars) ) { 
		return FALSE;
	}
	
	//Проверяем наличие обязательных параметров
	foreach ($req_vars as $k => $v) {
		if (!isset($fact[$v])) { return FALSE; }
		if (!is_numeric($fact[$v]) and ($fact[$v] == "")) {return FALSE;}
	}
	
	//проверяем отсутствие лишних параметров
	foreach ($fact as $k => $v) {
		if (  !(in_array($k, $req_vars))  and 
				!(in_array($k, $opt_vars))   ) { 
			return FALSE;
		}
	}
	return TRUE;
}

function esc($s) {
    global $q;
    return mysqli_real_escape_string($q, $s);
}
?>