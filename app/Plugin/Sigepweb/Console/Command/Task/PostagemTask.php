<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';
require(ROOT . DS . 'lib/FPDI/fpdi.php');

class PostagemTask extends Shell {
    
    public $uses = ['Cadastros.Destino', 'Sigepweb.SigepConta', 'Sigepweb.Servico', 'Sigepweb.Encomenda', 'Sigepweb.Postagem'];
    
    public function contas(){
        $lista = $this->SigepConta->find('all');
        foreach ($lista as $i){
            $pasta = WWW_ROOT . 'sigepweb' . DS . $i['SigepConta']['id'];
            if (!is_dir($pasta)) @mkdir($pasta, 0777, true);
            $this->importar_nfe($i['SigepConta']['id']);
            $this->orcamentos($i['SigepConta']['id']);
            $this->postagem($i['SigepConta']['id']);
        }
        /*
        $page = 1;
        $files = [];
        paginar_etq:
        echo "PAGE: {$page}\n";
        $lista = $this->Encomenda->find('all', ['conditions' => [
            'Encomenda.sigepweb_conta_id' => 14,
        ], 'limit' => 100, 'page' => $page]);
        foreach ($lista as $e){
            if (empty($e['Etiqueta']['id'])) continue;
            $files[] = $this->Encomenda->getEtiqueta($e['Encomenda']['id']);
        }
        $total = count($lista);
        if ($total==100){
            $page++;
            goto paginar_etq;
        }
        
        // iterate through the files
        $etiquetas = 0;
        foreach ($files AS $file) {
            if ($etiquetas==0){
                $pdf = new FPDI();
            }
            $etiquetas++;
            // get the page count
            try {
                $pageCount = $pdf->setSourceFile($file);
                // iterate through all pages
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    // import a page
                    $templateId = $pdf->importPage($pageNo);
                    // get the size of the imported page
                    $size = $pdf->getTemplateSize($templateId);

                    // create a page (landscape or portrait depending on the imported page size)
                    if ($size['w'] > $size['h']) {
                        $pdf->AddPage('L', array($size['w'], $size['h']));
                    } else {
                        $pdf->AddPage('P', array($size['w'], $size['h']));
                    }

                    // use the imported page
                    $pdf->useTemplate($templateId);
                }
            } catch (Exception $ex) {
                echo "ARQUIVO: $file FALHA {$ex->getMessage()}\n";
            }
            if ($etiquetas==1000){
                $etiquetas = 0;
                $arqPDF = WWW_ROOT . 'files' . DS . 'lote-etiquetas-sigep-'.onlyNumbers(microtime()).'.pdf';
                $pdf->Output($arqPDF);
                echo "ARQ END {$arqPDF}\n";
            }
        }
        $arqPDF = WWW_ROOT . 'files' . DS . 'lote-etiquetas-sigep-'.onlyNumbers(microtime()).'.pdf';
        $pdf->Output($arqPDF);
        echo "ARQ END {$arqPDF}\n";
         */
    }
    
    private function importar_nfe($conta_id){
        foreach (scandir(WWW_ROOT . 'sigepweb' . DS . $conta_id) as $i){
            if ($i=='.' || $i=='..') continue;
            $filename = WWW_ROOT . 'sigepweb' . DS . $conta_id . DS . $i;
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
                $cadastrada = $this->Encomenda->find('first', ['conditions' => [
                    'Encomenda.nfe_chave' => $nfe_chave,
                ]]);
                if ($cadastrada==0){
                    $this->Encomenda->importNFe($filename, $conta_id);
                }
            }
            unlink($filename);
        }
    }
    
    private function postagem($conta_id){
        $page = 1;
        paginarPLP:
        $encomendas = $this->Encomenda->find('all', ['conditions' => [
            'Encomenda.sigepweb_conta_id' => $conta_id,
            'Encomenda.id NOT IN (SELECT sigepweb_encomenda_id FROM sigepweb_postagens_encomendas WHERE sigepweb_encomenda_id=Encomenda.id)',
            'Encomenda.sigepweb_servico_id IS NOT NULL',
            'Etiqueta.id IS NOT NULL',
        ], 'limit' => 1000]);
        $ids = [];
        foreach ($encomendas as $e){
            $ids[] = $e['Encomenda']['id'];
        }
        $total = count($ids);
        if ($total>0){
            $this->Postagem->PreListaPostagem($conta_id, $ids);
        }
        if ($total==1000){
            $page++;
            goto paginarPLP;
        }
        
    }
    
    private function orcamentos($conta_id){
        $encomendas = $this->Encomenda->find('all', ['conditions' => [
            'Encomenda.sigepweb_conta_id' => $conta_id,
            'Encomenda.sigepweb_servico_id' => null,
            'Encomenda.id NOT IN (SELECT sigepweb_encomenda_id FROM sigepweb_orcamentos WHERE sigepweb_encomenda_id=Encomenda.id)',
            'Encomenda.sigepweb_conta_id IN (SELECT sigepweb_conta_id FROM sigepweb_contas_estrategias WHERE sigepweb_conta_id=Encomenda.sigepweb_conta_id)',
        ]]);
        foreach ($encomendas as $e){
            $this->Encomenda->SigepwebOrcamentos($e['Encomenda']['id']);
            $this->Encomenda->SigepwebEstrategiaServico($e['Encomenda']['id']);
        }
        
    }
    
}