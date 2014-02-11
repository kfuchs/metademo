requirejs.config({
  baseUrl: location.protocol + '//' + location.host + '/app',
  // paths to different libs being used in the project
  // finally, the "dream" of using r.js to build a motherfucking
  // requirejs project might come to reality
  paths: {
    jquery: '../assets/js/jquery.min',
    uiBootstrap: '../assets/js/ui-bootstrap-tpls-0.6.0.min',
    angular: '../assets/js/angular.min',
    helpers: 'util/helpers',
    moment: '../assets/js/moment.min',
    config: './config',
    text: '../assets/js/text'
  },

  shim: {
    angular: {
      deps: ['jquery'],
      exports: 'angular'
    },
    uiBootstrap: {
      deps: ['angular']
    }
  }
});

requirejs(['jquery', 'angular', 'app', 'router'], function (jquery, angular, app, router) {
  angular.bootstrap(document, ['demo']);
});