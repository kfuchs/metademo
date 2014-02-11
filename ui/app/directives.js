define(
  [
    'angular',
    'services',

    'directives/pasteOnly',
    
    'directives/previewBox',
    'directives/youtubeVideoContainer',

    'directives/photoHolder',

    'directives/dropdown',
    'directives/modalWidget',

    'directives/uploader/photos',
    'directives/uploader/videos',

    'directives/box',

    'directives/inWorksMarker',

    'directives/scroller'
  ],
  function (
    angular,
    services,

    pasteOnly,

    previewBox,
    youtubeVideoContainer, // i am not "meant" to write front end code

    photoHolder,

    dropdown,

    modalWidget,

    photosUploader,
    videosUploader,

    box,

    inWorksMarker,

    scroller
  )
  {

    angular.module('demo.directives', ['demo.services'])
           .directive('pasteOnly', pasteOnly)

           .directive('previewBox', previewBox)
           .directive('youtubeVideoContainer', youtubeVideoContainer)

           .directive('photoHolder', photoHolder)

           .directive('dropdown', dropdown)

           .directive('modalWidget', modalWidget)

           .directive('photosUploader', photosUploader)
           .directive('videosUploader', videosUploader)

           .directive('box', box)

           .directive('inWorksMarker', inWorksMarker)

           .directive('scroller', scroller)

  }
)