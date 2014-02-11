define(
['jquery'],
function ($) {
  var activeModal = null;

  return [
    '$document',
    function ($document) {
      return {
        restrict: 'A',

        link: function ($scope, $el, attrs) {

          var $launcher = $el.children('a[launcher]');
          var $modal = $el.children('div[modal]');

          $modal.addClass('modal').addClass('fade');

          if($modal.attr('large') !== undefined) {
            $modal.addClass('bs-modal-lg');
          }

          var closeModal = function ($modal) {
            $modal.css({display: 'none'})
                  .removeClass('in');

            activeModal = null;

            $document.find('body').css({
              overflow: 'auto'
            });

            $scope.$broadcast('modal-closing');
            $scope.$emit('modal-closing');
          };

          var openModal = function ($modal) {
            $scope.$broadcast('modal-opening');
            $scope.$emit('modal-opening');
            
            $modal.css({display: 'block'})
                  .addClass('in');

            activeModal = $modal.get(0);
            
            $document.find('body').css({
              overflow: 'hidden'
            });
          };

          $launcher.on('click', function (ev) {
            ev.preventDefault();

            if(activeModal !== null) {
              return; 
            }

            if($modal.hasClass('in')) {
              closeModal($modal);
            } else {
              openModal($modal);
            }
          });

          $modal.find('a[closer]').on('click', function (ev) {
            ev.preventDefault();

            closeModal($modal);
          });

          $modal.on('click', function (ev) {
            if(ev.target === this)
              closeModal($modal);
          });
        }
      }
    }
  ]
})