<?php
class CorreioCrawlerTask extends AppShell {
    
    private $urlRastreio = "https://www2.correios.com.br/sistemas/rastreamento/resultado.cfm";
    
    public $uses = ['Embarcador.CorreioStatus'];
    
    public function consultar($codigo) {
        $this->out("Consultar1: $codigo...");
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
            $this->out("Nao localizado: $codigo");
            return $data;
        } else {
            $this->out("Consultar2: $codigo");
            $data = [];
            foreach ($table->item(0)->childNodes as $node) {
                if ($doc->nodeType == XML_TEXT_NODE) {
                    $this->out("\t\tTEXT: {$node->nodeValue}");
                    continue;
                }
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
                print_r($status);
                $this->out("\n");
                $logArr = explode("\n", $node->getElementsByTagName("td")->item(1)->nodeValue);
                $log = '';
                foreach ($logArr as $l){
                    $log .= " " . utf8_encode(trim($l));
                }
                $log = limpaStr($log);
                $es = $this->CorreioStatus->find('first', ['conditions' => [
                    'CorreioStatus.Descricao' => $status,
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