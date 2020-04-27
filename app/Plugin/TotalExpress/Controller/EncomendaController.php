<?php
require_once ROOT . DS . APP_DIR . DS . 'Vendor' . DS . 'autoload.php';

class EncomendaController extends TotalExpressAppController {
    
    public $uses = ['Cadastros.Destino', 'TotalExpress.TotalConta', 'TotalExpress.Servico', 'TotalExpress.TotalEncomenda'];
    
    public function index(){
        $this->setRefer();
        $criterios = $this->crudModelSearch('TotalEncomenda');
        if (isset($_GET['total_express_conta_id'])){
            $criterios = [
                'TotalEncomenda.total_express_conta_id' => $_GET['total_express_conta_id'],
                $criterios,
            ];
        }
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de Encomendas TotalExpress';
        $this->request->data['CRUD']['cols'] = [
            'id' => [
                'label' => 'Pedido',
            ],
            'nfe_emitente_id' => [
                'label' => 'Emitente',
                'model' => 'Emitente',
                'field' => 'fantasia',
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
            'tracking_number' => [
                'label' => 'Código Rastreio',
            ],
            'postagem_id' => [
                'label' => 'Postagem',
            ],
            'status' => [
                'label' => 'Status',
                'model' => 'TotalStatus',
                'field' => 'descricao',
            ],
            'status_dh' => [
                'label' => 'Data Status',
            ],
        ];
        $this->request->data['CRUD']['data'] = $this->paginate('TotalEncomenda', $criterios);
        $this->request->data['CRUD']['model'] = 'TotalEncomenda';
        $this->request->data['CRUD']['actions'] = [
            'custom' => [
            ],
            //'create' => '/total_express/encomenda/form?total_express_conta_id='.@$_GET['total_express_conta_id'],
            //'update' => '/total_express/encomenda/form/',
            //'delete' => '/total_express/encomenda/delete/',
        ];
    }
    
    public function delete($id){
        $this->TotalEncomenda->id = $id;
        $this->TotalEncomenda->delete($id);
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
                $this->TotalEncomenda->importNFe($xml, $this->request->data['TotalEncomenda']['total_express_conta_id'], $this->request->data['TotalEncomenda']['total_express_servico_id']);
            }
            
        }
        $contas = $this->TotalConta->find('list');
        $servicos = $this->Servico->find('list');
        $this->request->data['CRUD']['titulo'] = 'Gerenciamento de TotalEncomenda TotalExpress';
        $this->request->data['CRUD']['form'] = [
            'total_express_conta_id' => [
                'options' => [
                    'label' => 'TotalConta',
                    'options' => $contas,
                    'empty' => false,
                ],
            ],
            'total_express_servico_id' => [
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
        $this->request->data['CRUD']['model'] = 'TotalEncomenda';
        if (isset($_GET['total_express_conta_id'])){
            $this->request->data['CRUD']['form']['total_express_conta_id']['options']['value'] = $_GET['total_express_conta_id'];
        }
    }
    
    public function etiqueta($encomenda_id){
        $filename = $this->TotalEncomenda->getEtiqueta($encomenda_id);
        return $this->redirect(str_replace(WWW_ROOT, DS, $filename));
    }
    
    public function etiquetas(){
        $files = [];
        foreach (func_get_args() as $id){
            $files[] = $this->TotalEncomenda->getEtiqueta($id);
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