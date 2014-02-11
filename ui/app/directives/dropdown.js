define(
['jquery'],
function ($) {
  return [
    function (path) {
      return {
        restrict: 'A',

        link: function ($scope, $el, attrs) {

          $el.addClass('dropdown');
          $el.children('a[dropdown-toggle]:first').addClass('dropdown-toggle');
          $el.children('ul[dropdown-menu]').addClass('dropdown-menu');

        }
      }
    }
  ]
})