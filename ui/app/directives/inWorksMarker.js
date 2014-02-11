define(function () {
  return [
    'path', 'ajax',
    function (path, ajax) {
      return {
        restrict: 'E',
        templateUrl: 'directive.inWorksMarker',
        replace: true
      }
    }
  ]
})