$(function () {

    // change country selectbox
    $("#countrySel").on("change", function () {
        // loader start
        $('select').prop('disabled', 'disabled');
        $('body').css('background-color', '#ccc');

        $.post('/change/country', {
            change: 1,
            countryId: $(this).find('option:selected').attr("value")
        }, function (res) {
            // loader end
            $('select').prop('disabled', false);
            $('body').css('background-color', '#fff');

            if (!res) {
                console.log(res);
            }

            // reload regions
            var regionSelect = $('#regionSel');
            regionSelect.empty();
            $.each(res['regions'], function(k, v) {
                regionSelect.append($("<option></option>")
                    .attr("value", v.id)
                    .text(v.name));
            });

            // reload cities
            var citySelect = $('#citySel');
            citySelect.empty();
            $.each(res['cities'], function(k, v) {
                citySelect.append($("<option></option>")
                    .attr("value", v.id)
                    .text(v.name));
            });

            return false;
        });
    });

    // change region selectbox
    $("#regionSel").on("change", function () {
        // loader start
        $('select').prop('disabled', 'disabled');
        $('body').css('background-color', '#ccc');

        $.post('/change/region', {
            change: 1,
            regionId: $(this).find('option:selected').attr("value")
        }, function (res) {
            // loader end
            $('select').prop('disabled', false);
            $('body').css('background-color', '#fff');

            if (!res) {
                console.log(res);
            }

            // reload cities
            var citySelect = $('#citySel');
            citySelect.empty();
            $.each(res['cities'], function(k, v) {
                citySelect.append($("<option></option>")
                    .attr("value", v.id)
                    .text(v.name));
            });

            return false;
        });
    });

});