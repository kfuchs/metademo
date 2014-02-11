define(function () {
  return [
    '$timeout',
    function ($timeout) {
      var ctrlVCheck = function (ev) { return ev.ctrlKey && ev.keyCode === 86; };
      var delCheck = function (ev) { return ev.keyCode === 46; };

      return {
        restrict: 'A',
        
        link: function ($scope, $el, attrs) {
          $el.on('keydown', function (ev) {

            if(! ctrlVCheck(ev) && ! delCheck(ev)) {
              ev.preventDefault();
            }

            if(ctrlVCheck(ev)) {
              $timeout((function (el) {
                return function () { el.select(); };
              })(this), 50);
            }

          });

          $el.on('click', function (ev) {
            this.select();
          });
        }
      }
    }
  ]
})