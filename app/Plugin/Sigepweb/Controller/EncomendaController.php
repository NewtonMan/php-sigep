<?php
require(ROOT . DS . 'lib/fpdf/fpdf.php');
require(ROOT . DS . 'lib/FPDI/fpdi.php');

require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

class EncomendaController extends SigepwebAppController {
    
    public $uses = ['Cadastros.Destino', 'Sigepweb.SigepConta', 'Sigepweb.Servico', 'Sigepweb.Encomenda'];
    
    public function orcamentos($encomenda_id){
        $this->Encomenda->SigepwebOrcamentos($encomenda_id);
    }
    
    public function index(){
        $this->setRefer();
        $criterios = $this->crudModelSearch('Encomenda');
        if (isset($_GET['sigepweb_conta_id'])){
            $criterios = [
                'Encomenda.sigepweb_conta_id' => $_GET['sigepweb_conta_id'],
                $criterios,
            ];
        }
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Encomendas Sigepweb';
        $this->request->data['CRUD']['cols'] = [
            'nfe_emitente_id' => [
                'label' => 'Emitente',
                'model' => 'Emitente',
                'field' => 'fantasia',
            ],
            'sigepweb_servico_id' => [
                'label' => 'Serviço',
                'model' => 'Servico',
                'field' => 'name',
            ],
            'codigo' => [
                'label' => 'Código Rastreio',
                'model' => 'Etiqueta',
                'field' => 'codigo_com_dv',
            ],
            'sigepweb_postagem_data' => [
                'label' => 'Postagem',
                'model' => 'Encomenda',
                'field' => 'sigepweb_postagem_id',
            ],
            'nfe_data' => [
                'label' => 'Data Emissão',
            ],
            'nfe_serie' => [
                'label' => 'Série',
            ],
            'nfe_numero' => [
                'label' => 'Número NF',
            ],
            'cidade' => [
                'label' => 'Cidade',
                'model' => 'Destino',
                'field' => 'municipio',
            ],
            'estado' => [
                'label' => 'UF',
                'model' => 'Destino',
                'field' => 'uf',
            ],
        ];
        $this->request->data['CRUD']['data'] = $this->paginate('Encomenda', $criterios);
        $this->request->data['CRUD']['model'] = 'Encomenda';
        $this->request->data['CRUD']['actions'] = [
            'custom' => [
                [
                    'href' => 'https://www2.correios.com.br/sistemas/rastreamento/default.cfm?objetos=',
                    'btn' => 'default',
                    'icon' => 'fa fa-search',
                    'label' => 'Rastreamento',
                    'suffix' => [
                        'model' => 'Etiqueta',
                        'field' => 'codigo_com_dv',
                    ],
                ],
                [
                    'href' => '/sigepweb/encomenda/etiqueta/',
                    'btn' => 'default',
                    'icon' => 'fa fa-tags',
                    'label' => 'Etiqueta',
                    'suffix' => [
                        'model' => 'Encomenda',
                        'field' => 'id',
                    ],
                ],
            ],
            'create' => '/sigepweb/encomenda/form?sigepweb_conta_id='.@$_GET['sigepweb_conta_id'],
            'update' => '/sigepweb/encomenda/form/',
            'delete' => '/sigepweb/encomenda/delete/',
        ];
    }
    
    public function delete($id){
        $this->Encomenda->id = $id;
        $this->Encomenda->delete($id);
        $this->getRefer();
    }
    
    public function form($id=null){
        if ($this->request->is('post') || $this->request->is('put')) {
            $arquivos = [];
            if (is_array($_FILES['arquivos']['error'])) {
                foreach ($_FILES['arquivos']['error'] as $x => $error) {
                    if ($error == UPLOAD_ERR_OK && $_FILES['arquivos']['type'][$x]=='text/xml') {
                        $arquivos[] = $_FILES['arquivos']['tmp_name'][$x];
                    }
                }
            } elseif ($_FILES['arquivos']['error'] == UPLOAD_ERR_OK && $_FILES['arquivos']['type']=='text/xml') {
                $arquivos[] = $_FILES['arquivos']['tmp_name'];
            }
            foreach ($arquivos as $xml){
                $this->Encomenda->importNFe($xml, $this->request->data['Encomenda']['sigepweb_conta_id'], $this->request->data['Encomenda']['sigepweb_servico_id']);
            }
            
        }
        $contas = $this->SigepConta->find('list');
        $servicos = $this->Servico->find('list');
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Encomenda Sigepweb';
        $this->request->data['CRUD']['form'] = [
            'sigepweb_conta_id' => [
                'options' => [
                    'label' => 'SigepConta',
                    'options' => $contas,
                    'empty' => false,
                ],
            ],
            'sigepweb_servico_id' => [
                'options' => [
                    'label' => 'Serviço',
                    'options' => $servicos,
                    'empty' => ' - Definir com base na estratégia cadastrada - ',
                ],
            ],
            'arquivos' => [
                'options' => [
                    'label' => 'Arquivos XML das NF-es',
                    'name' => 'arquivos[]',
                    'type' => 'file',
                    'multiple' => 'multiple',
                    'accept' => 'text/xml'
                ],
            ],
        ];
        $this->request->data['CRUD']['model'] = 'Encomenda';
        if (isset($_GET['sigepweb_conta_id'])){
            $this->request->data['CRUD']['form']['sigepweb_conta_id']['options']['value'] = $_GET['sigepweb_conta_id'];
        }
    }
    
    public function etiqueta($encomenda_id){
        $filename = $this->Encomenda->getEtiqueta($encomenda_id);
        return $this->redirect($filename);
    }
    
    public function etiquetas(){
        $files = [];
        foreach (func_get_args() as $id){
            $files[] = $this->Encomenda->getEtiqueta($id);
        }
        
        $arqPDF = WWW_ROOT . 'files' . DS . 'lote-etiquetas-sigep-'.onlyNumbers(microtime()).'.pdf';
        
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