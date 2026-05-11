-- phpMyAdmin SQL Dump
-- version 5.1.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Tempo de geração: 11-Maio-2026 às 19:05
-- Versão do servidor: 5.7.36
-- versão do PHP: 8.1.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sistema_eventos`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `criadores`
--

CREATE TABLE `criadores` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `razao_social` varchar(100) NOT NULL,
  `nome_fantasia` varchar(100) DEFAULT NULL,
  `cnpj` varchar(18) NOT NULL,
  `telefone` varchar(20) DEFAULT NULL,
  `endereco` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `criadores`
--

INSERT INTO `criadores` (`id`, `usuario_id`, `razao_social`, `nome_fantasia`, `cnpj`, `telefone`, `endereco`) VALUES
(1, 2, 'Eventos Produções LTDA', 'Eventos Produções', '12.345.678/0001-90', '(11) 99999-9999', 'Av. Paulista, 1000 - São Paulo/SP'),
(2, 5, 'so', 'da', '12.345.678/0001-91', '(11) 99999-9991', 'rua la em casa');

-- --------------------------------------------------------

--
-- Estrutura da tabela `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `criador_id` int(11) NOT NULL,
  `nome_evento` varchar(100) NOT NULL,
  `descricao` text,
  `endereco` varchar(200) NOT NULL,
  `data_evento` datetime NOT NULL,
  `capacidade_maxima` int(11) NOT NULL,
  `inscritos` int(11) DEFAULT '0',
  `status` enum('ativo','cancelado','encerrado') DEFAULT 'ativo',
  `data_criacao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `eventos`
--

INSERT INTO `eventos` (`id`, `criador_id`, `nome_evento`, `descricao`, `endereco`, `data_evento`, `capacidade_maxima`, `inscritos`, `status`, `data_criacao`) VALUES
(1, 1, 'Show de Rock', 'O melhor show de rock da cidade!', 'Espaço das Artes - Rua das Flores, 500', '2026-06-06 22:18:27', 100, 1, 'ativo', '2026-05-08 01:18:27'),
(2, 2, 'evento foda', 'temos hentai kek', 'aqui na facul', '2026-05-07 00:00:00', 150, 0, 'ativo', '2026-05-08 01:57:57'),
(3, 2, 'teste data', 'teste para ver se aparece evento q ocorre no mesmo dia de hj', 'teste', '2026-05-08 23:31:00', 3, 1, 'ativo', '2026-05-09 01:31:55'),
(4, 2, 'teste data passado', 'teste para ver se o \"status\" fica como encerrado', 'teste', '2026-05-06 22:43:00', 4, 0, 'ativo', '2026-05-09 01:43:51'),
(5, 2, 'teste data passado2', 'antigo teste para ver se o evento aparece apos a data de hj', 'teste', '2026-05-08 22:45:00', 2, 0, 'ativo', '2026-05-09 01:45:02'),
(6, 2, 'teste botao1', 'teste botao1', 'teste botao1', '2026-05-30 21:55:00', 5, 0, 'ativo', '2026-05-11 00:56:04'),
(7, 2, 'teste texto', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa /n aaaaaaaaaaaaaaaaaaaa /n a aaa', 'teste texto', '2026-05-30 22:01:00', 5, 0, 'ativo', '2026-05-11 01:01:24'),
(8, 2, 'teste texto2', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa\\naaaaaaaaaaaaaaaaaaaaaaaaaaaabbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb\\n', 'teste texto2', '2026-05-30 22:07:00', 4, 0, 'ativo', '2026-05-11 01:07:16');

-- --------------------------------------------------------

--
-- Estrutura da tabela `inscricoes`
--

CREATE TABLE `inscricoes` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `evento_id` int(11) NOT NULL,
  `data_inscricao` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('confirmada','cancelada') DEFAULT 'confirmada'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `inscricoes`
--

INSERT INTO `inscricoes` (`id`, `usuario_id`, `evento_id`, `data_inscricao`, `status`) VALUES
(2, 3, 3, '2026-05-09 01:39:22', 'confirmada'),
(4, 6, 1, '2026-05-11 00:21:28', 'confirmada');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `tipo` enum('usuario','criador') DEFAULT 'usuario',
  `data_cadastro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `senha`, `tipo`, `data_cadastro`) VALUES
(1, 'João Silva', 'joao@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'usuario', '2026-05-08 01:18:27'),
(2, 'Eventos Produções', 'eventos@email.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'criador', '2026-05-08 01:18:27'),
(3, 'gui', 'gui@gmail.com', '$2y$10$0TYxEcUDDcLdozX3PQyslOFG7JaeJ8b6sPG00pFuorAZed.7gnUQS', 'usuario', '2026-05-08 01:28:27'),
(5, 'gui', 'gui2@gmail.com', '$2y$10$RDsLm0b06FKNMFP4k8.l3.KtNbIvNkOtENYrj4xMoyORRn55Ux29m', 'criador', '2026-05-08 01:56:41'),
(6, 'g', 'g@gmail.com', '$2y$10$KGKjAf8f1kNdoidPIpH...Ud6cTAs/TAN2/gkh2Yuvo.nXu8Kqwme', 'usuario', '2026-05-11 00:21:08');

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `criadores`
--
ALTER TABLE `criadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cnpj` (`cnpj`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índices para tabela `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criador_id` (`criador_id`);

--
-- Índices para tabela `inscricoes`
--
ALTER TABLE `inscricoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscricao` (`usuario_id`,`evento_id`),
  ADD KEY `evento_id` (`evento_id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `criadores`
--
ALTER TABLE `criadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `inscricoes`
--
ALTER TABLE `inscricoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `criadores`
--
ALTER TABLE `criadores`
  ADD CONSTRAINT `criadores_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`criador_id`) REFERENCES `criadores` (`id`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `inscricoes`
--
ALTER TABLE `inscricoes`
  ADD CONSTRAINT `inscricoes_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscricoes_ibfk_2` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
