var enviarMensagem = true;
var conversa = document.getElementById("chatAtivo");
var ultimaConversa = "";
conversa.scrollIntoView(false);

document.getElementById("enviar").addEventListener("submit", function(evt) {
    evt.preventDefault();

    var mensagemCliente = '<li class="clearfix admin_chat">\
                                <div class="chat-body1 clearfix">\
                                    <p align="right">{1}</p>\
                                </div>\
                            </li>';

    var mensagemBot = '<li class="left clearfix">\
                            <div class="chat-body1 clearfix resposta">\
                                <p>{1}</p>\
                            </div>\
                        </li>';

    if (enviarMensagem) {
        enviarMensagem = false;
        var texto = document.getElementById("textoEnviar").value;
        ultimaConversa = texto;
        document.getElementById("textoEnviar").value = "";
        var mensagem = mensagemCliente.replace('{1}', texto);
        mensagem += mensagemBot.replace('{1}', ' . . . ');

        conversa.innerHTML += mensagem;
        conversa.scrollIntoView(false);
        enviaParaApiAI(texto);
    } else {
        alert("Aguarde o retorno da mensagem!");
    }
    console.log(enviarMensagem);
});

function enviaParaApiAI(mensagem) {
    $.ajax({
        type: "GET",
        url: "https://api.api.ai/api/query?v=20150910&query=" + mensagem + "&lang=pt-br&sessionId=aeeebde1-fe92-4ec8-bad0-d085209021a6",
        dataType: "json",
        headers: {
            "Authorization": 'Bearer f67a45e0e6044494bf638ec4ef159ce0'
        },
        success: function(data) {
            console.log(data);
            console.log(data.result.fulfillment.speech);
            var mensagemResposta = document.getElementsByClassName("resposta")[0];
            mensagemResposta.innerHTML = '<p>' + data.result.fulfillment.speech + '</p>';
            mensagemResposta.classList.remove("resposta");
            enviarMensagem = true;
            conversa.scrollIntoView(false);
        },
        error: function(data) {
            console.log(data);
            var mensagemResposta = document.getElementsByClassName("resposta")[0].innerHTML = data.result.fulfillment.speech;
            mensagemResposta.classList.remove("resposta");
            enviarMensagem = true;
            conversa.scrollIntoView(false);
        }
    });
}

$(document).keyup(function(event) {
    if (event.which == 38) { 
        document.getElementById("textoEnviar").value = ultimaConversa;
    }
    else if (event.which == 40) { 
        document.getElementById("textoEnviar").value = "";
    }
});
