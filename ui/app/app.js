define(
  ['angular', 'uiBootstrap', 'services', 'controllers', 'directives'],
  function (angular, uiBootstrap, services, controllers, directives) {
    angular.module(
      'demo', [
        'ui.bootstrap',
        'demo.services',
        'demo.controllers',
        'demo.directives',
      ]
    );
  }
)