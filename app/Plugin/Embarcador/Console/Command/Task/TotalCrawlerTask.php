<?php
function limpaStr($str){
    $str = str_replace("\t", ' ', $str);
    $str = str_replace('  ', ' ', $str);
    if (strpos('  ', $str) !== false){
        $str = limpaStr($str);
    }
    return $str;
}
function DOMinnerHTML(DOMNode $element) 
{ 
    $innerHTML = ""; 
    $children  = $element->childNodes;

    foreach ($children as $child) 
    { 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }

    return $innerHTML; 
}
require_once ROOT . DS . 'lib' . DS . 'functions.php';

class CorreioCrawlerTask extends AppShell {
    
    private $urlRastreio = "https://www2.correios.com.br/sistemas/rastreamento/resultado.cfm";
    
    public $uses = ['Embarcador.CorreioStatus'];
    
    public function consultar($codigo){
        $this->out("Consultar: $codigo");
        $dados = http_build_query(array(
            'objetos' => $codigo,
        ));
        $contexto = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'content' => $dados,
                'header' => "Content-type: application/x-www-form-urlencoded\r\n"
                . "Content-Length: " . strlen($dados) . "\r\n",
            )
        ));
        $result = file_get_contents($this->urlRastreio, null, $contexto);
        libxml_use_internal_errors(true);
        $doc = new DOMDocument('1.0', 'UTF-8');
        $doc->loadHTML($result);
        $table = $doc->getElementsByTagName("table");
        if (!isset($table->item(0)->childNodes)) {
            $data["success"] = false;
            return $data;
        } else {
            $this->out("Consultar: $codigo");
            $data = [];
            foreach ($table->item(0)->childNodes as $node) {
                //$registro = explode(" ", $node->getElementsByTagName("td")->item(0)->nodeValue);
                $dtEvent = $node->getElementsByTagName("td")->item(0)->nodeValue;
                $dtEventArr = explode(" ", limpaStr($dtEvent), 3);
                $date = trim($dtEventArr[0]);
                $hora = trim($dtEventArr[1]);
                $local = trim($dtEventArr[2]);
                if (empty($local)){
                    $local = trim(substr($hora, 6)) . $local;
                    $hora = substr($hora, 0, 5);
                }
                $status = $node->getElementsByTagName("td")->item(1)->getElementsByTagName('strong')->item(0)->nodeValue;
                $status = utf8_decode($status);
                $logArr = explode("\n", $node->getElementsByTagName("td")->item(1)->nodeValue);
                $log = '';
                foreach ($logArr as $l){
                    $log .= " " . utf8_encode(trim($l));
                }
                $log = limpaStr($log);
                $es = $this->CorreioStatus->find('first', ['conditions' => [
                    'CorreioStatus.descricao' => utf8_encode($status),
                ]]);
                if (!isset($es['CorreioStatus']['id'])){
                    $correio_status = [
                        'CorreioStatus' => [
                            'Descricao' => $status,
                        ],
                    ];
                    $this->CorreioStatus->create();
                    $this->CorreioStatus->save($correio_status);
                }
                $data["data"][] = [
                    'data' => DataToSQL($date),
                    'hora' => $hora . ':00',
                    'status' => $status,
                    'log' => "{$local}: {$log}",
                    'status_id' => @$es['CorreioStatus']['status_id'],
                ];
            }
            $data["success"] = true;
            return $data;
        }
    }

    
}