(function () {
  var tbody = $(".result table tbody");
  var loading = $(".loading");

  var settings = {
    whitelist: [111, 222, 333, 555],
    pattern: /(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/gi,
  };

  var tagify = new Tagify(document.querySelector('input[name="q"]'), settings);

  $("#finder").on("submit", function (e) {
    e.preventDefault();

    var $q = $(this).find("input").val();

    DNSLookupService.call($q);
  });

  // Service
  var DNSLookupService = {
    active: false,
    call: function (data) {
      var self = this;
      var render = "";

      if (this.active != false) {
        this.active.abort();
      }
      
      loading.fadeIn();
      
      $('.res-error-msg').remove();

      self.active = $.post(
        "/",
        { form_data: data },
        function (res) {
          if (res.status == "error") {
            self.active = false;
            loading.fadeOut("fast", function () {
              $(".result").append(
                "<div class='res-error-msg'>Error: " + res.msg + "<button class='rm-err'>[x]</button></div>"
              );
            });
            return;
          }
          $.each(res.data, function (i, item) {
            render += "<tr>";
            render += "<td>" + item.type + "</td>";
            render += "<td>" + item.host + "</td>";
            render += "<td>" + item.ip + "</td>";
            render += "<td>" + item.ttl + " sec</td>";
            render += "</tr>";
          });
          loading.fadeOut("fast", function () {
            tbody.prepend(render).find("tr:first-child").hide().show("slow");
            self.active = false;
          });
        },
        "json"
      );
      //  $.post
    },
  };
    // Forgive me, Lord! :3
    $(document).on('click','.rm-err', function(){
        $(this).parent().remove();
    });
})();
