define(function () {
  return [
    function () {
      return function (mode) {
        var modes = {
          success: {class: 'has-success', msg: 'Success'},
          error: {class: 'has-error', msg: 'Error'},
          neutral: {class: '', msg: ''}
        }

        this.setMode = function (mode) {
          mode = modes.hasOwnProperty(mode) ? mode : 'neutral';
          var data = modes[mode];

          for (var k in data) {
            this[k] = data[k];
          }

          this.mode = mode;
        }

        // now just set up acc to supplied mode
        
        this.setMode(mode);
      }
    }
  ]
});