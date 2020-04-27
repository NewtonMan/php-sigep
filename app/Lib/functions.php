<?php
function ean_valid($ean) {
    $ean = strrev($ean);
    // Split number into checksum and number
    $checksum = substr($ean, 0, 1);
    $number = substr($ean, 1);
    $total = 0;
    for ($i = 0, $max = strlen($number); $i < $max; $i++) {
        if (($i % 2) == 0) {
            $total += ($number[$i] * 3);
        } else {
            $total += $number[$i];
        }
    }
    $mod = ($total % 10);
    $calculated_checksum = (10 - $mod);
    if ($calculated_checksum == $checksum) {
        return true;
    } else {
        return false;
    }
}
function genHASH($str){
    $salt = 2345123412341234;
    return substr(hash('sha1', "hash-{$salt}-{$str}"), 0, 8);
}
function montaChave($cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo = '')
{
  if ($codigo == '') {
    $codigo = $numero;
  }
  $forma = "%02d%02d%02d%s%02d%03d%09d%01d%08d";
  $chave = sprintf(
      $forma, $cUF, $ano, $mes, $cnpj, $mod, $serie, $numero, $tpEmis, $codigo
  );
  return $chave . calculaDV($chave);
}

function calculaDV($chave43)
{
  $multiplicadores = array(2, 3, 4, 5, 6, 7, 8, 9);
  $iCount = 42;
  $somaPonderada = 0;
  while ($iCount >= 0) {
    for ($mCount = 0; $mCount < count($multiplicadores) && $iCount >= 0; $mCount++) {
      $num = (int) substr($chave43, $iCount, 1);
      $peso = (int) $multiplicadores[$mCount];
      $somaPonderada += $num * $peso;
      $iCount--;
    }
  }
  $resto = $somaPonderada % 11;
  if ($resto == '0' || $resto == '1') {
    $cDV = 0;
  } else {
    $cDV = 11 - $resto;
  }
  return (string) $cDV;
}
function CalculaTributoEmbutido($bc, $aliquota){
    $dv = (100 - $aliquota) / 100;
    $total = $bc / $dv;
    return $total - $bc;
}
function CalculaTributoNaoEmbutido($bc, $aliquota){
    $mt = $aliquota / 100;
    return $bc * $mt;
}
function enderecoLinhaDestino($i, $quebra = false){
    return $i['endereco'] . (!empty($i['numero']) ? ", {$i['numero']}":'') . (!empty($i['complemento']) ? " - {$i['complemento']}":'') . (!empty($i['bairro']) ? ($quebra ? '<br/>':' - ') . "{$i['bairro']}":($quebra ? '<br/>':'')) . (!empty($i['municipio']) ? " - {$i['municipio']}":'') . (!empty($i['uf']) ? " - {$i['uf']}":'') . (!empty($i['cep']) ? " - CEP {$i['cep']}":'');
}
function MesPorExtenso($mes){
    switch ($mes){
        case 1: $mes = "Janeiro"; break;
        case 2: $mes = "Fevereiro"; break;
        case 3: $mes = "Mar�o"; break;
        case 4: $mes = "Abril"; break;
        case 5: $mes = "Maio"; break;
        case 6: $mes = "Junho"; break;
        case 7: $mes = "Julho"; break;
        case 8: $mes = "Agosto"; break;
        case 9: $mes = "Setembro"; break;
        case 10: $mes = "Outubro"; break;
        case 11: $mes = "Novembro"; break;
        case 12: $mes = "Dezembro"; break;
    }
    return $mes;
}
function DiaDaSemanaPorExtenso($semana){
    switch ($semana) {
        case 0: $semana = "Domingo"; break;
        case 1: $semana = "Segunda-feira"; break;
        case 2: $semana = "Ter�a-feira"; break;
        case 3: $semana = "Quarta-feira"; break;
        case 4: $semana = "Quinta-feira"; break;
        case 5: $semana = "Sexta-feira"; break;
        case 6: $semana = "S�bado"; break;
    }
    return $semana;
}
function DataHoraHumanize($str, $br=true){
    $parts = explode(' ', $str, 2);
    return $parts[0] . ($br ? '<br/>':' as ') . str_replace(':', 'h', $parts[1]);
}
function normaliza($string){
    $a = '��������������������������������������������������������������??';
    $b = 'AAAAAAACEEEEIIIIDNOOOOOOUUUUYbsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    $string = strtr($string, $a, $b);
    return utf8_encode($string);
}
function highlight($str, $highlight){
    return str_ireplace(normaliza($highlight), "<span class=\"highlightme\">{$highlight}</span>", normaliza($str));
}
function exibirCpfCnpj($str){
    $numbers = onlyNumbers($str);
    if (strlen($numbers)<=11){ // CPF
        $numbers = addLeading($numbers, 11);
        return "CPF " . substr($numbers, 0, 3) . '.' . substr($numbers, 3, 3) . '.' . substr($numbers, 6, 3) . '-' . substr($numbers, 9, 2);
    } else { // CNPJ
        $numbers = addLeading($numbers, 14);
        return "CNPJ " . substr($numbers, 0, 2) . '.' . substr($numbers, 2, 3) . '.' . substr($numbers, 5, 3) . '/' . substr($numbers, 8, 4) . '-' . substr($numbers, 12, 2);
    }
}

function isCNPJ($cnpj){
    $cnpj = trim($cnpj);
    $soma = 0;
    $multiplicador = 0;
    $multiplo = 0;
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    $cnpj = addLeading($cnpj, 14);
    if (empty($cnpj) || strlen($cnpj) <= 11 || strlen($cnpj) > 14) return FALSE;
    for($i = 0; $i <= 9; $i++) {
        $repetidos = str_pad('', 14, $i);
        if($cnpj === $repetidos) return FALSE;
    }
    $parte1 = substr($cnpj, 0, 12);  
    $parte1_invertida = strrev($parte1);
    for ($i = 0; $i <= 11; $i++)
    {
        $multiplicador = ($i == 0) || ($i == 8) ? 2 : $multiplicador;
        $multiplo = ($parte1_invertida[$i] * $multiplicador);
        $soma += $multiplo;
        $multiplicador++;
    }
    $rest = $soma % 11;
    $dv1 = ($rest == 0 || $rest == 1) ? 0 : 11 - $rest;
    $parte1 .= $dv1;
    $parte1_invertida = strrev($parte1);
      
    $soma = 0;
    for ($i = 0; $i <= 12; $i++){
        $multiplicador = ($i == 0) || ($i == 8) ? 2 : $multiplicador;
        $multiplo = ($parte1_invertida[$i] * $multiplicador);
        $soma += $multiplo;
        $multiplicador++;
    }
    $rest = $soma % 11;
    $dv2 = ($rest == 0 || $rest == 1) ? 0 : 11 - $rest;
    return ($dv1 == $cnpj[12] && $dv2 == $cnpj[13]) ? TRUE : FALSE;
}

function isCPF($cpf = null) {
 
    // Verifica se um n�mero foi informado
    if(empty($cpf)) {
        return false;
    }
 
    // Elimina possivel mascara
    $cpf = preg_replace('[^0-9]', '', $cpf);
    $cpf = str_pad($cpf, 11, '0', STR_PAD_LEFT);
     
    // Verifica se o numero de digitos informados � igual a 11 
    if (strlen($cpf) != 11) {
        return false;
    }
    // Verifica se nenhuma das sequ�ncias invalidas abaixo 
    // foi digitada. Caso afirmativo, retorna falso
    else if ($cpf == '00000000000' || 
        $cpf == '11111111111' || 
        $cpf == '22222222222' || 
        $cpf == '33333333333' || 
        $cpf == '44444444444' || 
        $cpf == '55555555555' || 
        $cpf == '66666666666' || 
        $cpf == '77777777777' || 
        $cpf == '88888888888' || 
        $cpf == '99999999999') {
        return false;
     // Calcula os digitos verificadores para verificar se o
     // CPF � v�lido
     } else {   
         
        for ($t = 9; $t < 11; $t++) {
             
            for ($d = 0, $c = 0; $c < $t; $c++) {
                $d += $cpf{$c} * (($t + 1) - $c);
            }
            $d = ((10 * $d) % 11) % 10;
            if ($cpf{$c} != $d) {
                return false;
            }
        }
 
        return true;
    }
}

function montaProdutoListaCodigo($p){
    return "{$p['id']}".(!empty($p['codigo']) ? " / {$p['codigo']}":'').(!empty($p['codigo_cliente']) && $p['codigo_cliente']!=$p['codigo'] ? " / {$p['codigo_cliente']}":'');
}
function montaProdutoListaNome($p){
    return "{$p['nome']}";
}
function montaProdutoNome($p){
    return "{$p['id']}".(!empty($p['codigo']) ? " / {$p['codigo']}":'').(!empty($p['codigo_cliente']) && $p['codigo_cliente']!=$p['codigo'] ? " / {$p['codigo_cliente']}":'')." - {$p['nome']}";
}
function sendToPrinter($nfe_filepath) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,'http://10.135.2.85/pdfs/index.php');
    curl_setopt($ch, CURLOPT_POST,1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('arquivo'=>'@'.$nfe_filepath));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
    $result=curl_exec($ch);
    curl_close ($ch);
    return $result;
}
function ArmazemAreaLocalSPFLY($local){
    $p = explode('-', $local);
    if (!isset($p[1])){
        $local = (empty($local) ? 'No ch�o':$local);
        return $local;
    } else {
        $rua = ord(substr($local, 0, 1)) - 64;
        $col = substr($p[0], 1);
        $niv = chr((64 + $p[1]));
        return "RUA {$rua} - COLUNA {$col} - N�VEL {$niv}";
    }
}
function ArmazemAreaLocalNome($local, $area=''){
    $eid = AuthComponent::User('empresa_id');
    $p = explode('-', $local);
    if ($eid == 19){
        $col = substr($p[0], 1);
        $niv = ($area=='PICKING' ? $p[1]:chr((64 + $p[1])));
        return "{$area} - COLUNA {$col} - N�VEL {$niv}";
    } else {
        if (!isset($p[1])){
            $local = (empty($local) ? 'No ch�o':$local);
            return $local;
        } else {
            if ($area=='PICKING'){
                $rua = ord(substr($local, 0, 1)) - 64;
                return "PICKING {$rua}";
            } else {
                $rua = ord(substr($local, 0, 1)) - 64;
                $col = substr($p[0], 1);
                $niv = ($area=='PICKING' ? $p[1]:chr((64 + $p[1])));
                return "RUA {$rua} - COLUNA {$col} - N�VEL {$niv}";
            }
        }
    }
}
function AramzemAreaPosicaoNome($a, $p){
    $area = $a['nome'];
    if (strtoupper($area)=='PICKING'){
        return "{$area} {$p['id']}";
    } else {
        return "{$area} - RUA {$p['rua']} - COL {$p['coluna']} - NIV {$p['nivel']}";
    }
}
function cep_por_endereco($logradouro = '') {
    
    if (empty($logradouro))
        return array();

    $postdata = http_build_query(
            array(
                'relaxation' => $logradouro,
                'TipoCep' => 'ALL',
                'semelhante' => 'N',
                'cfm' => '1',
                'Metodo' => 'listaLogradouro',
                'TipoConsulta' => 'relaxation',
                'StartRow' => '1',
                'EndRow' => '10'
            )
    );

    $opts = array('http' =>
        array(
            'method' => 'POST',
            'header' => 'Content-type: application/x-www-form-urlencoded',
            'content' => $postdata
        )
    );

    $context = stream_context_create($opts);
    $result = file_get_contents('http://www.buscacep.correios.com.br/servicos/dnec/consultaEnderecoAction.do', false, $context);

    $dom = new DOMDocument;
    $dom->loadHTML($result);

    $div = $dom->getElementById('lamina');
    $table = $div->getElementsByTagName('table')->item(2);
    $tr = $table->getElementsByTagName('tr');

    $result = array();

    foreach ($tr as $item) {

        $inside = array();
        $children = $item->childNodes;
        foreach ($children as $child) {
            $td = $child->nodeValue;
            if (trim($td) != '') {
                array_push($inside, utf8_decode($td));
            }
        }

        array_push($result, $inside);
    }

    return $result;
}

function cep_por_endereco_table($logradouro = '') {
    $array = $this->cep_por_endereco($logradouro);
    $template = '<table class = "table table-bordered table-striped table-condensed">
<thead>
<tr>
<th>Logradouro</th>
<th>Bairro</th>
<th>Localidade</th>
<th>UF</th>
<th width="65">CEP</th>
</tr>
</thead>
<tbody>';

    foreach ($array as $value) {
        $template .= '<tr>';
        foreach ($value as $item)
            $template .= '<td>' . $item . '</td>';

        $template .= '</tr>';
    }

    $template .= '</tbody></table>';
    return utf8_encode($template);
}

// Uso:
// cep_por_endereco('Avenida Na��es Unidas, Bauru');
// cep_por_endereco_table('Avenida Na��es Unidas, Bauru');

function retrieve_body($mbox, $messageid) {
    $message = array();

    $header = imap_header($mbox, $messageid);
    $structure = imap_fetchstructure($mbox, $messageid);

    $message['subject'] = $header->subject;
    $message['fromaddress'] = $header->fromaddress;
    $message['toaddress'] = $header->toaddress;
    $message['ccaddress'] = $header->ccaddress;
    $message['date'] = $header->date;

    if (check_type($structure)) {
        $message['body'] = imap_fetchbody($mbox, $messageid, "1"); ## GET THE BODY OF MULTI-PART MESSAGE
        if (!$message['body']) {
            $message['body'] = '[NO TEXT ENTERED INTO THE MESSAGE]\n\n';
        }
    } else {
        $message['body'] = imap_body($mbox, $messageid);
        if (!$message['body']) {
            $message['body'] = '[NO TEXT ENTERED INTO THE MESSAGE]\n\n';
        }
    }

    return $message;
}

function check_type($structure) { ## CHECK THE TYPE
    if ($structure->type == 1) {
        return(true); ## YES THIS IS A MULTI-PART MESSAGE
    } else {
        return(false); ## NO THIS IS NOT A MULTI-PART MESSAGE
    }
}

function mail_parse_headers($headers) {
    $headers = preg_replace('/\r\n\s+/m', '', $headers);
    preg_match_all('/([^: ]+): (.+?(?:\r\n\s(?:.+?))*)?\r\n/m', $headers, $matches);
    foreach ($matches[1] as $key => $value)
        $result[$value] = $matches[2][$key];
    return($result);
}

function mail_mime_to_array($imap, $mid, $parse_headers = false) {
    $mail = imap_fetchstructure($imap, $mid);
    $mail = mail_get_parts($imap, $mid, $mail, 0);
    if ($parse_headers)
        $mail[0]["parsed"] = mail_parse_headers($mail[0]["data"]);
    return($mail);
}

function mail_get_parts($imap, $mid, $part, $prefix) {
    $attachments = array();
    $attachments[$prefix] = mail_decode_part($imap, $mid, $part, $prefix);
    if (isset($part->parts)) { // multipart
        $prefix = ($prefix == "0") ? "" : "$prefix.";
        foreach ($part->parts as $number => $subpart)
            $attachments = array_merge($attachments, mail_get_parts($imap, $mid, $subpart, $prefix . ($number + 1)));
    }
    return $attachments;
}

function mail_decode_part($connection, $message_number, $part, $prefix) {
    $attachment = array();

    if ($part->ifdparameters) {
        foreach ($part->dparameters as $object) {
            $attachment[strtolower($object->attribute)] = $object->value;
            if (strtolower($object->attribute) == 'filename') {
                $attachment['is_attachment'] = true;
                $attachment['filename'] = $object->value;
            }
        }
    }

    if ($part->ifparameters) {
        foreach ($part->parameters as $object) {
            $attachment[strtolower($object->attribute)] = $object->value;
            if (strtolower($object->attribute) == 'name') {
                $attachment['is_attachment'] = true;
                $attachment['name'] = $object->value;
            }
        }
    }

    $attachment['data'] = imap_fetchbody($connection, $message_number, $prefix);
    if ($part->encoding == 3) { // 3 = BASE64
        $attachment['data'] = base64_decode($attachment['data']);
    } elseif ($part->encoding == 4) { // 4 = QUOTED-PRINTABLE
        $attachment['data'] = quoted_printable_decode($attachment['data']);
    }
    return($attachment);
}

function adjustBrightness($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Format the hex color string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
    }

    // Get decimal values
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    // Adjust number of steps and keep it inside 0 to 255
    $r = max(0, min(255, $r + $steps));
    $g = max(0, min(255, $g + $steps));
    $b = max(0, min(255, $b + $steps));

    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

    return '#' . $r_hex . $g_hex . $b_hex;
}

function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
    $theta = $longitude1 - $longitude2;
    $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
    $miles = acos($miles);
    $miles = rad2deg($miles);
    $miles = $miles * 60 * 1.1515;
    $feet = $miles * 5280;
    $yards = $feet / 3;
    $kilometers = $miles * 1.609344;
    $meters = $kilometers * 1000;
    return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
}

function geraCodigoBarras($text, $arquivo = '', $scale = 1.5) {
    $dv = CalculaDigitoMod10($text);
    $text = "{$text}{$dv}";
    require_once(ROOT . '/lib/barcode/class/BCGFontFile.php');
    require_once(ROOT . '/lib/barcode/class/BCGColor.php');
    require_once(ROOT . '/lib/barcode/class/BCGDrawing.php');

    // Including the barcode technology
    require_once(ROOT . '/lib/barcode/class/BCGcode128.barcode.php');
    
    // Loading Font
    $font = new BCGFontFile(ROOT . '/lib/barcode/font/Arial.ttf', 12);
    
    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
    
    $drawException = null;
    try {
        $code = new BCGcode128();
        $code->setScale($scale); // Resolution
        $code->setThickness(100); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
      1 - Filename (empty : display on screen)
      2 - Background color */
    $drawing = new BCGDrawing($arquivo, $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Header that says it is an image (remove it if you save the barcode to a file)
    if ($arquivo == '') {
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="barcode.png"');
    }

    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

function geraCodigoBarrasEAN8($text, $arquivo = '', $scale = 1.5) {
    //$dv = CalculaDigitoMod10($text);
    //$text = "{$text}{$dv}";
    require_once(ROOT . '/lib/barcode/class/BCGFontFile.php');
    require_once(ROOT . '/lib/barcode/class/BCGColor.php');
    require_once(ROOT . '/lib/barcode/class/BCGDrawing.php');

    // Including the barcode technology
    require_once(ROOT . '/lib/barcode/class/BCGean8.barcode.php');
    
    // Loading Font
    $font = new BCGFontFile(ROOT . '/lib/barcode/font/Arial.ttf', 8.5);
    
    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
    
    $drawException = null;
    try {
        $code = new BCGean8();
        $code->setScale($scale); // Resolution
        $code->setThickness(30); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
      1 - Filename (empty : display on screen)
      2 - Background color */
    $drawing = new BCGDrawing($arquivo, $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Header that says it is an image (remove it if you save the barcode to a file)
    if ($arquivo == '') {
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="barcode.png"');
    }

    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

function geraCodigoBarrasSemDV($text, $arquivo = '', $scale = 1.5) {
    require_once(ROOT . '/lib/barcode/class/BCGFontFile.php');
    require_once(ROOT . '/lib/barcode/class/BCGColor.php');
    require_once(ROOT . '/lib/barcode/class/BCGDrawing.php');

    // Including the barcode technology
    require_once(ROOT . '/lib/barcode/class/BCGcode39.barcode.php');
    
    // Loading Font
    $font = new BCGFontFile(ROOT . '/lib/barcode/font/Arial.ttf', 12);
    
    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
    
    $drawException = null;
    try {
        $code = new BCGcode39();
        $code->setScale($scale); // Resolution
        $code->setThickness(50); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
      1 - Filename (empty : display on screen)
      2 - Background color */
    $drawing = new BCGDrawing($arquivo, $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Header that says it is an image (remove it if you save the barcode to a file)
    if ($arquivo == '') {
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="barcode.png"');
    }

    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

function geraCodigoBarrasNovo($text, $arquivo = '', $scale = 1.5) {
    $dv = CalculaDigitoMod10($text);
    $text = "{$text}{$dv}";
    require_once(ROOT . '/lib/barcode/class/BCGFontFile.php');
    require_once(ROOT . '/lib/barcode/class/BCGColor.php');
    require_once(ROOT . '/lib/barcode/class/BCGDrawing.php');

    // Including the barcode technology
    require_once(ROOT . '/lib/barcode/class/BCGcode128.barcode.php');
    
    // Loading Font
    $font = new BCGFontFile(ROOT . '/lib/barcode/font/Arial.ttf', 12);
    
    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
    
    $drawException = null;
    try {
        $code = new BCGcode128();
        $code->setScale($scale); // Resolution
        $code->setThickness(100); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
      1 - Filename (empty : display on screen)
      2 - Background color */
    $drawing = new BCGDrawing($arquivo, $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Header that says it is an image (remove it if you save the barcode to a file)
    if ($arquivo == '') {
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="barcode.png"');
    }

    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

function geraCodigoBarrasNovoSemDV($text, $arquivo = '', $scale = 1.5) {
    require_once(ROOT . '/lib/barcode/class/BCGFontFile.php');
    require_once(ROOT . '/lib/barcode/class/BCGColor.php');
    require_once(ROOT . '/lib/barcode/class/BCGDrawing.php');

    // Including the barcode technology
    require_once(ROOT . '/lib/barcode/class/BCGcode128.barcode.php');
    
    // Loading Font
    $font = new BCGFontFile(ROOT . '/lib/barcode/font/Arial.ttf', 12);
    
    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
    
    $drawException = null;
    try {
        $code = new BCGcode128();
        $code->setScale($scale); // Resolution
        $code->setThickness(100); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
      1 - Filename (empty : display on screen)
      2 - Background color */
    $drawing = new BCGDrawing($arquivo, $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Header that says it is an image (remove it if you save the barcode to a file)
    if ($arquivo == '') {
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="barcode.png"');
    }

    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

function geraCodigoBarrasDUN($text, $arquivo = '', $scale = 1.5) {
    $text = $text . DunMod10($text);
    require_once(ROOT . '/lib/barcode/class/BCGFontFile.php');
    require_once(ROOT . '/lib/barcode/class/BCGColor.php');
    require_once(ROOT . '/lib/barcode/class/BCGDrawing.php');

    // Including the barcode technology
    require_once(ROOT . '/lib/barcode/class/BCGcode39.barcode.php');
    require_once(ROOT . '/lib/barcode/class/BCGcode128.barcode.php');
    
    // Loading Font
    $font = new BCGFontFile(ROOT . '/lib/barcode/font/Arial.ttf', 12);
    
    // The arguments are R, G, B for color.
    $color_black = new BCGColor(0, 0, 0);
    $color_white = new BCGColor(255, 255, 255);
    
    $drawException = null;
    try {
        $code = new BCGcode128();
        $code->setScale($scale); // Resolution
        $code->setThickness(100); // Thickness
        $code->setForegroundColor($color_black); // Color of bars
        $code->setBackgroundColor($color_white); // Color of spaces
        $code->setFont($font); // Font (or 0)
        $code->parse($text); // Text
    } catch (Exception $exception) {
        $drawException = $exception;
    }

    /* Here is the list of the arguments
      1 - Filename (empty : display on screen)
      2 - Background color */
    $drawing = new BCGDrawing($arquivo, $color_white);
    if ($drawException) {
        $drawing->drawException($drawException);
    } else {
        $drawing->setBarcode($code);
        $drawing->draw();
    }

    // Header that says it is an image (remove it if you save the barcode to a file)
    if ($arquivo == '') {
        header('Content-Type: image/png');
        header('Content-Disposition: inline; filename="barcode.png"');
    }

    // Draw (or save) the image into PNG format.
    $drawing->finish(BCGDrawing::IMG_FORMAT_PNG);
}

function array_unshift_assoc(&$arr, $key, $val) {
    $arr = array_reverse($arr, true);
    $arr[$key] = $val;
    $arr = array_reverse($arr, true);
    return $arr;
}

function CalculaDigitoMod10($num, $fator_default = 2) {
    $numtotal10 = 0;
    $fator = $fator_default;

// Separacao dos numeros
    for ($i = strlen($num); $i > 0; $i--) {
// pega cada numero isoladamente
        $numeros[$i] = substr($num, $i - 1, 1);
// Efetua multiplicacao do numero pelo (falor 10)
        $parcial10[$i] = $numeros[$i] * $fator;
// monta sequencia para soma dos digitos no (modulo 10)
        $numtotal10 .= $parcial10[$i];
        if ($fator == $fator_default) {
            $fator = 1;
        } else {
            $fator = $fator_default;     // intercala fator de multiplicacao (modulo 10)
        }
    }

    $soma = 0;
// Calculo do modulo 10
    for ($i = strlen($numtotal10); $i > 0; $i--) {
        $numeros[$i] = substr($numtotal10, $i - 1, 1);
        $soma += $numeros[$i];
    }

    $resto = $soma % 10;
    $digito = 10 - $resto;
    if ($resto == 0) {
        $digito = 0;
    }

    return $digito;
}

function DunMod10($num, $fator_default = 3) {
    $soma = 0;
    $fator = $fator_default;
    for ($i = strlen($num); $i > 0; $i--) {
        $tmp = substr($num, $i - 1, 1);
        $soma += $tmp * $fator;
        if ($fator == $fator_default) {
            $fator = 1;
        } else {
            $fator = $fator_default;     // intercala fator de multiplicacao (modulo 10)
        }
    }
    $resto = ($soma / 10) - floor(($soma / 10));
    if ($resto==0){
        return '0';
    } else {
        //((_resto * 10) - 10) * -1
        return (($resto * 10) - 10) * -1;
    }
}

function calcula_dv_municipio($codigo_municipio) {
    $peso = "1212120";
    $soma = 0;
    for ($i = 0; $i < 7; $i++) {
        $valor = substr($codigo_municipio, $i, 1) * substr($peso, $i, 1);
        if ($valor > 9)
            $soma = $soma + substr($valor, 0, 1) + substr($valor, 1, 1);
        else
            $soma = $soma + $valor;
    }
    $dv = (10 - ($soma % 10));
    if (($soma % 10) == 0)
        $dv = 0;
    return $dv;
}

function getCosmosData($barcode) {
    $produto = array();
    $produto['Produto']['codigo_barras'] = $barcode;
    libxml_use_internal_errors(true);
    $dom = new DOMDocument();
    $dom->strictErrorChecking = false;
    if (@$dom->loadHTMLFile('http://cosmos.bluesoft.com.br/products/' . $barcode)) {
        $h3s = $dom->getElementsByTagName('h3');
        foreach ($h3s as $x => $h3) {
            if ($x == 0)
                $produto['Produto']['nome'] = $h3->nodeValue;
        }
        $spans = $dom->getElementsByTagName('span');
        foreach ($spans as $span) {
            if ($span->hasAttribute('class')) {
                if ($span->getAttribute('class') == 'full-description') {
                    $val = trim($span->nodeValue);
                    $parts = explode(' - ', $val, 2);
                    $t = count($parts);
                    if ($t == 2) {
                        $produto['Produto']['nf_NCM'] = str_replace('.', '', $parts[0]);
                        $produto['Produto']['descricao'] = $produto['Produto']['nome'];
                    }
                }
            }
        }
    }
    return $produto;
}

function arrayToJSONSelectOptions($array) {
    $options_array = array();
    foreach ($array as $key => $value) {
        $options_array[] = array('text' => $value, 'value' => $key);
    }
    return $options_array;
}

function onlyNumbers($str) {
    $str_out = "";
    for ($x = 0; $x < strlen($str); $x++) {
        $y = substr($str, $x, 1);
        $str_out .= (is_numeric($y) ? $y : '');
    }
    return $str_out;
}

function hex2rgb($hex) {
    $hex = str_replace("#", "", $hex);

    if (strlen($hex) == 3) {
        $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
        $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
        $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
    } else {
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
    }
    $rgb = array($r, $g, $b);
    //return implode(",", $rgb); // returns the rgb values separated by commas
    return $rgb; // returns an array with the rgb values
}

function addLeading($i, $size, $char='0') {
    if (strlen($i)>$size){
        $i = substr($i, 0, $size);
    } elseif (strlen($i) < $size) {
        for ($x = strlen($i); $x < $size; $x++) {
            $i = "{$char}{$i}";
        }
    }
    return $i;
}

function addFiller($i, $size, $char=' ') {
    if (strlen($i)>$size){
        $i = substr($i, 0, $size);
    } elseif (strlen($i) < $size) {
        for ($x = strlen($i); $x < $size; $x++) {
            $i = "{$i}{$char}";
        }
    }
    return $i;
}

function CodigoEtiqueta($code) {
    $lw = 2;
    $hi = 100;
    $Lencode = array('0001101', '0011001', '0010011', '0111101', '0100011',
        '0110001', '0101111', '0111011', '0110111', '0001011');
    $Rencode = array('1110010', '1100110', '1101100', '1000010', '1011100',
        '1001110', '1010000', '1000100', '1001000', '1110100');
    $ends = '101';
    $center = '01010';
    /* UPC-A Must be 11 digits, we compute the checksum. */
    if (strlen($code) != 11) {
        //die("O campo de c�digo de barras deve ter 11 digitos.");
    }
    /* Compute the EAN-13 Checksum digit */
    $ncode = '0' . $code;
    $even = 0;
    $odd = 0;
    for ($x = 0; $x < 12; $x++) {
        if ($x % 2) {
            $odd += $ncode[$x];
        } else {
            $even += $ncode[$x];
        }
    }
    $code.=(10 - (($odd * 3 + $even) % 10)) % 10;
    /* Create the bar encoding using a binary string */
    $bars = $ends;
    $bars.=$Lencode[$code[0]];
    for ($x = 1; $x < 6; $x++) {
        $bars.=$Lencode[$code[$x]];
    }
    $bars.=$center;
    for ($x = 6; $x < 12; $x++) {
        $bars.=$Rencode[$code[$x]];
    }
    $bars.=$ends;
    /* Generate the Barcode Image */
    $img = ImageCreate($lw * 95 + 30, $hi + 30);
    $fg = ImageColorAllocate($img, 0, 0, 0);
    $bg = ImageColorAllocate($img, 255, 255, 255);
    ImageFilledRectangle($img, 0, 0, $lw * 95 + 30, $hi + 20, $bg);
    $shift = 10;
    for ($x = 0; $x < strlen($bars); $x++) {
        if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
            $sh = 10;
        } else {
            $sh = 0;
        }
        if ($bars[$x] == '1') {
            $color = $fg;
        } else {
            $color = $bg;
        }
        ImageFilledRectangle($img, ($x * $lw) + 15, 5, ($x + 1) * $lw + 14, $hi + 5 + $sh, $color);
    }
    /* Add the Human Readable Label */
    ImageString($img, 4, 5, $hi - 5, $code[0], $fg);
    for ($x = 0; $x < 5; $x++) {
        ImageString($img, 5, $lw * (13 + $x * 6) + 15, $hi + 5, $code[$x + 1], $fg);
        ImageString($img, 5, $lw * (53 + $x * 6) + 15, $hi + 5, $code[$x + 6], $fg);
    }
    ImageString($img, 4, $lw * 95 + 17, $hi - 5, $code[11], $fg);
    return $img;
}

function CodigoBarras($code) {
    $lw = 2;
    $hi = 100;
    $Lencode = array('0001101', '0011001', '0010011', '0111101', '0100011',
        '0110001', '0101111', '0111011', '0110111', '0001011');
    $Rencode = array('1110010', '1100110', '1101100', '1000010', '1011100',
        '1001110', '1010000', '1000100', '1001000', '1110100');
    $ends = '101';
    $center = '01010';
    /* UPC-A Must be 11 digits, we compute the checksum. */
    if (strlen($code) != 11) {
        die("UPC-A Must be 11 digits.");
    }
    /* Compute the EAN-13 Checksum digit */
    $ncode = '0' . $code;
    $even = 0;
    $odd = 0;
    for ($x = 0; $x < 12; $x++) {
        if ($x % 2) {
            $odd += $ncode[$x];
        } else {
            $even += $ncode[$x];
        }
    }
    $code.=(10 - (($odd * 3 + $even) % 10)) % 10;
    /* Create the bar encoding using a binary string */
    $bars = $ends;
    $bars.=$Lencode[$code[0]];
    for ($x = 1; $x < 6; $x++) {
        $bars.=$Lencode[$code[$x]];
    }
    $bars.=$center;
    for ($x = 6; $x < 12; $x++) {
        $bars.=$Rencode[$code[$x]];
    }
    $bars.=$ends;
    /* Generate the Barcode Image */
    $img = ImageCreate($lw * 95 + 30, $hi + 30);
    $fg = ImageColorAllocate($img, 0, 0, 0);
    $bg = ImageColorAllocate($img, 255, 255, 255);
    ImageFilledRectangle($img, 0, 0, $lw * 95 + 30, $hi + 30, $bg);
    $shift = 10;
    for ($x = 0; $x < strlen($bars); $x++) {
        if (($x < 10) || ($x >= 45 && $x < 50) || ($x >= 85)) {
            $sh = 10;
        } else {
            $sh = 0;
        }
        if ($bars[$x] == '1') {
            $color = $fg;
        } else {
            $color = $bg;
        }
        ImageFilledRectangle($img, ($x * $lw) + 15, 5, ($x + 1) * $lw + 14, $hi + 5 + $sh, $color);
    }
    /* Add the Human Readable Label */
    ImageString($img, 4, 5, $hi - 5, $code[0], $fg);
    for ($x = 0; $x < 5; $x++) {
        ImageString($img, 5, $lw * (13 + $x * 6) + 15, $hi + 5, $code[$x + 1], $fg);
        ImageString($img, 5, $lw * (53 + $x * 6) + 15, $hi + 5, $code[$x + 6], $fg);
    }
    ImageString($img, 4, $lw * 95 + 17, $hi - 5, $code[11], $fg);
    /* Output the Header and Content. */
    header("Content-Type: image/png");
    ImagePNG($img);
}

function clearString($var) {
    $var = remove_accentos($var);
    $var = str_replace("�", "", $var);
    $var = str_replace("�", "", $var);
    $var = str_replace("?", "", $var);
    $var = str_replace("&", "", $var);
    $var = str_replace("\n\r", " ", $var);
    $var = str_replace("\n", " ", $var);
    $var = str_replace("\r", " ", $var);
    $var = str_replace("\t", "", $var);
$regex = <<<'END'
/
  (
    (?: [\x00-\x7F]                 # single-byte sequences   0xxxxxxx
    |   [\xC0-\xDF][\x80-\xBF]      # double-byte sequences   110xxxxx 10xxxxxx
    |   [\xE0-\xEF][\x80-\xBF]{2}   # triple-byte sequences   1110xxxx 10xxxxxx * 2
    |   [\xF0-\xF7][\x80-\xBF]{3}   # quadruple-byte sequence 11110xxx 10xxxxxx * 3 
    ){1,100}                        # ...one or more times
  )
| .                                 # anything else
/x
END;
    return preg_replace($regex, '$1', $var);
}

function remove_accentos($str){
  $a = array('?', '?', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '�', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?', '?');
  $b = array('-', '-', 'A', 'A', 'A', 'A', 'A', 'A', 'AE', 'C', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'D', 'N', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'Y', 's', 'a', 'a', 'a', 'a', 'a', 'a', 'ae', 'c', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'n', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'y', 'y', 'A', 'a', 'A', 'a', 'A', 'a', 'C', 'c', 'C', 'c', 'C', 'c', 'C', 'c', 'D', 'd', 'D', 'd', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'E', 'e', 'G', 'g', 'G', 'g', 'G', 'g', 'G', 'g', 'H', 'h', 'H', 'h', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'I', 'i', 'IJ', 'ij', 'J', 'j', 'K', 'k', 'L', 'l', 'L', 'l', 'L', 'l', 'L', 'l', 'l', 'l', 'N', 'n', 'N', 'n', 'N', 'n', 'n', 'O', 'o', 'O', 'o', 'O', 'o', 'OE', 'oe', 'R', 'r', 'R', 'r', 'R', 'r', 'S', 's', 'S', 's', 'S', 's', 'S', 's', 'T', 't', 'T', 't', 'T', 't', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'W', 'w', 'Y', 'y', 'Y', 'Z', 'z', 'Z', 'z', 'Z', 'z', 's', 'f', 'O', 'o', 'U', 'u', 'A', 'a', 'I', 'i', 'O', 'o', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'U', 'u', 'A', 'a', 'AE', 'ae', 'O', 'o');
  return str_replace($a, $b, $str);
} 

function to_utf8($in) {
    if (is_array($in)) {
        $out = [];
        foreach ($in as $key => $value) {
            $out[to_utf8($key)] = to_utf8($value);
        }
        return $out;
    } elseif (is_string($in)) {
        return utf8_encode($in);
    } else {
        return $in;
    }
}

function clearAll($in) {
    if (is_array($in)) {
        $out = [];
        foreach ($in as $key => $value){
            $out[$key] = clearAll($value);
        }
        return $out;
    } elseif (is_string($in)) {
        $in = trim($in);
        return clearString($in);
    } else {
        return $in;
    }
}

function from_utf8($in) {
    if (is_array($in)) {
        foreach ($in as $key => $value) {
            $out[from_utf8($key)] = from_utf8($value);
        }
    } elseif (is_string($in)) {
        return utf8_decode($in);
    } else {
        return $in;
    }
    return @$out;
}

function DataToSQL($str) {
    $partes = explode('/', $str);
    if (count($partes) == 3) {
        if (strlen($partes[0]) == 1)
            $partes[0] = "0{$partes[0]}";
        if (strlen($partes[1]) == 1)
            $partes[1] = "0{$partes[1]}";
        return "{$partes[2]}-{$partes[1]}-{$partes[0]}";
    } else {
        return $str;
    }
}

function DataFromSQL($str) {
    $partes = explode('-', substr($str, 0, 10));
    if (count($partes) == 3) {
        return "{$partes[2]}/{$partes[1]}/{$partes[0]}";
    } else {
        return $str;
    }
}

function DataFromSQLShort($str) {
    $partes = explode('-', $str);
    if (count($partes) == 3) {
        $partes[0] = substr($partes[0], 2);
        return "{$partes[2]}/{$partes[1]}/{$partes[0]}";
    } else {
        return $str;
    }
}

function DataHoraToSQL($str) {
    $p = explode(' ', $str, 2);
    $pt = count($p);
    if ($pt==2){
        $str = $p[0];
        $partes = explode('/', $str);
        $tp = count($partes);
        if ($tp == 3) {
            if (strlen($partes[0]) == 1)
                $partes[0] = "0{$partes[0]}";
            if (strlen($partes[1]) == 1)
                $partes[1] = "0{$partes[1]}";
            return "{$partes[2]}-{$partes[1]}-{$partes[0]} {$p[1]}:00";
        } else {
            return $str;
        }
    } else {
        return $pt;
    }
}

function DataHoraFromSQL($str) {
    $p = explode(' ', $str);
    $str = $p[0];
    $partes = explode('-', $str);
    if (count($partes) == 3) {
        return "{$partes[2]}/{$partes[1]}/{$partes[0]} " . substr($p[1], 0, 5);
    } else {
        return $str;
    }
}

function FloatToSQL($float) {
    $float = str_replace('.', '', $float);
    $float = str_replace(',', '.', $float);
    return $float;
}

function FloatToNfe($float) {
    $parts = explode(',', $float);
    $parts = count($parts);
    if ($parts>1){
        $float = FloatToSQL($float);
    }
    return number_format($float, 2, '.', '');
}

function FloatFromSQL($float) {
    $float = str_replace(',', '.', $float);
    $float = str_replace('.', ',', $float);
    return $float;
}

function UnitFromSQL($unit) {
    if (is_numeric($unit)) {
        return number_format($unit, 0, ',', '.');
    } else {
        return $unit;
    }
}

function modulo10ab($digits){
    $soma = 0;
    $fator = 3;
    for ($x = strlen($digits); $x > 0; $digits--){
        $soma += substr($digits, ($x - 1), 1) * $fator;
        if ($fator==3){
            $fator = 1;
        } else {
            $fator = 3;
        }
    }
    $r = ($soma%10);
    
    die($r);
}
