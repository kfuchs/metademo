define(
  [
    'angular',
    'services',

    'controllers/master',
    'controllers/login',
    'controllers/app',

    'controllers/app/widgetTags',
    'controllers/app/widgetTag/new',
    'controllers/app/widgetTag/edit',

    'controllers/app/manufacturers',
    'controllers/app/manufacturer/new',
    'controllers/app/manufacturer/edit',

    'controllers/app/manufacturer/panel',

    'controllers/app/manufacturer/widgets',
    'controllers/app/manufacturer/widget/new',
    'controllers/app/manufacturer/widget/edit'
  ],
  function (
    angular, services,
    
    masterCtrl, loginCtrl, appCtrl,

    appWidgetTagsCtrl,
    appWidgetTagNewCtrl,
    appWidgetTagEditCtrl,

    appManufacturersCtrl,
    appManufacturerNewCtrl,
    appManufacturerEditCtrl,

    appManufacturerPanelCtrl,

    appManufacturerWidgetsCtrl,
    appManufacturerWidgetNewCtrl,
    appManufacturerWidgetEditCtrl
  )
  {

    angular.module('demo.controllers', ['demo.services'])
           .controller('masterCtrl', masterCtrl)
           .controller('loginCtrl', loginCtrl)
           .controller('appCtrl', appCtrl)

           .controller('app.widgetTagsCtrl', appWidgetTagsCtrl)
           .controller('app.widgetTag.newCtrl', appWidgetTagNewCtrl)
           .controller('app.widgetTag.editCtrl', appWidgetTagEditCtrl)

           .controller('app.manufacturersCtrl', appManufacturersCtrl)
           .controller('app.manufacturer.newCtrl', appManufacturerNewCtrl)
           .controller('app.manufacturer.editCtrl', appManufacturerEditCtrl)

           .controller('app.manufacturer.panelCtrl', appManufacturerPanelCtrl)
           .controller('app.manufacturer.widgetsCtrl', appManufacturerWidgetsCtrl)
           .controller('app.manufacturer.widget.newCtrl', appManufacturerWidgetNewCtrl)
           .controller('app.manufacturer.widget.editCtrl', appManufacturerWidgetEditCtrl)
    ;
  }
)