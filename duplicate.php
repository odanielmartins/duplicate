<?php

/*
		SCRIPT TO CLEAN A DIRECTORY TREE FROM EMPTY AND DUPLICATE FILESPROGRAMA
		=======================================================================
		
		You should give an input parameter containing the name of the directory root to work with. Example:
		php -f duplicate C:\PHOTOS
*/
$novasLinhas[] = "";

if($argc < 2)
	die("Please, give me the name of the root directory to clear.\n");

echo("===================== GENERATING THE LIST OF FILES INSIDE TREE DIRECTORY ==========================\n");
system("dir /S \"" . $argv[1] . "\" | findstr  \":\" | findstr /V \"<DIR>\" > dir.txt");

$linhas = file('dir.txt');									
$nLinhas = sizeof($linhas);

echo("===================== PREPARING AN ARRAY WITH A LIST OF ALL FILES =================================\n");
sleep(2);
$j = 0;
for($i = 0 ; $i < $nLinhas ; $i++)
{
	if(strpos($linhas[$i],"Pasta de") !== FALSE)				// É uma linha diretorio ?
		$dirAtual = LeDir($linhas[$i]);
	else
	{
		$tamNome = LeArq($linhas[$i]);
		$novasLinhas[$j] = $tamNome . " \""  . $dirAtual . "\"";
		$j++;
	}
}
sort($novasLinhas);
echo("===================== REMOVING EMPTY FILES ========================================================\n");
sleep(2);
$nLinhas = 0;
$vazios = 0;
for($i=0;$i<$j;$i++)
{
	if(substr($novasLinhas[$i],0,1) == '0')					// Arquivo vazio
	{
		$aux = preg_split("/[\"]/",$novasLinhas[$i]);
		$cmd = "del \"" . $aux[3] . "\\" .  $aux[1] . "\"\n";
		system($cmd);
		++$vazios;
	}
	else
	{
		$linhas[$nLinhas] = $novasLinhas[$i];
		$nLinhas++;
	}
}

echo("\n\n Eliminated " . $vazios . " empty files.\n\n");

//print_r($linhas);
echo("===================== REMOVING DUPLICATE FILES ======================================================\n");
sleep(2);
$i = 0;
$dups = 0;
for(;;)
{
	for($j = $i+1 ; $j < $nLinhas; $j++)
	{
		//echo($linhas[$i] . "\n");
		if(equalFiles($linhas[$i],$linhas[$j]))
		{
			echo($linhas[$i] . "\n");
			echo($linhas[$j] . " ==> DELETED.\n");
			$aux = preg_split("/[\"]/",$linhas[$j]);							// Separa pelo caracter aspas
			$cmd = "del \"" . $aux[3] . "\\" .  $aux[1] . "\"\n";				// Cria comando para deletar arquivo duplicado
			system($cmd);
			++$dups;
		}
		else
		{
			$i = $j;
			break;
		}
	}
	if(++$i > $nLinhas - 1)
		break;
}
echo("\n\nEliminated " . $dups . " duplicate files.\n");
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function equalFiles($str1,$str2)	{								// Compara arquivos quanto ao sem tamanho e nome
	
	$aux1 = preg_split("/[\"]/",$str1);								// Cria matriz separando pelo caracter aspas
	$tam1 = $aux1[0];
	$nome1 = $aux1[1];
	$aux2 = preg_split("/[\"]/",$str2);
	$tam2 = $aux2[0];
	$nome2 = $aux2[1];
	if( ($tam1 == $tam2) and ($nome1 == $nome2))
		return true;
	else
		return false;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function LeDir($linha)		{										// LEITURA DO NOME DO DIRETÓRIO a partir de uma string
	
	$dir = preg_split("/[\s]+/",$linha);							// split for any number of space characters
	$pos = strpos($linha,$dir[3]);									// descobre a posição do terceiro elemento = inicio do nome do diretório
	$dirAtual = substr($linha,$pos);								// guarda nome do diretório atual
	$dirAtual = preg_replace('/\R/', '', $dirAtual);				// retira o CR e LF do fim do nome do diretorio
	return $dirAtual;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function LeArq($linha)		{										// LEITURA DO NOME E TAMANHO DO ARQUIVO A PROCURAR POR UMA DUPLICATA
	
	$aux = preg_split("/[\s\t]+/",$linha);							// split file name and parameters
	$tamanho = $aux[2];
	$pos = strpos($linha,$aux[3]); 									// procura pelo inicio do nome do arquivo
	$nomeArq = substr($linha,$pos);		     						// le todo o resto da string menos o último character	
	$nomeArq = preg_replace('/\R/', '', $nomeArq);					//  menos o último character CR LF
	return $tamanho . " \"" . $nomeArq . "\"";
}
?>