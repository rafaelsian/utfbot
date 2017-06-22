utfbotdashboard.controller('LoginCtrl', function($scope, $location, cfpLoadingBar) {
    $scope.msg = "";

    //Verifica se o usuario ja esta logado.
    if (firebase.auth().currentUser) {
        $location.path("/professor");
    }

    $scope.login = function(username, password) {
        if (username != null && password != null) {
            $scope.msg = "";
            cfpLoadingBar.start();

            firebase.auth().signInWithEmailAndPassword(username, password).then(function(firebaseUser) {
                console.log("Authenticated successfully with payload:");
                //console.log(JSON.stringify(authData));
                //$state.go("dashboard");
                cfpLoadingBar.complete();
                $location.path("/professor");
            }, function(error) {
                console.log("Error logging user in: ", error.code);
                cfpLoadingBar.complete();

                switch (error.code) {
                    case "INVALID_USER" || "INVALID_EMAIL":
                        $scope.msg = "E-mail inválido.";
                        $scope.$apply();
                        break;
                    case "INVALID_PASSWORD":
                        $scope.msg = "Senha inválida.";
                        $scope.$apply();
                        console.log(error);
                        break;
                    default:
                        $scope.msg = "Erro desconhecido (ver console).";
                        $scope.$apply();
                        console.log(error);
                        break;
                }

                console.log($scope.msg);
            });
        } else {
            cfpLoadingBar.complete();
            $scope.msg = "Preencha todos os campos.";
            $scope.$apply();
        }
    };
});
