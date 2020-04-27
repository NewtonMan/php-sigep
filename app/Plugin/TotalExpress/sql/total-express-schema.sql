-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Tempo de geração: 11-Mar-2020 às 16:55
-- Versão do servidor: 5.7.29-0ubuntu0.16.04.1-log
-- versão do PHP: 7.3.13

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `mktlog_ptk`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `total_express_contas`
--

DROP TABLE IF EXISTS `total_express_contas`;
CREATE TABLE IF NOT EXISTS `total_express_contas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cliente_id` int(11) NOT NULL,
  `agencia_id` int(11) NOT NULL,
  `endpoint` varchar(255) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `senha` varchar(100) NOT NULL,
  `padrao_total_express_servico_id` int(11) DEFAULT NULL,
  `natureza` varchar(100) NOT NULL,
  `cfop` varchar(4) NOT NULL,
  `eid` int(11) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `cliente_id` (`cliente_id`),
  KEY `padrao_total_express_servico_id` (`padrao_total_express_servico_id`),
  KEY `agencia_id` (`agencia_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `total_express_encomendas`
--

DROP TABLE IF EXISTS `total_express_encomendas`;
CREATE TABLE IF NOT EXISTS `total_express_encomendas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_express_conta_id` int(11) NOT NULL,
  `total_express_servico_id` int(11) DEFAULT NULL,
  `nfe_chave` varchar(44) NOT NULL,
  `nfe_serie` varchar(3) NOT NULL,
  `nfe_numero` varchar(9) NOT NULL,
  `nfe_data` date NOT NULL,
  `nfe_emitente_id` int(11) NOT NULL,
  `nfe_destino_id` int(11) NOT NULL,
  `nfe_destino_local_id` int(11) DEFAULT NULL,
  `nfe_transportadora_id` int(11) DEFAULT NULL,
  `nfe_valor` decimal(30,2) NOT NULL DEFAULT '0.00',
  `nfe_peso_gr` int(11) NOT NULL DEFAULT '100',
  `nfe_observacao` text,
  `postagem_id` int(11) DEFAULT NULL,
  `tracking_number` varchar(100) DEFAULT NULL,
  `total_express_status_id` int(11) DEFAULT NULL,
  `status_dh` datetime DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nfe_chave` (`nfe_chave`),
  KEY `total_express_servico_id` (`total_express_servico_id`),
  KEY `total_express_conta_id` (`total_express_conta_id`),
  KEY `nfe_serie` (`nfe_serie`),
  KEY `nfe_numero` (`nfe_numero`),
  KEY `nfe_data` (`nfe_data`),
  KEY `nfe_emitente_id` (`nfe_emitente_id`),
  KEY `nfe_destino_id` (`nfe_destino_id`),
  KEY `nfe_destino_local_id` (`nfe_destino_local_id`),
  KEY `nfe_transportadora_id` (`nfe_transportadora_id`),
  KEY `tracking_number` (`tracking_number`),
  KEY `expedicao_romaneio_id` (`postagem_id`)
) ENGINE=InnoDB AUTO_INCREMENT=7615 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `total_express_local`
--

DROP TABLE IF EXISTS `total_express_local`;
CREATE TABLE IF NOT EXISTS `total_express_local` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cpf_cnpj` bigint(20) NOT NULL,
  `endereco` varchar(100) NOT NULL,
  `numero` varchar(60) NOT NULL,
  `complemento` varchar(60) DEFAULT NULL,
  `bairro` varchar(100) DEFAULT NULL,
  `municipio` varchar(100) DEFAULT NULL,
  `uf` varchar(100) DEFAULT NULL,
  `ibge_estado_id` int(11) DEFAULT NULL,
  `ibge_cidade_id` int(11) DEFAULT NULL,
  `cep` varchar(9) DEFAULT NULL,
  `lat` double(40,30) DEFAULT NULL,
  `lgt` double(40,30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lat` (`lat`),
  KEY `lgt` (`lgt`),
  KEY `cpf_cnpj` (`cpf_cnpj`)
) ENGINE=InnoDB AUTO_INCREMENT=3392 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Estrutura da tabela `total_express_orcamentos`
--

DROP TABLE IF EXISTS `total_express_orcamentos`;
CREATE TABLE IF NOT EXISTS `total_express_orcamentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `total_express_encomenda_id` int(11) NOT NULL,
  `total_express_servico_id` int(11) DEFAULT NULL,
  `valor` decimal(30,2) NOT NULL DEFAULT '0.00',
  `valorAvisoRecebimento` decimal(30,2) NOT NULL DEFAULT '0.00',
  `valorMaoPropria` decimal(30,2) NOT NULL DEFAULT '0.00',
  `prazoEntrega` int(11) NOT NULL DEFAULT '0',
  `entregaDoliciliar` varchar(10) DEFAULT NULL,
  `entregaSabado` varchar(10) DEFAULT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `total_express_encomenda_id` (`total_express_encomenda_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estrutura da tabela `total_express_servicos`
--

DROP TABLE IF EXISTS `total_express_servicos`;
CREATE TABLE IF NOT EXISTS `total_express_servicos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `codigo` varchar(10) DEFAULT NULL,
  `id_servico` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Truncar tabela antes do insert `total_express_servicos`
--

TRUNCATE TABLE `total_express_servicos`;
--
-- Extraindo dados da tabela `total_express_servicos`
--

INSERT INTO `total_express_servicos` (`id`, `name`, `codigo`, `id_servico`) VALUES
(1, 'Standard', 'STD', 'STD');

-- --------------------------------------------------------

--
-- Estrutura da tabela `total_express_status`
--

DROP TABLE IF EXISTS `total_express_status`;
CREATE TABLE IF NOT EXISTS `total_express_status` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `codigo` int(11) NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `embarcador_status_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `embarcador_status_id` (`embarcador_status_id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;

--
-- Truncar tabela antes do insert `total_express_status`
--

TRUNCATE TABLE `total_express_status`;
--
-- Extraindo dados da tabela `total_express_status`
--

INSERT INTO `total_express_status` (`id`, `codigo`, `descricao`, `embarcador_status_id`) VALUES
(1, 0, 'ARQUIVO RECEBIDO', 0),
(2, 1, 'ENTREGA REALIZADA', 2),
(3, 6, 'ENDERECO DESTINATARIO NAO LOCALIZADO', 7),
(4, 8, 'DUAS OU MAIS VEZES AUSENTE/FECHADO', 47),
(5, 9, 'RECUSADA - MERCADORIA EM DESACORDO', 5),
(6, 10, 'SINISTRO LIQUIDADO', 0),
(7, 11, 'RECUSADA - AVARIA DA MERCADORIA / EMBALAGEM', 12),
(8, 12, 'SERVIÇO NÃO ATENDIDO', 0),
(9, 13, 'CIDADE NAO ATENDIDA', 14),
(10, 14, 'MERCADORIA AVARIADA', 79),
(11, 15, 'EMBALAGEM EM ANALISE', 100),
(12, 16, 'RECUSADA - PEDIDO / COLETA EM DUPLICIDADE', 4),
(13, 18, 'EXTRAVIO / HUB', 81),
(14, 19, 'EXTRAVIO POR DIVERGÊNCIA DE COLETA', 81),
(15, 20, 'ESPERA SUPERIOR A 20 MINUTOS', 100),
(16, 21, 'CLIENTE AUSENTE/ ESTABELECIMENTO FECHADO', 22),
(17, 23, 'EXTRAVIO DE MERCADORIA EM TRANSITO', 81),
(18, 24, 'ACAREAÇÃO SEM SUCESSO – MERCADORIA EXTRAVIADA', 81),
(19, 25, 'DEVOLUÇÃO EM ANDAMENTO AO CD', 0),
(20, 26, 'DEVOLUÇÃO RECEBIDA NO CD', 0),
(21, 27, 'ROUBO DE CARGA', 28),
(22, 29, 'CLIENTE RETIRA NA TRANSPORTADORA', 30),
(23, 30, 'EXTRAVIO / AGENTE', 81),
(24, 31, 'EXTRAVIO / COURIER OU MOTORISTA', 81),
(25, 32, 'EXTRAVIO / TRANSFERÊNCIA AEREA', 81),
(26, 33, 'EXTRAVIO / TRANSFERÊNCIA RODOVIARIA', 81),
(27, 34, 'DEVOLVIDO PELOS CORREIOS', 26),
(28, 35, 'EXTRAVIO / ROUBO - TRANSPORTADORAS', 81),
(29, 36, 'EXTRAVIO / ROUBO - ECT', 81),
(30, 37, 'FORA DE ROTA', 76),
(31, 38, 'REDESPACHADO CORREIO', 0),
(32, 39, 'DESTINATARIO MUDOU-SE', 78),
(33, 40, 'CANCELADO PELO DESTINATARIO', 0),
(34, 41, 'DESTINATARIO DESCONHECIDO', 7),
(35, 42, 'DESTINATARIO DEMITIDO', 75),
(36, 43, 'DESTINATARIO FALECEU', 46),
(37, 44, 'FALTA BLOCO DO EDIFICIO / SALA', 61),
(38, 45, 'FALTA NOME DE CONTATO / DEPARTAMENTO / RAMAL', 61),
(39, 46, 'FALTA NUMERO APT/CASA', 61),
(40, 47, 'NUMERO INDICADO NÃO LOCALIZADO', 61),
(41, 48, 'INTEMPERIES', 100),
(42, 49, 'AREA URBANA NAO ATENDIDA', 14),
(43, 50, 'VEICULO ENTREGADOR AVARIADO', 100),
(44, 51, 'NÃO VISITADO', 100),
(45, 52, 'RECUSADO POR TERCEIROS', 4),
(46, 53, 'CONFLITO CEP/LOCALIDADE', 100),
(47, 55, 'RECUSA - FALTA DE COMPRA', 4),
(48, 56, 'CANCELADO PELO REMETENTE', 0),
(49, 57, 'PRODUTO NAO DISPONIVEL PARA COLETA', 0),
(50, 58, 'COLETA REVERSA NÃO SOLICITADA', 0),
(51, 59, 'RECEBEDOR SEM IDENTIFICACAO', 0),
(52, 60, 'RMA EXECUTADO', 0),
(53, 61, 'DEVOLVIDA AO REMETENTE', 26),
(54, 62, 'RMA RECEBIDO NO CD', 0),
(55, 63, 'ORDEM DE COLETA CANCELADA', 100),
(56, 64, 'DINHEIRO OU CHEQUE NAO DISPONIVEL', 0),
(57, 65, 'VALOR DE COD DIVERGENTE', 0),
(58, 68, 'COLETA RECEBIDA NO CD', 105),
(59, 69, 'COLETA RECEBIDA COM NC NO CD DE', 105),
(60, 70, 'AVISO ENTREGA/EXECUÇÃO SERVIÇO', 0),
(61, 71, 'DEVOLUÇÃO EM ANDAMENTO PARA O REMETENTE', 0),
(62, 72, 'DEVOLUÇÃO REJEITADA PELO REMETENTE', 8),
(63, 73, 'AGUARDANDO AUTORIZAÇÃO PARA DEVOLUÇÃO', 0),
(64, 74, 'PERDA DE EMBARQUE POR HORÁRIO DE CORTE', 100),
(65, 80, 'EM AGENDAMENTO', 99),
(66, 83, 'COLETA REALIZADA', 105),
(67, 84, 'COLETA REALIZADA C/ NÃO CONFORMIDADE', 105),
(68, 90, 'ENCOMENDA DESCARTADA', 0),
(69, 91, 'ENTREGA PROGRAMADA', 92),
(70, 92, 'PROBLEMAS FISCAIS', 31),
(71, 93, 'PROBLEMAS OPERACIONAIS', 100),
(72, 94, 'EXTRAVIO PARCIAL', 82),
(73, 95, 'FALTA DE COMPLEMENTO FISICO', 61),
(74, 98, 'AGUARDANDO CUBAGEM', 100),
(75, 101, 'RECEBIDA E PROCESSADA NO CD', 1),
(76, 102, 'TRANSFERENCIA PARA:', 1),
(77, 103, 'RECEBIDO CD DE:', 0),
(78, 104, 'PROCESSO DE ENTREGA', 1),
(79, 106, 'REDESPACHO TRANSPORTADORA', 0),
(80, 107, 'REDESPACHO POR CONTA DO CLIENTE', 0);

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `total_express_encomendas`
--
ALTER TABLE `total_express_encomendas`
  ADD CONSTRAINT `total_express_encomendas_ibfk_1` FOREIGN KEY (`total_express_conta_id`) REFERENCES `total_express_contas` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION,
  ADD CONSTRAINT `total_express_encomendas_ibfk_2` FOREIGN KEY (`total_express_servico_id`) REFERENCES `total_express_servicos` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Limitadores para a tabela `total_express_orcamentos`
--
ALTER TABLE `total_express_orcamentos`
  ADD CONSTRAINT `total_express_orcamentos_ibfk_1` FOREIGN KEY (`total_express_encomenda_id`) REFERENCES `total_express_encomendas` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
