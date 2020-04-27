<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

class PostagemTask extends Shell {
    
    public $uses = ['Cadastros.Destino', 'TotalExpress.TotalConta', 'TotalExpress.Servico', 'TotalExpress.TotalEncomenda', 'TotalExpress.Local', 'TotalExpress.Postagem'];
    
    public function contas(){
        $lista = $this->TotalConta->find('all');
        foreach ($lista as $i){
            $pasta = WWW_ROOT . 'total_express';
            if (!is_dir($pasta)) @mkdir($pasta, 0777, true);
            $this->importar_nfe($i['TotalConta']['id']);
            $this->postagem($i['TotalConta']['id']);
            $this->tracking($i['TotalConta']['id']);
        }
    }
    
    private function importar_nfe($conta_id) {
        foreach (scandir(WWW_ROOT . 'total_express') as $i){
            if ($i=='.' || $i=='..') continue;
            $filename = WWW_ROOT . 'total_express' . DS . $i;
            $std = new NFePHP\NFe\Common\Standardize();
            
            // ERRO AQUI
            try {
                $nfe = $std->toArray(file_get_contents($filename));
            } catch (Exception $ex) {
                echo "$i não é NF-e: {$ex->getMessage()}\n";
                continue;
            }
            // ERRO AQUI
            
            $nfe_chave = substr($nfe['NFe']['infNFe']['attributes']['Id'], 3, 44);
            if (strlen($nfe_chave)==44){
                $cadastrada = $this->TotalEncomenda->find('first', ['conditions' => [
                    'TotalEncomenda.nfe_chave' => $nfe_chave,
                ]]);
                if ($cadastrada==0){
                    $this->TotalEncomenda->importNFe($filename, $conta_id);
                }
            }
                unlink($filename);
        }
    }
    
    private function postagem($conta_id){
        $this->TotalConta->TotalExpressColetaRequest($conta_id);
    }
    
    private function tracking($conta_id){
        $this->TotalConta->TotalExpressTrackingRequest($conta_id);
    }
    
}