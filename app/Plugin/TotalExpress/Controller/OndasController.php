<?php
require(ROOT . DS . 'lib/fpdf/fpdf.php');
require(ROOT . DS . 'lib/FPDI/fpdi.php');

require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

class OndasController extends TotalExpressAppController {
    
    public $uses = ['TotalExpress.TotalConta', 'TotalExpress.Encomenda', 'Separacao.Movimento', 'Separacao.MovimentoSeparacao', 'Separacao.MovimentoSeparacaoMaterial'];
    
    public function etiquetas_old_lento($onda_id) {
        Configure::write('debug', 2);
        $onda = $this->MovimentoSeparacao->read(null, $onda_id);
        $conta = $this->TotalConta->find('first', ['conditions' => [
            'TotalConta.cliente_id' => $onda['MovimentoSeparacao']['cliente_id'],
        ]]);
        $arqPDF = WWW_ROOT . DS . 'files' . DS . 'ETQ-ONDA-' . $onda_id . '-CORREIO.PDF';
        //if (!file_exists($arqPDF)){
            $codigos = [];
            $lista = $this->Movimento->find('all', ['conditions' =>[
                'Movimento.id IN (SELECT movimento_id FROM movimento_separacao_material WHERE movimento_separacao_id=?)' => $onda_id,
                'Movimento.cancelado' => 'nao',
            ], 'order' => [
                'Movimento.id' => 'ASC',
            ]]);
            $codigos = [];
            $files = [];
            $encomendas = [];
            if (!empty($conta['TotalConta']['id'])){
                foreach ($lista as $i){
                    $encomenda = $this->Encomenda->find('first', ['conditions' => [
                        'Encomenda.nfe_chave' => $i['Movimento']['nfe_chave'],
                    ]]);
                    if (!empty($encomenda['Encomenda']['id'])){
                        $encomendas[] = $encomenda['Encomenda']['id'];
                    }
                }
            }
            return $this->redirect('/total_express/ondas/encomendas/' . implode('/', $encomendas));
        //}
        return $this->redirect(DS . 'files' . DS . 'ETQ-ONDA-' . $onda_id . '-CORREIO.PDF');
    }
    
    public function etiquetas($onda_id) {
        $this->MovimentoSeparacao->recursive = -1;
        $pedidos = $this->MovimentoSeparacaoMaterial->find('all', [
            'fields' => [
                'DISTINCT MovimentoSeparacaoMaterial.movimento_id',
                '(SELECT nfe_chave FROM movimento M WHERE M.id=MovimentoSeparacaoMaterial.movimento_id) as nfe_chave',
            ],
            'conditions' => [
                'MovimentoSeparacaoMaterial.movimento_separacao_id' => $onda_id,
            ],
            'order' => [
                'MovimentoSeparacaoMaterial.movimento_id' => 'ASC',
            ],
        ]);
        $chaves = [];
        foreach ($pedidos as $p){
            if (empty($p[0]['nfe_chave'])) continue;
            $chaves[] = $p[0]['nfe_chave'];
        }
        $encomendas = [];
        if (is_array($chaves)){
            foreach ($chaves as $chave){
                $i = $this->Encomenda->find('first', ['conditions' => [
                    "Encomenda.nfe_chave" => $chave,
                ]]);
                if (empty($i['Etiqueta']['id'])) continue;
                $encomendas[] = $i['Encomenda']['id'];
            }
        }
        //die(print_r($encomendas));
        return $this->redirect("/total_express/encomenda/etiquetas/" . implode('/', $encomendas));
    }
    
    public function encomendas() {
        Configure::write('debug', 2);
        $ids = func_get_args();
        $arqPDF = WWW_ROOT . 'files' . DS . 'SIGEPWEB-ETIQUTAS-' . implode('-', $ids) . '.PDF';
        $files = [];
        foreach ($ids as $id){
            $files[] = $this->Encomenda->getEtiqueta($id);
        }
        
        $pageCount = 0;
        // initiate FPDI
        $pdf = new FPDI();

        // iterate through the files
        foreach ($files AS $file) {
            // get the page count
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
        }
        $pdf->Output($arqPDF);
        
        return $this->redirect(str_replace(WWW_ROOT, DS, $arqPDF));
    }
    
}