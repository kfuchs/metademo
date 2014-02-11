define(function () {
  return [
    function () {
      return function (errors) {
        this.set = function (errors) {
          this.errors = errors === undefined ? {} : errors;
        };

        this.class = function (slug) {
          if(slug === undefined) {
            return this.isEmpty() ? '' : 'has-error';
          } else {
            return this.errors.hasOwnProperty(slug) ? 'has-error' : '';
          }
        };

        this.msg = function (slug) {
          if(this.errors.hasOwnProperty(slug)) return this.errors[slug].join(', ');
          else                                 return '';
        };

        this.clear = function (slug) {
          if(slug === undefined) this.errors = {};
          else                   delete(this.errors[slug]);
        }

        this.isEmpty = function () {
          for(var k in this.errors) {
            return false;
          }

          return true;
        }

        this.set(errors);
      };
    }
  ]
})