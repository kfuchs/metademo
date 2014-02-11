define(
[
  'angular',
  'app',
  'util/path',
  
  'text!tmpls/login.html',

  'text!tmpls/app.html',
  'text!tmpls/app/home.html',

  'text!tmpls/app/widget-tags.html',
  'text!tmpls/app/widget-tag/new.html',
  'text!tmpls/app/widget-tag/edit.html',

  'text!tmpls/app/manufacturers.html',
  'text!tmpls/app/manufacturer/new.html',
  'text!tmpls/app/manufacturer/edit.html',

  'text!tmpls/app/manufacturer/panel.html',
  'text!tmpls/app/manufacturer/widgets.html',
  'text!tmpls/app/manufacturer/widget/new.html',
  'text!tmpls/app/manufacturer/widget/edit.html',

  'text!tmpls/directives/in-works-marker.html',
  'text!tmpls/directives/uploader/videos.html',
  'text!tmpls/directives/uploader/photos.html'
],
function (
  angular, app, path,

  loginHtml,

  appHtml,
  appHomeHtml,

  appWidgetTagsHtml,
  appWidgetTagNewHtml,
  appWidgetTagEditHtml,

  appManufacturersHtml,
  appManufacturerNewHtml,
  appManufacturerEditHtml,

  appManufacturerPanelHtml,
  
  appManufacturerWidgetsHtml,
  appManufacturerWidgetNewHtml,
  appManufacturerWidgetEditHtml,

  directivesInWorksMarkerHtml,
  directivesUploaderVideosHtml,
  directivesUploaderPhotosHtml

) {

  var tmpl = path.tmpl;

  angular.module('demo').run(['$templateCache', function ($t) {
    $t.put('login', loginHtml);
    $t.put('app', appHtml);
    $t.put('app.home', appHomeHtml);

    $t.put('app.widgetTags', appWidgetTagsHtml);
    $t.put('app.widgetTag.new', appWidgetTagNewHtml);
    $t.put('app.widgetTag.edit', appWidgetTagEditHtml);
    
    $t.put('app.manufacturers', appManufacturersHtml);
    $t.put('app.manufacturer.new', appManufacturerNewHtml);
    $t.put('app.manufacturer.edit', appManufacturerEditHtml);

    $t.put('app.manufacturer.panel', appManufacturerPanelHtml);
    $t.put('app.manufacturer.widgets', appManufacturerWidgetsHtml);
    $t.put('app.manufacturer.widget.new', appManufacturerWidgetNewHtml);
    $t.put('app.manufacturer.widget.edit', appManufacturerWidgetEditHtml);

    $t.put('directive.inWorksMarker', directivesInWorksMarkerHtml);
    $t.put('directive.uploader.videos', directivesUploaderVideosHtml);
    $t.put('directive.uploader.photos', directivesUploaderPhotosHtml);
  }]);

})