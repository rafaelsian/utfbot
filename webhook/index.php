<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
include('crawler/simple_html_dom.php');

error_reporting(E_ALL);

header('Access-Control-Allow-Origin: *');

    /*
     * Configurações de Time Zone
     */ 
    date_default_timezone_set("America/Sao_Paulo");

    /*
     * Configurações do Banco de Dados
     */ 
    require 'vendor/autoload.php';

    use Kreait\Firebase\Configuration;
    use Kreait\Firebase\Firebase;

    function connectFirebase() {
    	$config = new Configuration();
    	$config->setFirebaseSecret("lpMTJaiKTPN5Tx4qJO0JfXwyKlh3tGy6DoSUt4uY");
    	$firebase = new Firebase("https://utfbot-165621.firebaseio.com", $config);
    	$tokenGenerator = $firebase->getConfiguration()->getAuthTokenGenerator();
    	$adminToken = $tokenGenerator->createAdminToken();
    	$firebase->setAuthOverride($adminToken);

    	return $firebase;
    }

    function getDadosProfessor($professor, $tipo) {

    	$firebase = connectFirebase();
    	$professores =  $firebase->getReference('Professores')->getData();

    	foreach($professores as $professor_key => $professorAux){
    		if ($professorAux["nome"] == $professor){
    			if($tipo == "escaninho"){
    				return $professorAux["escaninho"];
    			} 
    			else if($tipo == "email"){
    				return $professorAux["email"];
    			}
    			else if($tipo == "ramal"){
    				return $professorAux["ramal"];
    			}
    			else if($tipo == "departamento"){
    				return $professorAux["departamento"];
    			}
    		}
    	}

    	// return "não encontrado";
    }

    function getInfomacoesProfessor($professor) {

    	$firebase = connectFirebase();
    	$professores =  $firebase->getReference('Professores')->getData();

    	foreach($professores as $professor_key => $professorAux){
    		if ($professorAux["nome"] == $professor){
    			return "O(A) professor(a) " . $professorAux["nome"] . " possui titulação de " . $professorAux["titulacao"] . " e é do departamento " . $professorAux["departamento"] . ". Email: " . $professorAux["email"] . " - Ramal: " . $professorAux["ramal"];
    		}
    	}

    	// return "não encontrado";
    }

    function getInformacoesDepartamento($departamento) {

    	$firebase = connectFirebase();
    	$professores =  $firebase->getReference('Professores')->getData();
    	$count = 0;

    	foreach($professores as $professor_key => $professorAux){
    		if ($professorAux["departamento"] == $departamento){
    			$count++;
    		}
    	}

    	return $count;
    }

    function getInformacoesTitulacao($professor) {

    	$firebase = connectFirebase();
    	$professores =  $firebase->getReference('Professores')->getData();

    	foreach($professores as $professor_key => $professorAux){
    		if ($professorAux["nome"] == $professor){
    			return $professorAux["titulacao"];
    		}
    	}

    	// return "não encontrado";
    }

    function getInformacoesDepartamentoTitulacao($departamento, $titulacao) {

    	$firebase = connectFirebase();
    	$professores =  $firebase->getReference('Professores')->getData();
    	$count = 0;

    	foreach($professores as $professor_key => $professorAux){
    		if ($professorAux["departamento"] == $departamento && $professorAux["titulacao"] == $titulacao){
    			$count++;
    		}
    	}

    	return $count;
    }

    function getProfessores() {

    	$firebase = connectFirebase();
    	$professores =  $firebase->getReference('Professores')->getData();

    	$listaProfessores = '<table class="table table-bordered"><thead><tr><th>Nome</th><th>Departamento</th><th>Email</th></tr></thead><tbody>';
    	foreach($professores as $professor_key => $professorAux) {
    		$listaProfessores =  $listaProfessores . '<tr><td>' . $professorAux["nome"] . '</td><td>' .  $professorAux["departamento"] . '</td><td>' . $professorAux["email"] . '</td></tr>';
    	}
    	$listaProfessores = $listaProfessores . '</tbody></table>';


    	return $listaProfessores;
    }

    function getProgramas() {
    	$firebase = connectFirebase();
    	$programas =  $firebase->getReference('Programas')->getData();
    	$listaProgramas = "";

    	foreach($programas as $programa_key => $programaAux){
    		$listaProgramas = $listaProgramas . '<br>' . '• ' . $programaAux["nome"];
    	}

    	return $listaProgramas;
    }

    function getPrograma($programa) {
    	$firebase = connectFirebase();
    	$programas =  $firebase->getReference('Programas')->getData();

    	foreach($programas as $programa_key => $programaAux){
    		if ($programaAux["nome"] == $programa){
    			return '<b>' . $programaAux["nome"] . '</b><br><br>' . $programaAux["descricao"] . '<br><br>Mais informações: <a target="_blank" href="' . $programaAux["link"] . '">' . $programaAux["link"] . '</a>';
    		}
    	}

    	return "não encontrado";
    }

    /*
     * PROCESSANDO A MENSAGEM 
     * QUE CHEGA DO BOT
     */
    function processMessage($update) {
    	if($update["result"]["action"] == "professor"){
    		if($update["result"]["metadata"]["intentName"] == "Escaninho"){
    			$speech = "O escaninho do(a) professor(a) " . $update["result"]["parameters"]["Professores"] . " é: " . getDadosProfessor($update["result"]["parameters"]["Professores"], "escaninho");
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    		else if($update["result"]["metadata"]["intentName"] == "Email"){
    			$speech = "O email do(a) professor(a) " . $update["result"]["parameters"]["Professores"] . " é: " . getDadosProfessor($update["result"]["parameters"]["Professores"], "email");
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    		else if($update["result"]["metadata"]["intentName"] == "Ramal"){
    			$speech = "O ramal do(a) professor(a) " . $update["result"]["parameters"]["Professores"] . " é: " . getDadosProfessor($update["result"]["parameters"]["Professores"], "ramal");
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    		else if($update["result"]["metadata"]["intentName"] == "Departamento"){
    			$speech = "O departamento do(a) professor(a) " . $update["result"]["parameters"]["Professores"] . " é: " . getDadosProfessor($update["result"]["parameters"]["Professores"], "departamento");
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    		else if($update["result"]["metadata"]["intentName"] == "Informacoes_Professor"){
    			$speech = getInfomacoesProfessor($update["result"]["parameters"]["Professores"]);
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    		else if($update["result"]["metadata"]["intentName"] == "Professores"){
    			$speech = "Atualmente, temos os professores cadastrados:</p>" . getProfessores() . "<p>Você pode consultar as informações de um professor em específico digitando: Informações sobre o(a) Professor(a).";
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    	}
    	else if($update["result"]["action"] == "departamento"){
    		if($update["result"]["metadata"]["intentName"] == "Departamento_Professores"){
    			$speech = "O departamento " . $update["result"]["parameters"]["Departamento"] . " possui " . getInformacoesDepartamento($update["result"]["parameters"]["Departamento"]) . " professores.";
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    	}
    	else if($update["result"]["action"] == "titulacao"){
    		if($update["result"]["metadata"]["intentName"] == "Titulacao_Professores"){
    			$speech = "O(A) professor(a) " . $update["result"]["parameters"]["Professores"] . " possui título de " . getInformacoesTitulacao($update["result"]["parameters"]["Professores"]);
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    	}
    	else if($update["result"]["action"] == "departamento_titulacao"){
    		if($update["result"]["metadata"]["intentName"] == "Departamento_Titulacao_Professores"){
    			$speech = "O departamento " . $update["result"]["parameters"]["Departamento"] . " possui " . getInformacoesDepartamentoTitulacao($update["result"]["parameters"]["Departamento"], $update["result"]["parameters"]["Titulacao"]) . " professores que possuem título de " . $update["result"]["parameters"]["Titulacao"];
    			sendMessage(array(
    				"source" => $update["result"]["source"],
    				"speech" => $speech,
    				"displayText" => $speech,
    				"contextOut" => array()
    				));
    		}
    	}
    	else if ($update["result"]["metadata"]["intentName"] == "Cardapio"){
    		$speech = getCardapio();
    		sendMessage(array(
    			"source" => $update["result"]["source"],
    			"speech" => $speech,
    			"displayText" => $speech,
    			"contextOut" => array()
    			));
    	}
    	else if ($update["result"]["metadata"]["intentName"] == "smalltalk.agent.hungry") {
    		$speech = "Eu não sinto fome, mas você pode comer no RU hoje, o que acha?<br><br>" . getCardapio();
    		sendMessage(array(
    			"source" => $update["result"]["source"],
    			"speech" => $speech,
    			"displayText" => $speech,
    			"contextOut" => array()
    			));
    	}
    	else if ($update["result"]["metadata"]["intentName"] == "Programas") {
    		$speech = "";
    		if ($update["result"]["parameters"]["Programas"] == "") {
    			$speech = "Os Programas que existem na UTFPR são esses:<br>" . getProgramas() . "<br><br>Se você deseja saber mais sobre um Programa específico, escreva o nome dele.";
    		}
    		else {
    			$speech = getPrograma($update["result"]["parameters"]["Programas"]);
    		}

    		sendMessage(array(
    			"source" => $update["result"]["source"],
    			"speech" => $speech,
    			"displayText" => $speech,
    			"contextOut" => array()
    			));
    	}
    	else if ($update["result"]["metadata"]["intentName"] == "Certificados_Ano") {
    		$speech = 'Certificados do ano ' . $update["result"]["parameters"]["number"] . '</p>' . getEventosAno($update["result"]["parameters"]["number"]) . '<p>Para pegar um certificado, digite: "Certificado do(a) SEU NOME COMPLETO evento CÓDIGO DO EVENTO"';
    		sendMessage(array(
    			"source" => $update["result"]["source"],
    			"speech" => $speech,
    			"displayText" => $speech,
    			"contextOut" => array()
    			));
    	}
    	else if ($update["result"]["metadata"]["intentName"] == "Certificados_Nome") {
    		$linkCertificado = getEventosNome($update["result"]["parameters"]["number"], $update["result"]["parameters"]["nome"]);
    		$speech = 'Confira o seu certificado nesse <a target="_blank" href="' . $linkCertificado . '">LINK</a>';
    		sendMessage(array(
    			"source" => $update["result"]["source"],
    			"speech" => $speech,
    			"displayText" => $speech,
    			"contextOut" => array()
    			));
    	}
    	else if ($update["result"]["metadata"]["intentName"] == "Noticias") {
    		$speech = '<b>Notícias:</b><br>' . getNoticias();
    		sendMessage(array(
    			"source" => $update["result"]["source"],
    			"speech" => $speech,
    			"displayText" => $speech,
    			"contextOut" => array()
    			));
    	}
    }

    function getEventosAno($ano) {
    	$url = 'http://apl.utfpr.edu.br/extensao/certificados/listaPublica';
    	$data = array('txtCampus' => '13', 'txtAno' => $ano, 'txtEvento' => '');
// $data = array('txtCampus' => '13', 'txtAno' => '2017', 'txtEvento' => '540', 'hdnPesquisa' => 'pesquisa', 'cmbPesquisa' => 'D', 'txtPesquisa' => 'Rafael Sian');

  // use key 'http' even if you send the request to https://...
    	$options = array(
    		'http' => array(
    			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    			'method'  => 'POST',
    			'content' => http_build_query($data)
    			)
    		);
    	$context  = stream_context_create($options);
    	$result = file_get_contents($url, false, $context);
    	if ($result === FALSE) {
    		/* Handle error */ 
    	}
    	else
    	{
    		$html = str_get_html($result);
    		$listaEventos = '<table class="table table-bordered"><thead><tr><th>Nome do Evento</th><th>Código</th></tr></thead><tbody>';
    		foreach($html->find('select[name=txtEvento]') as $e) {
    			$e2 = $e->find('option');
    			for ($i=1; $i < count($e2); $i++) { 
    				$nomeEvento = $e2[$i]->plaintext;
                    // if (strlen($nomeEvento) > 20) {
                    //     $nomeEvento = substr($nomeEvento, 0, 20) . '...';
                    // }
    				$listaEventos =  $listaEventos . '<tr><td>' . $nomeEvento . '</td><td>' .  $e2[$i]->value . '</td></tr>';
    			}
    		}
    		$listaEventos = $listaEventos . '</tbody></table>';
    		return $listaEventos;
    	}
    }

    function getEventosNome($codigo, $nome) {
    	$url = 'http://apl.utfpr.edu.br/extensao/certificados/listaPublica';
        // $data = array('txtCampus' => '13', 'txtAno' => $ano, 'txtEvento' => '');
    	$data = array('txtCampus' => '13', 'txtAno' => '', 'txtEvento' => $codigo, 'hdnPesquisa' => 'pesquisa', 'cmbPesquisa' => 'D', 'txtPesquisa' => $nome);

  // use key 'http' even if you send the request to https://...
    	$options = array(
    		'http' => array(
    			'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
    			'method'  => 'POST',
    			'content' => http_build_query($data)
    			)
    		);
    	$context  = stream_context_create($options);
    	$result = file_get_contents($url, false, $context);
    	if ($result === FALSE) {
    		/* Handle error */ 
    	}
    	else
    	{
    		$html = str_get_html($result);

            //find all div tags with id=gbar
    		foreach($html->find('table#data_table') as $e)
    			return $e->find('a', 0)->href;
    	}
    }

    function getCardapio() {
    	$html = file_get_html('https://sistemas.cp.utfpr.edu.br/slogin/');

    	$cardapio = "";

    	$cardapio = $cardapio . "Data: " . $html->find('th', 0)->plaintext . '<br>';

    	foreach($html->find('td') as $e) {
    		$labels = $e->find('label');
    		for ($i=0; $i < count($labels); $i++) { 
    			if ($i == 0) {
    				$cardapio = $cardapio . $labels[$i]->plaintext . ': ';
    			}
    			else {
    				$cardapio = $cardapio . $labels[$i]->plaintext . ($i == count($labels) - 1 ? '<br>' : ', ');
    			}
    		}
    	}

        // echo $cardapio;

    	return $cardapio;
    }

    function getNoticias() {
    	$html = file_get_html('http://utfpr.edu.br/');

    	$not = $html->find('div.standard-topic');
    	$noticias = "";

    	for ($i=0; $i < count($not); $i++) { 
    		if ($i == 0) {
    			foreach($not[$i]->find('div.tileItem') as $e2) {
    				$noticias = $noticias . '<br><b><a target="_blank" href="' . $e2->find('a',0)->href . '">•' . $e2->find('p.tileBody', 0)->plaintext . '</a></b>';
                // echo $e2->find('p.tileBody', 0)->plaintext . ' - ' .  $e2->find('a',0)->href . '<br><br>';
    			}
    		}
    		else {
    			foreach($not[$i]->find('div.tileItem') as $e2) {
            // echo $e2->outertext;
    				$noticias = $noticias . '<br><b><a target="_blank" href="' . $e2->find('a', 0)->href . '">• ' . $e2->find('a', 0)->plaintext . '</a></b>';
                // echo $e2->find('a', 0)->plaintext . ' - ' .  $e2->find('a', 0)->href . '<br><br>';
    			}
    		}
    	}

    	return $noticias;
    }

    /*
     * FUNÇÃO PARA ENVIAR A MENSAGEM
     */
    function sendMessage($parameters) {
    	echo json_encode($parameters);
    }

    /*
     * PEGANDO A REQUISIÇÃO
     */
    $update_response = file_get_contents("php://input");
    $update = json_decode($update_response, true);
    if (isset($update["result"]["action"])) {
    	processMessage($update);
    }

    ?>