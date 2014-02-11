define(function () {
  return {
    type: function (val) {
      return Object.prototype.toString.call(val);
    }
  }
})