<?php
require_once __DIR__ . DS . '..' . DS . 'vendor' . DS . 'vendor' . DS . 'autoload.php';

use PhpSigep\Model\Diretoria;

class SigepwebAppModel extends AppModel {
    
    public $opened = false;
    
    public function sigepweb_access_data($conta) {
        if (isset($conta['SigepConta'])){
            $conta['Conta'] = $conta['SigepConta'];
        }
        return new \PhpSigep\Model\AccessData([
            'usuario'                  => $conta['Conta']['usuario'],
            'senha'                    => $conta['Conta']['senha'],
            'codAdministrativo' => $conta['Conta']['codigo_administrativo'],
            'numeroContrato'   => $conta['Conta']['numero_contrato'],
            'cartaoPostagem'   => $conta['Conta']['cartao_postagem'],
            'cnpjEmpresa'        => $conta['Conta']['cnpj'],
            'anoContrato'         => null,
            'diretoria'                => new Diretoria(Diretoria::DIRETORIA_DR_BRASILIA),
        ]);
    }
    
    public function sigepweb_start($conta){
        try {
            $config = new \PhpSigep\Config();
            $config->setAccessData($this->sigepweb_access_data($conta));
            $config->setEnv(\PhpSigep\Config::ENV_PRODUCTION);
            $config->setCacheOptions(
                array(
                    'storageOptions' => array(
                        // Qualquer valor setado neste atributo será mesclado ao atributos das classes 
                        // "\PhpSigep\Cache\Storage\Adapter\AdapterOptions" e "\PhpSigep\Cache\Storage\Adapter\FileSystemOptions".
                        // Por tanto as chaves devem ser o nome de um dos atributos dessas classes.
                        'enabled' => false,
                        'ttl' => 10,// "time to live" de 10 segundos
                        'cacheDir' => sys_get_temp_dir(), // Opcional. Quando não inforado é usado o valor retornado de "sys_get_temp_dir()"
                    ),
                )
            );
            \PhpSigep\Bootstrap::start($config);
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
}
