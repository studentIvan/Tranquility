$.post("{{ panel_base_uri }}/ajax?csrf_token={{ csrf_token }}", { a: "get-site-size" })
    .done(function (data) {
        $("#space-info").html(data);
    });