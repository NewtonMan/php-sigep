<?php
echo utf8_decode("NOME COMPLETO;SEXO;CPF;RG;CARGO;EMPREGADOR;EMAIL;TELEFONE 1;TELEFONE 2;CADASTRO;ATUALIZADO;ULT ACESSO APP\n");
foreach ($list as $i){
    $i['User']['CPF'] = trim(substr(exibirCpfCnpj($i['User']['CPF']), 4));
    $i['User']['active'] = ($i['User']['active']==1 ? 'ATIVO':'INATIVO');
    echo utf8_decode("\"{$i['User']['nome_completo']}\";\"{$i['User']['sexo']}\";\"{$i['User']['CPF']}\";\"{$i['User']['RG']}\";\"{$i['User']['cargo']}\";\"{$i['User']['empregador']}\";\"{$i['User']['email']}\";\"{$i['User']['telefone1']}\";\"{$i['User']['telefone2']}\";\"{$i['User']['active']}\";\"{$i['User']['created']}\";\"{$i['User']['modified']}\";\"{$i['User']['last_access']}\"\n");
}
