define(
  ['config'],
  function (config) {
    return {
      page: function (slug) {
        return config.pageBase + slug;
      },
      tmpl: function (slug) {
        return config.tmplBase + slug + '.html';
      },
      api: function (slug) {
        return config.apiBase + slug;
      },
      auth: function (slug) {
        return config.authBase + slug;
      }
    };
  }
);