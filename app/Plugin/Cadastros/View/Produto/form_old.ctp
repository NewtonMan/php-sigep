<?php echo $this->Form->create('Produto', array('type' => 'file')); ?>
    <fieldset>
        <legend><?php echo __('Formulário de Produto'); ?> <a href="#back" onclick="window.history.back();" title="Voltar"><img src="/img/btn-voltar.png"></a></legend>
    <?php
        echo $this->Form->input('destinatario_id', array('options'=>$clientes, 'label'=>'Cliente'));
        echo $this->Form->input('tracking', array('options'=>array('sim'=>'Sim', 'nao'=>'Não')));
        echo $this->Form->input('codigo', array('class'=>'mask_int'));
        echo $this->Form->input('codigo_cliente', array('class'=>'mask_int'));
        echo $this->Form->input('codigo_barras', array('class'=>'mask_int', 'id'=>'barcode'));
        echo $this->Form->input('nome');
        echo $this->Form->input('descricao', array('label'=>'Descrição'));
        echo $this->Form->input('peso_kg', array('class'=>'mask_weight'));
        echo $this->Form->input('peso_maximo_suportado', array('class'=>'mask_int'));
        if (isset($foto) && !empty($foto)){?>
<img src="/files/produtos/p/<?=$foto?>" /><br/><?
        }
        echo $this->Form->input('foto', array('type'=>'file'));
        echo $this->Form->input('origem', array('options'=>array('nacional'=>'Nacional', 'importado'=>'Importado')));
        echo $this->Form->input('estoque_minimo', array('class'=>'mask_int'));
        echo $this->Form->input('ativo', array('options'=>array('sim'=>'Sim', 'nao'=>'Não')));
        echo $this->Form->submit('Salvar', array('class'=>'btn btn-primary'));
    ?>
    </fieldset>
<?php echo $this->Form->end(); ?>