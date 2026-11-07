-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: database:3306
-- Tempo de geração: 07/11/2026 às 22:44
-- Versão do servidor: 8.0.46
-- Versão do PHP: 8.3.36

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `livraria`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `Assunto`
--

CREATE TABLE `Assunto` (
  `CodAs` int NOT NULL,
  `Descricao` varchar(20) NOT NULL,
  `CreatedAt` datetime NOT NULL,
  `UpdatedAt` datetime NOT NULL,
  `DeletedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Autor`
--

CREATE TABLE `Autor` (
  `CodAu` int NOT NULL,
  `Nome` varchar(40) NOT NULL,
  `CreatedAt` datetime NOT NULL,
  `UpdatedAt` datetime NOT NULL,
  `DeletedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Livro`
--

CREATE TABLE `Livro` (
  `CodL` int NOT NULL,
  `Titulo` varchar(40) NOT NULL,
  `Editora` varchar(40) NOT NULL,
  `Edicao` int NOT NULL,
  `AnoPublicacao` smallint NOT NULL,
  `Valor` decimal(10,2) NOT NULL,
  `CreatedAt` datetime NOT NULL,
  `UpdatedAt` datetime NOT NULL,
  `DeletedAt` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Livro_Assunto`
--

CREATE TABLE `Livro_Assunto` (
  `Livro_CodL` int NOT NULL,
  `Assunto_CodAs` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `Livro_Autor`
--

CREATE TABLE `Livro_Autor` (
  `Livro_CodL` int NOT NULL,
  `Autor_CodAu` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estrutura stand-in para view `vw_livros_por_autor`
-- (Veja abaixo para a visão atual)
--
CREATE TABLE `vw_livros_por_autor` (
`autor_codigo` int
,`autor_nome` varchar(40)
,`livro_codigo` int
,`titulo` varchar(40)
,`editora` varchar(40)
,`edicao` int
,`ano_publicacao` smallint
,`valor` decimal(10,2)
,`assuntos` text
);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `Assunto`
--
ALTER TABLE `Assunto`
  ADD PRIMARY KEY (`CodAs`);

--
-- Índices de tabela `Autor`
--
ALTER TABLE `Autor`
  ADD PRIMARY KEY (`CodAu`);

--
-- Índices de tabela `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Índices de tabela `Livro`
--
ALTER TABLE `Livro`
  ADD PRIMARY KEY (`CodL`);

--
-- Índices de tabela `Livro_Assunto`
--
ALTER TABLE `Livro_Assunto`
  ADD PRIMARY KEY (`Livro_CodL`,`Assunto_CodAs`),
  ADD KEY `IDX_2F01B7437134DCF1` (`Livro_CodL`),
  ADD KEY `IDX_2F01B74364209C06` (`Assunto_CodAs`);

--
-- Índices de tabela `Livro_Autor`
--
ALTER TABLE `Livro_Autor`
  ADD PRIMARY KEY (`Livro_CodL`,`Autor_CodAu`),
  ADD KEY `IDX_412939417134DCF1` (`Livro_CodL`),
  ADD KEY `IDX_41293941B44F3F36` (`Autor_CodAu`);

--
-- Índices de tabela `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `Assunto`
--
ALTER TABLE `Assunto`
  MODIFY `CodAs` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Autor`
--
ALTER TABLE `Autor`
  MODIFY `CodAu` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `Livro`
--
ALTER TABLE `Livro`
  MODIFY `CodL` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint NOT NULL AUTO_INCREMENT;

-- --------------------------------------------------------

--
-- Estrutura para view `vw_livros_por_autor`
--
DROP TABLE IF EXISTS `vw_livros_por_autor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`user`@`%` SQL SECURITY DEFINER VIEW `vw_livros_por_autor`  AS SELECT `a`.`CodAu` AS `autor_codigo`, `a`.`Nome` AS `autor_nome`, `l`.`CodL` AS `livro_codigo`, `l`.`Titulo` AS `titulo`, `l`.`Editora` AS `editora`, `l`.`Edicao` AS `edicao`, `l`.`AnoPublicacao` AS `ano_publicacao`, `l`.`Valor` AS `valor`, group_concat(`s`.`Descricao` order by `s`.`Descricao` ASC separator ', ') AS `assuntos` FROM ((((`Autor` `a` join `Livro_Autor` `la` on((`la`.`Autor_CodAu` = `a`.`CodAu`))) join `Livro` `l` on(((`l`.`CodL` = `la`.`Livro_CodL`) and (`l`.`DeletedAt` is null)))) left join `Livro_Assunto` `ls` on((`ls`.`Livro_CodL` = `l`.`CodL`))) left join `Assunto` `s` on(((`s`.`CodAs` = `ls`.`Assunto_CodAs`) and (`s`.`DeletedAt` is null)))) WHERE (`a`.`DeletedAt` is null) GROUP BY `a`.`CodAu`, `a`.`Nome`, `l`.`CodL`, `l`.`Titulo`, `l`.`Editora`, `l`.`Edicao`, `l`.`AnoPublicacao`, `l`.`Valor` ;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `Livro_Assunto`
--
ALTER TABLE `Livro_Assunto`
  ADD CONSTRAINT `FK_2F01B74364209C06` FOREIGN KEY (`Assunto_CodAs`) REFERENCES `Assunto` (`CodAs`),
  ADD CONSTRAINT `FK_2F01B7437134DCF1` FOREIGN KEY (`Livro_CodL`) REFERENCES `Livro` (`CodL`);

--
-- Restrições para tabelas `Livro_Autor`
--
ALTER TABLE `Livro_Autor`
  ADD CONSTRAINT `FK_412939417134DCF1` FOREIGN KEY (`Livro_CodL`) REFERENCES `Livro` (`CodL`),
  ADD CONSTRAINT `FK_41293941B44F3F36` FOREIGN KEY (`Autor_CodAu`) REFERENCES `Autor` (`CodAu`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
