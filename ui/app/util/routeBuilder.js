define(function () {

  var recorded = {}

  var Record = function (name, config) {
    
    this.config = config;
    
    this.config.name = name;

    this.extend = function (config) {
      for(k in this.config) {
        if (! config.hasOwnProperty(k)) config[k] = this.config[k];
      }

      return config;
    } 

    this.nest = function (name, config) {
      return record(this.config.name+'.'+name, this.extend(config));
    }
  };

  var r = function(name, config) {
    if(config === undefined) return retrieve(name);

    return record(name, config);
  };

  var record = function (name, config) {
    recorded[name] = new Record(name, config);
    return recorded[name].config;
  };

  var retrieve = function (name) {
    return recorded[name];
  };

  var recorded = function () { return recorded; }

  return r;

})