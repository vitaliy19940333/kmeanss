$(function() {

    $("#form_count_cluster").validate({

        rules: {
            count_cluster_hands: {
                required: true
            }
        },
        messages: {
            count_cluster_hands: {
                required: "Введите количество кластеров"
            }
		}
    });

});
