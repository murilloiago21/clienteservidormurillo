-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 09-Dez-2023 às 21:35
-- Versão do servidor: 10.4.28-MariaDB
-- versão do PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `servidormurillo`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `pontos`
--

CREATE TABLE `pontos` (
  `id` int(11) NOT NULL,
  `nome` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `pontos`
--

INSERT INTO `pontos` (`id`, `nome`) VALUES
(1, 'Lab.1'),
(2, 'Lab.2'),
(3, 'Lab.3'),
(4, 'Lab.4'),
(5, 'Lab.5'),
(6, 'Lab.6'),
(7, 'Lab.7'),
(8, 'Lab.8'),
(9, 'Portaria principal'),
(10, 'Capela'),
(11, 'Auditorio'),
(12, 'LaCA'),
(13, 'DAINF'),
(14, 'Escada 1'),
(15, 'Escada 2'),
(16, 'Escada 3'),
(17, 'Escada 4'),
(18, 'Rampa 1');

-- --------------------------------------------------------

--
-- Estrutura da tabela `segmentos`
--

CREATE TABLE `segmentos` (
  `distancia` double NOT NULL,
  `status` int(11) NOT NULL,
  `direcao` varchar(400) NOT NULL,
  `id` int(11) NOT NULL,
  `ponto_inicial` varchar(60) NOT NULL,
  `ponto_final` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `segmentos`
--

INSERT INTO `segmentos` (`distancia`, `status`, `direcao`, `id`, `ponto_inicial`, `ponto_final`) VALUES
(100, 1, 'Em frente (Cuidado! Escada subindo)', 1, 'Portaria principal', 'Escada 1'),
(5, 1, 'Cima', 2, 'Escada 1', 'Capela'),
(20, 1, 'Direita', 3, 'Capela', 'Escada 2'),
(2, 1, 'Frente', 4, 'Escada 2', 'Lab.6'),
(20, 1, 'Esquerda', 5, 'Lab.6', 'Lab.7'),
(10, 1, 'Em frente', 6, 'Lab.7', 'Lab.8'),
(5, 1, 'Vire a esquerda(Cuidado! Escada subindo)', 7, 'Lab.8', 'Escada 4'),
(2, 1, 'Em frente (Cuidado! Escada subindo)', 8, 'Escada 4', 'LaCA'),
(40, 1, 'Em frente', 9, 'LaCA', 'Rampa 1'),
(35, 1, 'Vire a esquerda (Cuidado! rampa mantenha a esquerda)', 10, 'Rampa 1', 'Auditório'),
(20, 1, 'Vire a esquerda', 11, 'Auditório', 'Capela'),
(2, 1, 'Em frente (Cuidado! Escada subindo 10 m)', 12, 'Auditório', 'Escada 3'),
(30, 1, 'Vire a esquerda', 13, 'Escada 3', 'Lab.4'),
(5, 1, 'Em frente', 14, 'Lab.4', 'Lab.2'),
(3, 1, 'Em frente', 15, 'Lab.2', 'Escada 2'),
(2, 1, '2', 16, 'Escada 2', 'Lab.1'),
(2, 1, 'Em frente', 17, 'Lab.1', 'Lab.3'),
(10, 1, 'Vire a esquerda', 18, 'Lab.3', 'Lab.5'),
(15, 1, 'Em frente', 19, 'Lab.5', 'DAINF'),
(15, 1, 'Em frente', 20, 'DAINF', 'Lab.5'),
(10, 1, 'Em frente', 21, 'Lab.5', 'Lab.3'),
(2, 1, 'Vire a direita', 22, 'Lab.3', 'Lab.1'),
(2, 1, 'Em frente', 23, 'Lab.1', 'Escada 2'),
(3, 1, 'Em frente', 24, 'Escada 2', 'Lab.2'),
(5, 1, 'Em frente', 25, 'Lab.2', 'Lab.4'),
(30, 1, 'Em frente', 26, 'Lab.4', 'Escada 3'),
(2, 1, 'Vire a direita (Cuidado! Escada descendo 10m)', 27, 'Escada 3', 'Auditório'),
(20, 1, 'Em frente', 28, 'Escada 2', 'Capela'),
(20, 1, 'direita(Rampa 1) esquerda (Escada 3)', 29, 'Capela', 'Auditório'),
(35, 1, 'Em frente(Cuidado! Rampa mantenha a direita)', 30, 'Auditório', 'Rampa 1'),
(40, 1, 'Vire a direita', 31, 'Rampa 1', 'LaCA'),
(2, 1, 'Vire a direita(Cuidado! Escada descendo)', 32, 'LaCA', 'Escada 4'),
(5, 1, 'Em frente', 33, 'Escada 4', 'Lab.8'),
(10, 1, 'Em frente', 34, 'Lab.8', 'Lab.7'),
(20, 1, 'Em frente', 35, 'Lab.7', 'Lab.6'),
(2, 1, 'Vire a direita', 36, 'Lab.6', 'Escada 2'),
(20, 1, 'Em frente', 37, 'Escada 2', 'Capela'),
(5, 1, 'Vire a esquerda', 38, 'Capela', 'Escada 1'),
(100, 1, 'Em frente', 39, 'Escada 1', 'Portaria principal');

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `registro` int(30) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `senha` varchar(150) NOT NULL,
  `tipo_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `registro`, `nome`, `email`, `senha`, `tipo_usuario`) VALUES
(1, 21, 'murillo', 'murillo1@murillo.com', '01221dd8ff128adabdaddb9fb5436f63', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios_logados`
--

CREATE TABLE `usuarios_logados` (
  `id` int(11) NOT NULL,
  `token` varchar(500) NOT NULL,
  `registro` int(30) NOT NULL,
  `tipo_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `pontos`
--
ALTER TABLE `pontos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `segmentos`
--
ALTER TABLE `segmentos`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices para tabela `usuarios_logados`
--
ALTER TABLE `usuarios_logados`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `pontos`
--
ALTER TABLE `pontos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `segmentos`
--
ALTER TABLE `segmentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuarios_logados`
--
ALTER TABLE `usuarios_logados`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
