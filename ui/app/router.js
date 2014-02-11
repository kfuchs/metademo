define(
  ['angular', 'app', 'templates', 'util/routeBuilder', 'util/path', 'config'],
  function (angular, app, templates, r, path, config) {

    var page = path.page;
    var tmpl = path.tmpl;

    angular.module('demo').config([
      '$routeProvider', '$locationProvider', 
      function ($routeProvider, $locationProvider) {

        $routeProvider
          .when(
            page(config.loginPage),
            r('login', {
              title: 'Login',
              bodyTmpl: 'login'
            })
          )
          .when(
            page(config.homePage),
            r('app', {
              title: 'Home',
              bodyTmpl: 'app',
              panelTmpl: 'app.home'
            })
          )
          // widget tags
          .when(
            page('widget-tags'),
            r('app').nest('widget-tags', {title: 'Widget Tags', panelTmpl: 'app.widgetTags'})
          )
          .when(
            page('widget-tags/new'),
            r('app.widget-tags').nest('new', {title: 'New Widget Tag', panelTmpl: 'app.widgetTag.new'})
          )
          .when(
            page('widget-tags/:widgetTagId/edit'),
            r('app.widget-tags').nest('edit', {title: 'Edit Widget Tag', panelTmpl: 'app.widgetTag.edit'})
          )
          // manufacturers
          .when(
            page('manufacturers'),
            r('app').nest('manufacturers', {title: 'Manufacturers', panelTmpl: 'app.manufacturers'})
          )
          .when(
            page('manufacturers/new'),
            r('app.manufacturers').nest('new', {title: 'New Manufacturer', panelTmpl: 'app.manufacturer.new'})
          )
          .when(
            page('manufacturers/:manufacturerId/edit'),
            r('app.manufacturers').nest('edit', {title: 'Edit Manufacturers', panelTmpl: 'app.manufacturer.edit'})
          )
          // manufacturer widget
          .when(
            page('manufacturers/:manufacturerId/widgets'),
            r('app.manufacturers').nest(
              'widgets', {title: 'Manufacturer Widgets', panelTmpl: 'app.manufacturer.panel', subPanelTmpl: 'app.manufacturer.widgets'}
            )
          )
          .when(
            page('manufacturers/:manufacturerId/widgets/new'),
            r('app.manufacturers.widgets').nest('new', {title: 'New Manufacturer Widget', subPanelTmpl: 'app.manufacturer.widget.new'})
          )
          .when(
            page('manufacturers/:manufacturerId/widgets/:widgetId/edit'),
            r('app.manufacturers.widgets').nest('edit', {title: 'Edit Manufacturer Widget', subPanelTmpl: 'app.manufacturer.widget.edit'})
          )
          
          .otherwise({redirectTo: config.homePage})

        $locationProvider.html5Mode(true);
        
      }
    ])

  }
)