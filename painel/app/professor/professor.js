utfbotdashboard.controller('ProfessorCtrl', function($scope, $firebaseArray, cfpLoadingBar, $location, $http, $timeout) {
    console.log('ProfessorCtrl');
    var nomeCategoria = "Professores";


    $scope.itemsList = ["Especialista", "Mestre", "Doutor(a)"];
    $scope.itemsListDepartamento = [];
    $scope.item = {};
    $scope.item.id = null;
    $scope.item.name = null;

    $scope.professores;


    $timeout(function() {
        // Verifica se esta logado ou não no Firebase.
        if (firebase.auth().currentUser) {

            $scope.professores = $firebaseArray(firebase.database().ref().child(nomeCategoria));
            cfpLoadingBar.start();

            $scope.professores.$loaded(function(x) {
                cfpLoadingBar.complete();
                getDepartamentos();
            }, function(error) {
                console.error("Error:", error);
            });

            console.log("logado");
        } else {
            console.log("deslogado");
            $location.path("/login");
        }
    }, 750);

    function getDepartamentos() {
        $.ajax({
            type: "GET",
            url: "https://api.api.ai/v1/entities/a3753419-6ab5-4af0-986e-8f3dc53e0476?v=20150910",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            headers: {
                "Authorization": 'Bearer 7e1337c672d5461ab9520a97c8b83ffc'
            },
            success: function(data) {
                for(var i = 0; i < data.entries.length; i++) {
                    $scope.itemsListDepartamento[i] = data.entries[i].value;
                }
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        });
    }

    $scope.registerQuestion = function(item) {
 
        $.ajax({
            type: "POST",
            url: "https://api.api.ai/v1/entities/5dff97b6-b041-46cb-bb2c-258a0e75f6f7/entries?v=20150910",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            headers: {
                "Authorization": 'Bearer 7e1337c672d5461ab9520a97c8b83ffc'
            },
            data: JSON.stringify({"value": item.nome,"synonyms": item.sinonimo.split(',')}),

            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        });

        document.querySelector("#nome").value = "";
        document.querySelector("#sinonimo").value = "";
        document.querySelector("#escaninho").value = "";
        document.querySelector("#email").value = "";
        document.querySelector("#departamento").value = "";
        document.querySelector("#ramal").value = "";

        $scope.professores.$add(item);
        console.log(item);
    };


    //Chamado para remover o professor desejado.
    $scope.removeProfessor = function(index) {
        console.log(index);

        var teste = '["' + $scope.professores[index].nome + '"]';
        console.log(teste);
        //Remove a questão do banco.

        $.ajax({
            type: "DELETE",
            url: "https://api.api.ai/v1/entities/5dff97b6-b041-46cb-bb2c-258a0e75f6f7/entries?v=20150910",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            headers: {
                "Authorization": 'Bearer 7e1337c672d5461ab9520a97c8b83ffc'
            },
            data: '["' + $scope.professores[index].nome + '"]',

            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        });

        $scope.professores.$remove(index);
    };
});
