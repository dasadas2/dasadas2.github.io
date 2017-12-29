<?php
/**
 * Библиотека функций для работы с БД
 * Mysql Lazy Library
 *
 * Основной идеей библиотеки является ленивость -
 * жирные функций, но малым числом параметров, позволяющие
 * возвращать необходимые результаты в соответствии с теми
 * методами, которые использую я =)
 *
 * @package lib
 * @author Zvepr (zvepr@pm.convex.ru)
 *
*/

	global
		$ERRORS_LIB;

	$ERRORS_LIB['mysql'] = '%s';


/**
 * "Аккуратный" запрос. В случае ошибки - выдает ошибку
 * и в соответствии с указанными константами (если они, конечно, указаны)
 *
 * @param string $sql SQL
 * @param resource $link
 *
 * @return resource
 */
    function mysql_careful_query($sql, $link = false){

        global $___COUNTER;



        $res = mysql_query($sql);



        if( !$res ){ //Ошибка
		    /* Автоопределение констант, если они не установлены. Ставим самый безопасный набор */
                if(!defined('DB_ERROR_CLOSE'))
                    define('DB_ERROR_CLOSE', true);

                if(!defined('DB_ERROR_DIE'))
                    define('DB_ERROR_DIE', true);

                if(!defined('DB_ERROR_SHOW'))
                    define('DB_ERROR_SHOW', false);

                if(!defined('DB_ERROR_SHOW_SQL'))
                    define('DB_ERROR_SHOW_SQL', false);


			$errHTML = DB_ERROR_SHOW
					? '#'.mysql_errno().' <br /><br /> '.mysql_error()
					.(
						( DB_ERROR_SHOW && DB_ERROR_SHOW_SQL && $sql )
						?'<br /><br /><i>'.nl2br($sql).'</i>'
						:''
					   ).
					   (
					   	( DB_ERROR_CLOSE || DB_ERROR_DIE )
					   	?'<br />'
					   	:''
					   ).
					   (
						( DB_ERROR_CLOSE )
						?'<br /><b>DB connection closed.</b></b>'
						:''
					   ).
					   (
						( DB_ERROR_DIE )
						?'<br /><b>Script halted.</b>'
						:''
					   ).
					   ('<br>'.HTTP_HOST)
					 :'';


			echo $errHTML;

			if(DB_ERROR_DIE){
				die();
			}elseif(DB_ERROR_CLOSE){
				if($link)
					mysql_close($link);
				else
					mysql_close();
			}

			return false;
		}
		return $res;
	}


/**
 * Выбирает значение первого столбца первой строки
 * результата sql-запроса
 *
 * @param string sql SQL
 *
 * @return string
 */
    function mysql_single($sql){
        $res = mysql_careful_query($sql);

        if(mysql_num_rows($res) == 0)
            return false;




        return mysql_result($res, 0, 0);
    }


/**
 * Получение массива из результата sql запроса
 *
 * @param mixed $res результат sql-запроса или sql-запрос
 * @param string $key поле результатов, которое будет использоваться как ключ
 * @param string $value_key поле, значение которого будет подставляться в значение элемента массива
 *
 * @return array
 */
    function mysql_assoc($res, $key = 'id', $value_key = false){

    	//$t_sql = $res;

        if( !is_resource($res) ){   //Получаем запрос
            if( is_string($res) ){
                $res = mysql_careful_query($res);
            }else{
                return false;
            }
        }

        $result = array();  //Конечный массив

        if( $r = mysql_fetch_assoc($res) ){
            //такое нуедобное разбиение на условие - для большей скорости
            if( isset( $r[$key] ) ){
                if($value_key && isset($r[$value_key])){
                    do{
                        $result[ $r[$key] ] = $r[$value_key];
                    }while( $r = mysql_fetch_assoc($res) );
                }else{
                    do{
                      $result[ $r[$key] ] = $r;
                    }while( $r = mysql_fetch_assoc($res) );
                }

            }else{
                if($value_key && isset( $r[$value_key] )){
                    do{
                        $result[] = $r[$value_key];
                    }while( $r = mysql_fetch_assoc($res) );
                }else{
                    do{
                        $result[] = $r;
                    }while( $r = mysql_fetch_assoc($res) );
                }
            }
        }
        //$sql_time = (microtime(true)-$time_start);
        //if( $sql_time > 1 ) {mail('kiryam@mediasite.ru', 'sql query', 'sqltime: '.$sql_time.' PATH: '.$PATH.' '.$t_sql ); }
        //if(is_debug()){

             //echo '<span class="text small">sql time: '.$sql_time.'</span>';
        //}
        return $result;
    }


    function mysql_row( $sql ){
        $res = mysql_assoc( $sql );

        if( !$res )
            return false;

        list( , $res ) = each( $res );

        return $res;
    }

/**
 * Функция для генерации части SQL-запроса со значениями
 * столбцов для insert/update -запросов.
 *
 * Из Array('field1' => 'value1', 'field2' => 'value2',)
 * делает `field1` = "value1", `field2` = "value2",
 *
 * Никаких обработок строк при вставке их не происходит -
 * так что осторожнее - используйте для этих массивов htmlspecialchars
 * или stripslashes (или если очень хочется можно сделать это
 * в функции - но не рекомендую)
 *
 * @param array $arr массив "поле => значени"
 * @return string
 */
	function mysql_list_from_array($arr){
		if( !is_array($arr) )
			return false;

		$temp = array();
		foreach ( $arr as $_field => $_value ){
			$temp[] = '`'.$_field.'` = "'.$_value.'"';
		}

		return implode(',', $temp);
	}



/* Алиасы функций*/
	function db_query(){
		$p = func_get_args();
		return call_user_func_array('mysql_careful_query', $p);
	}

	function db_list_from_array(){
		$p = func_get_args();
		return call_user_func_array('mysql_list_from_array', $p);
	}

	function db_assoc(){
		$p = func_get_args();
		return call_user_func_array('mysql_assoc', $p);
	}

	function db_fetch_array(){
		$p = func_get_args();
		return call_user_func_array('mysql_fetch_array', $p);
	}

	function db_fetch_assoc(){
		$p = func_get_args();
		return call_user_func_array('mysql_fetch_assoc', $p);
	}

	function db_single(){
		$p = func_get_args();
		return call_user_func_array('mysql_single', $p);
	}

	function db_insert_id(){
		$p = func_get_args();
		return call_user_func_array('mysql_insert_id', $p);
	}

	function db_num_rows(){
		$p = func_get_args();
		return call_user_func_array('mysql_num_rows', $p);
	}

	function db_row(){
		$p = func_get_args();
		return call_user_func_array('mysql_row', $p);
	}

	function db_error(){
		$p = func_get_args();
		return call_user_func_array('mysql_error', $p);
	}

?>