-- =========================================================================================
-- Autor: Rômulo Menhô Barbosa
-- Data de Criacao: 19/04/2016
-- Descricao: Painel do Coordenador de Vinculada com projeto aguardando distribuicao para
--            parecerista.
-- =========================================================================================

IF OBJECT_ID ('vwPainelCoordenadorVinculadasAguardandoAnalise', 'V') IS NOT NULL
DROP VIEW vwPainelCoordenadorVinculadasAguardandoAnalise ;
GO

SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE VIEW dbo.vwPainelCoordenadorVinculadasAguardandoAnalise
AS
SELECT p.IdPRONAC, (p.AnoProjeto + p.Sequencial) AS NrProjeto, p.NomeProjeto,t.idProduto,r.Descricao AS Produto,t.stPrincipal, 
       b.Area AS idArea, a.Descricao AS Area,b.Segmento as idSegmento,c.Descricao AS Segmento,      
       t.idDistribuirParecer, t.idOrgao,t.FecharAnalise, 
	   t.DtEnvio as DtEnvioMincVinculada,DATEDIFF(DAY,t.DtEnvio,GETDATE()) AS qtDiasDistribuir,
	   (SELECT COUNT(*) FROM sac.dbo.PlanoDistribuicaoProduto y WHERE y.idProjeto = p.idProjeto AND stPrincipal = 0) AS QtdeSecundarios,
	   (SELECT SUM(x.Ocorrencia*x.Quantidade*x.ValorUnitario) FROM SAC.dbo.tbPlanilhaProjeto x WHERE p.IdPRONAC = x.idPRONAC and x.FonteRecurso = 109 and x.idProduto = t.idProduto) AS Valor 
FROM tbDistribuirParecer            AS t
INNER JOIN Projetos                 AS p ON t.idPRONAC  = p.IdPRONAC
INNER JOIN PlanoDistribuicaoProduto AS b ON p.idProjeto = b.idProjeto and t.idProduto = b.idProduto
INNER JOIN Produto                  AS r ON b.idProduto = r.Codigo
INNER JOIN Area                     AS a ON b.Area      = a.Codigo 
INNER JOIN Segmento                 AS c ON b.Segmento  = c.Codigo 
WHERE t.stEstado = 0 
      AND t.FecharAnalise= 0
      AND t.tipoanalise IN (3, 1) 
	  AND p.Situacao IN ('B11','B14') 
	  AND t.DtDistribuicao      IS NULL
	  AND t.DtDevolucao         IS NULL
	  AND t.idAgenteParecerista IS NULL
GO 

GRANT  SELECT  ON dbo.vwPainelCoordenadorVinculadasAguardandoAnalise  TO usuarios_internet
GO
