<?php
class TotalExpressAppModel extends AppModel {
    
    public function total_express_access_data($conta) {
        if (isset($conta['TotalConta'])){
            $conta['Conta'] = $conta['TotalConta'];
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
    
    public function total_express_start($conta){
        $config = new \PhpSigep\Config();
        $config->setAccessData($this->total_express_access_data($conta));
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
    }
    
}
