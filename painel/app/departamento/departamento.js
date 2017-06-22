utfbotdashboard.controller('DepartamentoCtrl', function($scope, $firebaseArray, cfpLoadingBar, $location, $http, $timeout) {
    console.log('DepartamentoCtrl');
    var nomeCategoria = "Departamento";


    $scope.item = {};
    $scope.itens;


    $timeout(function() {
        // Verifica se esta logado ou não no Firebase.
        if (firebase.auth().currentUser) {

            $scope.itens = $firebaseArray(firebase.database().ref().child(nomeCategoria));
            cfpLoadingBar.start();

            $scope.itens.$loaded(function(x) {
                cfpLoadingBar.complete();
            }, function(error) {
                console.error("Error:", error);
            });

            console.log("logado");
        } else {
            console.log("deslogado");
            $location.path("/login");
        }
    }, 750);

    $scope.registerItem = function(item) {
 
        $.ajax({
            type: "POST",
            url: "https://api.api.ai/v1/entities/a3753419-6ab5-4af0-986e-8f3dc53e0476/entries?v=20150910",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            headers: {
                "Authorization": 'Bearer 7e1337c672d5461ab9520a97c8b83ffc'
            },
            data: JSON.stringify({"value": item.sigla,"synonyms": item.sinonimo.split(',')}),

            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        });

        document.querySelector("#sigla").value = "";
        document.querySelector("#sinonimo").value = "";

        $scope.itens.$add(item);
        console.log(item);
    };


    //Chamado para remover o item desejado.
    $scope.removeItem = function(index) {
        console.log(index);

        var teste = '["' + $scope.itens[index].sigla + '"]';
        console.log(teste);
        //Remove a questão do banco.

        $.ajax({
            type: "DELETE",
            url: "https://api.api.ai/v1/entities/a3753419-6ab5-4af0-986e-8f3dc53e0476/entries?v=20150910",
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            headers: {
                "Authorization": 'Bearer 7e1337c672d5461ab9520a97c8b83ffc'
            },
            data: '["' + $scope.itens[index].sigla + '"]',

            success: function(data) {
                console.log(data);
            },
            error: function(data) {
                console.log(data);
            }
        });

        $scope.itens.$remove(index);
    };
});
