# phpAPI-Inter-PIX
API PIX Banco Inter v2 com PHP

## Documentação Oficial do Banco Inter
https://developers.bancointer.com.br/reference/

## Apresentação 
Neste projeto é apresentado como consumir os Serviços de PIX da API Banco Inter v2, usando PHP  8.0.19, mas poderá funcionar em versões diferentes.

por Otávio Garrido.

## Configuração
Crie o banco de dados e crie as tabelas contidas no arquivo aplicacao_pix.sql
Ajuste o acesso no arquivo Database.php
Substitua seus arquivos certificado.crt e chave.key conforme zip disponibilizado do InternetBanking PJ

No arquivo Pix.php altere os campos:  
 "chavepix"     
 "client_id"    
 "client_secret"
 conforme informações disponibilizadas do InternetBanking PJ

Em minha máquina testei utilizando XAMPP, colocando tudo na pasta htdocs,
está pronto para gerar testes usando os arquivos:
teste_geraCobranca
teste_consultaCobranca
teste_consultaTudo
teste_devolver

De tempos em tempos execute o verificarPagamentoPix.php, ele irá confirmar os pix recebidos, ou expirar os vencidos.

para automarizar esta verificação no servidor, foi necessário um job
na pasta /etc/cron.d colocamos o arquivo cron_verificarPix

(edite ele com o caminho até o arquivo verificarPagamentoPix.php)

## Doação
### Doações via PIX / Donations PIX: 
#### Email: otavio.grrd@gmail.com



## Licença
GNU General Public License v3.0 [GNU General]



*obs copiei parte desse readme do Delmar de Lima(CortesDEV) https://github.com/delmardelima
