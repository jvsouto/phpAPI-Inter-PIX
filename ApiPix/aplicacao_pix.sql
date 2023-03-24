
--
-- Estrutura da tabela `tb_token_pix_bb`
--
CREATE TABLE `tb_token_pix` (
  `expiracao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `token` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estrutura da tabela `tb_transacao_pix`
--
CREATE TABLE `tb_transacao_pix` (
  `txid` varchar(40) NOT NULL,
  `status` varchar(50) NOT NULL,
  `id_pedido` int(11) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `dthr_cri` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `dthr_exp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `e2eid` varchar(40) CHARACTER SET utf8 DEFAULT NULL,
  `valorDevolv` decimal(10,2) NOT NULL DEFAULT 0.00,
  `textoImagemQRcode` varchar(800) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Índices para tabela `tb_transacao_pix`
--
ALTER TABLE `tb_transacao_pix`
  ADD PRIMARY KEY (`txid`);
COMMIT;
