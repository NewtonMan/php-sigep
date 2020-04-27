<?php
class EmbarcadorAppController extends AppController {
    
    public $layout = 'Embarcador.default';
    
    public $crumbs = [];
    
    public function beforeRender() {
        parent::beforeRender();
        $this->set('crumbs', $this->crumbs);
    }
    
}