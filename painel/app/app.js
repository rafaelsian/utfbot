var utfbotdashboard = angular.module('UTFBotDashboard', ['firebase', 'ngRoute', 'ngAnimate', 'cfp.loadingBar']);

// Initialize Firebase
var config = {
    apiKey: "AIzaSyBoSlfBtmvIpam54g9QWywHHoN6JJHbsKY",
    authDomain: "utfbot-165621.firebaseapp.com",
    databaseURL: "https://utfbot-165621.firebaseio.com",
    projectId: "utfbot-165621",
    storageBucket: "utfbot-165621.appspot.com",
    messagingSenderId: "255586001374"
  };
firebase.initializeApp(config);

utfbotdashboard.run(function($rootScope, $location) {
    //Metodo para fazer Logout.
    $rootScope.logout = function() {
        firebase.unauth();
        $location.path("/login");
    };

    $rootScope.loggedIn = function() {
        if (firebase.auth().currentUser) {
            return true;
        }
        return false;
    };
});

utfbotdashboard.config(['$routeProvider', '$locationProvider', function($routeProvider, $locationProvider) {

    $routeProvider
        .when('/login', {
            templateUrl: 'app/login/login.html',
            controller: 'LoginCtrl'
        })
        .when('/professor', {
            templateUrl: 'app/professor/professor.html',
            controller: 'ProfessorCtrl'
        })
        .when('/departamento', {
            templateUrl: 'app/departamento/departamento.html',
            controller: 'DepartamentoCtrl'
        })
        .otherwise("/login");
}]);
