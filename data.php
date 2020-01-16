<?php
	// https://www.php.net/manual/pt_BR/function.header.php //
	// https://www.php.net/manual/pt_BR/function.file-get-contents.php //
	// https://www.php.net/manual/pt_BR/language.operators.comparison.php //
	// https://www.php.net/domdocument //
	// https://www.php.net/manual/pt_BR/class.domxpath.php //
	// https://www.php.net/manual/pt_BR/class.domnodelist.php //
	// https://www.php.net/manual/pt_BR/class.domnode.php //
	// https://www.php.net/manual/pt_BR/ref.strings.php //
	// https://www.php.net/manual/pt_BR/function.json-encode.php //
	// https://www.php.net/manual/pt_BR/function.strstr.php //
	// https://www.php.net/manual/pt_BR/function.preg-match-all.php //
	// https://www.php.net/manual/en/function.error-reporting.php //
	// https://www.php.net/manual/pt_BR/ref.array.php //
	// https://www.php.net/manual/pt_BR/function.array-search.php //
	// https://www.php.net/manual/pt_BR/ref.strings.php //
	// https://www.php.net/manual/pt_BR/function.str-replace.php //
	// https://www.php.net/manual/pt_BR/reserved.variables.get.php //
	// https://www.php.net/manual/pt_BR/function.isset.php //
	// https://www.php.net/manual/pt_BR/function.intval.php //
	// https://www.php.net/manual/pt_BR/control-structures.foreach.php //
	// https://www.php.net/manual/pt_BR/function.str-ireplace.php //
	// https://jsonformatter.curiousconcept.com/ //
	// https://jsonlint.com/ //
	// https://jsonformatter.org/ //
	
	//http://127.0.0.1/data.php?option=date
	//http://127.0.0.1/data.php?option=period
	//http://127.0.0.1/data.php?option=condition
	//http://127.0.0.1/data.php?option=sky
	//http://127.0.0.1/data.php?option=precipitation
	//http://127.0.0.1/data.php?option=wind
	//http://127.0.0.1/data.php?option=temperature
	//http://127.0.0.1/data.php?option=temperaturedate
	//http://127.0.0.1/data.php?option=temperaturemininum
	//http://127.0.0.1/data.php?option=temperaturemaximum
	//http://127.0.0.1/data.php?option=temperaturaunit
	//http://127.0.0.1/data.php?option=basic
	//http://127.0.0.1/data.php?option=all
	//http://127.0.0.1/data.php?option=node
	
	header( 'Content-Type: application/json' );
	error_reporting(~E_ALL);
	
	function getPeriod( $xpath, $padrao ){
		$value =[];
		for( $i=0; $i<4; $i++ ){
			preg_match( $padrao, $xpath->query( '//table [@id="forecast-table"]/thead/tr/th' )->item($i)->textContent, $matches, PREG_OFFSET_CAPTURE);
			$value[$i] = str_replace("ã", "a", $matches[0][0]);
		}
		return $value;
	}
	
	function figureToNumber( $name ){
		$figures = [
			'D/claro.png',
			'D/encoberto.png',
			'D/encoberto_chuva.png',
			'D/encoberto_nublado_chuva.png',
			'D/nublado.png',
			'D/nublado_chuva.png',
			'D/parcial_nublado.png',
			'N/claro.png',
			'N/encoberto.png',
			'N/encoberto_chuva.png',
			'N/encoberto_nublado_chuva.png',
			'N/nublado.png',
			'N/nublado_chuva.png',
			'N/parcial_nublado.png'
		];
		
		$index = array_search( $name, $figures );
		return ($index===FALSE?14:$index);
	}
	
	function getImages( $xpath, $index ){
		$node = $xpath->query( '//table [@id="forecast-table"]/tbody/tr' )->item($index);
		$child = $node->firstChild;
		$i = 0;
		$value = [];
		while( $child ){
			if( $child->nodeName == 'td' ){
				$value[$i] = substr( $child->childNodes[1]->getAttribute( 'src' ), 21 );
				$i = $i + 1;
			}
			$child = $child->nextSibling;
		}
		return $value;
	}
	
	function getOthers( $xpath, $index ){
		$node = $xpath->query( '//table [@id="forecast-table"]/tbody/tr' )->item($index);
		$child = $node->firstChild;
		$i = 0;
		$value = [];
		while( $child ){
			if( $child->nodeName == 'td' ){
				$value[$i] = str_replace( "ã", "a", $child->textContent);
				$value[$i] = str_replace( "á", "a", $value[$i]);
				$value[$i] = str_replace( "é", "e", $value[$i]);
				$value[$i] = str_replace( "ç", "c", $value[$i]);
				$i = $i + 1;
			}
			$child = $child->nextSibling;
		}
		return $value;
	}
	
	function getTemperature( $xpath, $index ){
		$node = $xpath->query( '//table [@id="forecast-table"]/tbody/tr' )->item($index);
		$child = $node->firstChild;
		$value = [];
		while( $child ){
			if( $child->nodeName == 'td' ){
				$text = trim( $child->firstChild->textContent );
				preg_match_all( '/\d+/', $text, $matches, PREG_OFFSET_CAPTURE );
				$value[0] = $matches[0][0][0];
				$value[1] = $matches[0][1][0];
				$value[2] = $matches[0][2][0];
				$value[3] = $matches[0][3][0];
				$value[4] = $matches[0][4][0];
				break;
			}
			$child = $child->nextSibling;
		}
		return $value;
	}
	
	function getJSON( $data, $field ){ 
		$result = '"' . $field . '":[';
		foreach( $data[$field] as $key => $value ){
			//$result = $result . "\"$key\":\"$value\",";
			$result = $result . "\"$value\",";
		}
		$result = substr( $result, 0, strlen($result)-1 );
		$result = $result . ']';
		return $result;
	}
	
	function help(){
		return
			"\r\n\t" . 'help": "use data.php?option=op",' . "\r\n\t" .
			'"op": "date, period, condition, sky, precipitation, wind, temperature, temperaturedate, temperaturemininum, temperaturemaximum, temperaturaunit, basic, all or node"' . "\r\n";
	}
	
	$filename = 'http://alertablu.cob.sc.gov.br/p/detalhada'; // string //
	$flags = FILE_BINARY; // int //
	$context = NULL; // resource //
	$offset = 0; // int //
	$maxlen = 100000; // int //
	if( ($text = file_get_contents($filename, $flags, $context, $offset, $maxlen)) !== FALSE ){
		$doc = new DOMDocument;
		$doc->loadHTML( $text );
		$xpath = new DOMXpath( $doc );
		$data = [];
		
		$data['data']                    = getPeriod( $xpath, '@[0-9]{2}/[0-9]{2}/[0-9]{4}@i' );
		$data['periodo_do_dia']          = getPeriod( $xpath, '@[a-zA-Z].*@i' );
		$data['condicao_do_tempo_img']   = getImages( $xpath, 0 );
		$data['ceu']                     = getOthers( $xpath, 1 );
		$data['precipitacao']            = getOthers( $xpath, 2 );
		$data['vento']                   = getOthers( $xpath, 3 );
		$data['temperatura']             = getOthers( $xpath, 4 );
		$values                          = getTemperature( $xpath, 5 );
		$data['temperatura_data']        = $values[0].'/'.$values[1].'/'.$values[2];
		$data['temperatura_minima']      = $values[3];
		$data['temperatura_maxima']      = $values[4];
		$data['temperatura_unidade']     = 'C';
		
		$result = '{';
		if( isset($_GET['option']) ){
			switch( $_GET['option'] ){
				case 'date':
					$result = $result . getJSON( $data, 'data' );
					break;
				case 'period':
					$result = $result . getJSON( $data, 'periodo_do_dia' );
					break;
				case 'condition':
					$result = $result . getJSON( $data, 'condicao_do_tempo_img' );
					break;
				case 'sky':
					$result = $result . getJSON( $data, 'ceu' );
					break;
				case 'precipitation':
					$result = $result . getJSON( $data, 'precipitacao' );
					break;
				case 'wind':
					$result = $result . getJSON( $data, 'vento' );
					break;
				case 'temperature':
					$result = $result . getJSON( $data, 'temperatura' );
					break;
				case 'temperaturedate':
					$result = $result . "\"temperatura_data\":\"{$data['temperatura_data']}\"";
					break;
				case 'temperaturemininum':
					$result = $result . "\"temperatura_minima\":\"{$data['temperatura_minima']}\"";
					break;
				case 'temperaturemaximum':
					$result = $result . "\"temperatura_maxima\":\"{$data['temperatura_maxima']}\"";
					break;
				case 'temperaturaunit':
					$result = $result . "\"temperatura_unidade\":\"{$data['temperatura_unidade']}\"";
					break;
				case 'basic':
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'data' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'periodo_do_dia' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'condicao_do_tempo_img' ) . ',';
					//$result = $result . "\n";
					$result = $result . "\"temperatura_minima\":\"{$data['temperatura_minima']}\"" . ',';
					//$result = $result . "\n";
					$result = $result . "\"temperatura_maxima\":\"{$data['temperatura_maxima']}\"";
					//$result = $result . "\n";
					break;
				case 'all':
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'data' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'periodo_do_dia' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'condicao_do_tempo_img' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'ceu' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'precipitacao' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'vento' ) . ',';
					//$result = $result . "\n";
					$result = $result . getJSON( $data, 'temperatura' ) . ',';
					//$result = $result . "\n";
					$result = $result . "\"temperatura_data\":\"{$data['temperatura_data']}\"" . ',';
					//$result = $result . "\n";
					$result = $result . "\"temperatura_minima\":\"{$data['temperatura_minima']}\"" . ',';
					//$result = $result . "\n";
					$result = $result . "\"temperatura_maxima\":\"{$data['temperatura_maxima']}\"" . ',';
					//$result = $result . "\n";
					$result = $result . "\"temperatura_unidade\":\"{$data['temperatura_unidade']}\"";
					//$result = $result . "\n";
					break;
				case 'node':
					$result = $result . str_ireplace( 'data', 'd', getJSON( $data, 'data' ) ) . ',';
					$result = $result . str_ireplace( 'periodo_do_dia', 'p', getJSON( $data, 'periodo_do_dia' ) ) . ',';
					$result = $result . '"i":[';
					$result = $result . '"' . figureToNumber($data['condicao_do_tempo_img'][0]) . '",';
					$result = $result . '"' . figureToNumber($data['condicao_do_tempo_img'][1]) . '",';
					$result = $result . '"' . figureToNumber($data['condicao_do_tempo_img'][2]) . '",';
					$result = $result . '"' . figureToNumber($data['condicao_do_tempo_img'][3]) . '"],';
					$result = $result . "\"l\":\"{$data['temperatura_minima']}\"" . ',';
					$result = $result . "\"u\":\"{$data['temperatura_maxima']}\"" . ',';
					$result = $result . '"h":"' . date('H:i') . '"';
					break;
				default:
					$result = $result . help();
					break;
			}
		}
		else{
			$result = $result . help();
		}
		$result = $result . '}';
		echo $result;
		//print_r( $data );
		//echo json_encode( $data );
	}
	else{
		echo '{"error":"error to open page"}';
	}
?>