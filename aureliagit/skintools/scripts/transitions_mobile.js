$(document).ready(function() {


    var data = {};
    var go = 0;

    $("#page_1").click(function() {

        transition("main_1", "main_2");
        $("html, body").scrollTop(0);
    });

    // ***********
    // Question 1  
    // ***********  
    $("#page_2").click(function() {

        data[1] = $("#username").val();
        $("#question1").val(data[1]);
        transition("main_2", "main_3");
        $("html, body").scrollTop(0);
    });

    // ***********
    // Question 2  
    // *********** 
    $("#page_3").click(function() {

        if (go == 1) {

            // $('#main_3').addClass("blur");
            transition_side("main_4");
            go = 0;
        }
    });

    $("#page_3_mobile").click(function() {

        if (go == 1) {

            // $('#main_3').addClass("blur");
            transition("main_3", "main_4");
            $("html, body").scrollTop(0);
            go = 0;
        }
    });

    data[2] = "";
    var p3_array = [];
    var p3_counter = 0;

    $(".p3_images").click(function() {

        go = 1;
        // deselect();

        // var a = "";
        // var b = "";
        // var c = "";
        // var d = "";
        // var e = "";
        // var f = "";
        // var g = "";

        var divID = this.id;
        data[2] += divID + ",";

        // console.log("data = " + data[2]);

        // var count_housework = (data[2].match(/house_work/g) || []).length;
        // var count_shower = (data[2].match(/shower/g) || []).length;
        // var count_excercise = (data[2].match(/excercise/g) || []).length;
        // var count_children = (data[2].match(/children/g) || []).length;
        // var count_breakfast = (data[2].match(/breakfast/g) || []).length;
        // var count_social_media = (data[2].match(/social_media/g) || []).length;
        // var count_coffee = (data[2].match(/coffee/g) || []).length;


        // if (isOdd(count_housework)) {

        //     a = "house_work,";

        // } else {
        //     a = "";
        // }

        // if (isOdd(count_shower)) {

        //     b = "shower,";
        // } else {
        //     b = "";
        // }

        // if (isOdd(count_excercise)) {

        //     c = "excercise,";
        // } else {
        //     c = "";
        // }

        // if (isOdd(count_children)) {

        //     d = "children,";
        // } else {
        //     d = "";
        // }

        // if (isOdd(count_breakfast)) {

        //     e = "breakfast,";
        // } else {
        //     e = "";
        // }

        // if (isOdd(count_social_media)) {

        //     f = "social_media,";
        // } else {
        //     f = "";
        // }

        // if (isOdd(count_coffee)) {

        //     g = "coffee,";
        // } else {
        //     g = "";
        // }


        if ($('.' + divID + '_selected')[0]) {

            p3_counter--;
            $('.' + divID).removeClass(divID + '_selected');

            removeByIndex(p3_array, divID);
            console.log("p3_array = " + p3_array);

        } else if (p3_counter < 3) {

            p3_counter++;
            $('.' + divID).toggleClass(divID + '_selected');

            var idx = $.inArray(divID, p3_array);

            if (idx == -1) {

                p3_array.push(divID);
            } else {

                p3_array.splice(idx, 1);
            }

            console.log("p3_array = " + p3_array);

        }

        $("#question2").val(p3_array + ",");

    });

    function isOdd(num) {
        return num % 2;
    }

    function removeByIndex(arr, index) {

        arr.splice(index, 1);
    }

    // ***********
    // Question 3 
    // *********** 
    $("#page_4").click(function() {

        transition_sideout("main_3", "main_4", "main_5");
        data[3] = $("#feelingslider").slider("value");

        if (data[3] >= 66) {

            data[3] = 3;

        } else if (data[3] < 66 && data[3] > 33) {

            data[3] = 2;

        } else if (data[3] < 33) {

            data[3] = 1;
        }

        $("#question3").val(data[3]);
    });

    $("#page_4_mobile").click(function() {

        transition("main_4", "main_5");
        $("html, body").scrollTop(0);
        data[3] = $("#feelingslider_v").slider("value");

        if (data[3] >= 66) {

            data[3] = 3;

        } else if (data[3] < 66 && data[3] > 33) {

            data[3] = 2;

        } else if (data[3] < 33) {

            data[3] = 1;
        }

        $("#question3").val(data[3]);

    });

    $("#feelingslider").slider({
        orientation: "horizontal",
        range: "min",
        max: 100,
        value: 50,
        slide: function(event, ui) {

            console.log(ui.value);
            if (ui.value > 50) {
                $('#feelingslider span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            } else {
                $('#feelingslider span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            }
        }
    });

    $("#feelingslider_v").slider({
        orientation: "vertical",
        range: "min",
        max: 100,
        value: 50,
        slide: function(event, ui) {

            console.log(ui.value);
            if (ui.value > 50) {
                $('#feelingslider_v span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            } else {
                $('#feelingslider_v span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            }
        }
    });


    // ***********
    // Question 4
    // *********** 
    $("#page_5").click(function() {
        if (go == 1) {

            $('#main_5').addClass("blur");
            transition_side("main_6");
            go = 0;
        }
    });

    $("#page_5_mobile").click(function() {
        if (go == 1) {

            transition("main_5", "main_6");
            $("html, body").scrollTop(0);
            go = 0;
        }
    });

    $(".ragime_buttons").click(function() {

        go = 1;
        var divID = this.id;
        data[4] = divID;
        $("#question4").val(data[4]);
        deselect();
        $('#' + divID).css('border', '5px solid pink');
    });

    // ***********
    // Question 5
    // *********** 
    $("#page_6").click(function() {
        if (go == 1) {
            transition_sideout("main_5", "main_6", "main_7");
            go = 0;
        }
    });

    $("#page_6_mobile").click(function() {
        if (go == 1) {

            transition("main_6", "main_7");
            $("html, body").scrollTop(0);
            go = 0;
        }
    });

    $(".happy_with_butttons").click(function() {
        go = 1;
        deselect();
        var divID = this.id;
        data[5] = divID;
        $("#question5").val(data[5]);
        $('.' + divID).toggleClass(divID + '_selected');

    });

    // ***********
    // Question 6
    // *********** 
    $("#page_7").click(function() {

        var lovedata = [];
        var i = 0;

        $('#sortable2').each(function() {

            var d_data = '';

            // grabs selected data and adds it to array
            $(this).find('li').each(function() {
                var current = $(this);
                if (current.children().size() > 0) {
                    return true;
                }
                d_data = current.text();
                lovedata[i] = d_data;
                i++;
            });
        });
        data[6] = lovedata;
        $("#question6").val(data[6]);

        var un = $("#username").val();
        if (un.length > 0) {

            $('#dontworry').text("Don't worry " + un + ",");
        }

        if (lovedata.length > 0) {

            $('#main_7').addClass("blur");
            transition_side("main_8");
            go = 0;
        }
        printdata();
    });

    $("#page_7_mobile").click(function() {

        var lovedata = [];
        var i = 0;

        $('#sortable2').each(function() {

            var d_data = '';

            // grabs selected data and adds it to array
            $(this).find('li').each(function() {
                var current = $(this);
                if (current.children().size() > 0) {
                    return true;
                }
                d_data = current.text();
                lovedata[i] = d_data;
                console.log(lovedata);
                i++;
            });
        });
        data[6] = lovedata;
        $("#question6").val(data[6]);

        var un = $("#username").val();
        if (un.length > 0) {

            $('#dontworry').text("Don't worry " + un + ",");
        }

        // if (lovedata.length > 0) {

        transition("main_7", "main_8");
        $("html, body").scrollTop(0);
        go = 0;
        // }
        // printdata();
    });

    $("#page_8").click(function() {

        transition_sideout("main_7", "main_8", "main_9");
    });

    $("#page_8_mobile").click(function() {

        transition("main_8", "main_9");
        $("html, body").scrollTop(0);
    });

    // ***********
    // Question 7
    // *********** 
    $("#page_9").click(function() {

        if (go == 1) {
            $('#main_9').addClass("blur");
            transition_side("main_10");
            go = 0;
        }
    });

    $("#page_9_mobile").click(function() {

        if (go == 1) {
            transition("main_9", "main_10");
            $("html, body").scrollTop(0);
            go = 0;
        }
    });

    $(".p9_images").click(function() {

        go = 1;
        deselect();
        var divID = this.id;
        data[7] = divID;
        $("#question7").val(data[7]);
        $('.' + divID).toggleClass(divID + '_selected');
    });

    // ***********
    // Question 8
    // *********** 
    $("#page_10").click(function() {

        data[8] = $("#age_scroller option:selected").attr("value");
        $("#question8").val(data[8]);
        transition_sideout("main_9", "main_10", "main_11");
    });

    $("#page_10_mobile").click(function() {

        data[8] = $("#age_scroller option:selected").attr("value");
        $("#question8").val(data[8]);
        transition("main_10", "main_11");
        $("html, body").scrollTop(0);
    });

    // ***********
    // Question 9
    // *********** 
    $("#page_11").click(function() {

        $('#main_11').addClass("blur");
        transition_side("main_12");
        data[9] = $("#gifslider").slider("value");
        $("#question9").val(data[9]);
    });

    $("#page_11_mobile").click(function() {

        transition("main_11", "main_12");
        $("html, body").scrollTop(0);
        data[9] = $("#gifslider").slider("value");
        $("#question9").val(data[9]);
    });

    $("#gifslider").slider({
        orientation: "horizontal",
        range: "min",
        min: 1,
        max: 32,
        value: 16,
        slide: function(event, ui) {
            $('#gif_image').attr('src', 'img/pages/page11/flower/flower-' + ui.value + '.png');
        }
    });

    $("#page_12").click(function() {

        transition_sideout("main_11", "main_12", "main_13");
    });

    $("#page_12_mobile").click(function() {

        transition("main_12", "main_13");
        $("html, body").scrollTop(0);
    });

    // ***********
    // Question 10
    // *********** 
    $("#page_13").click(function() {

        if (go == 1) {

            $('#main_13').addClass("blur");
            transition_side("main_14");
            go = 0;
        }

    });
    $("#page_13_mobile").click(function() {

        if (go == 1) {

            transition("main_13", "main_14");
            $("html, body").scrollTop(0);
            go = 0;
        }

    });

    $(".p3_images_2").click(function() {

        go = 1;
        deselect();
        var divID = this.id;
        data[2] = divID;
        $("#question10").val(data[2]);
        $('.' + divID).toggleClass(divID + '_selected');
    });

    $(".p13_images").click(function() {

        go = 1;
        deselect();
        var divID = this.id;
        data[10] = divID;
        $("#question10").val(data[10]);
        $('.' + divID).toggleClass(divID + '_selected');
    });

    // ***********
    // Question 11
    // *********** 
    $("#page_14").click(function() {

        transition_sideout("main_13", "main_14", "main_15");
        data[11] = $("#stress").slider("value");
        $("#question11").val(data[11]);
    });

    $("#page_14_mobile").click(function() {

        transition("main_14", "main_15");
        $("html, body").scrollTop(0);
        data[11] = $("#stress_v").slider("value");
        $("#question11").val(data[11]);
    });

    $("#stress").slider({
        orientation: "horizontal",
        range: "min",
        min: 0,
        max: 100,
        value: 50,
        slide: function(event, ui) {

            if (ui.value > 50) {
                $('#stress span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page14/slider_button_red.png)');
            } else {
                $('#stress span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page14/sliderbutton.png)');
            }
        }
    });

    $("#stress_v").slider({
        orientation: "vertical",
        range: "min",
        min: 0,
        max: 100,
        value: 50,
        slide: function(event, ui) {

            if (ui.value > 50) {
                $('#stress_v span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page14/slider_button_red.png)');
            } else {
                $('#stress_v span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page14/sliderbutton.png)');
            }
        }
    });


    // ***********
    // Question 12
    // *********** 
    $("#page_15").click(function() {

        var ragime_buttons_2_data = [];

        for (var i = 1; i <= 7; i++) {

            if ($("#bag" + i).hasClass("ragime_buttons_2_hi")) {
                ragime_buttons_2_data.push("bag" + i);
            }
        }

        data[12] = "";
        data[12] += ragime_buttons_2_data + ",";
        $("#question12").val(data[12]);
        console.log(data[12]);

        if (go == 1) {
            $('#main_15').addClass("blur");
            transition_side("main_16");
            go = 0;
        }
    });

    $("#page_15_mobile").click(function() {

        var ragime_buttons_2_data = [];

        for (var i = 1; i <= 7; i++) {

            if ($("#bag" + i).hasClass("ragime_buttons_2_hi")) {
                ragime_buttons_2_data.push("bag" + i);
            }
        }

        data[12] = "";
        data[12] += ragime_buttons_2_data + ",";
        $("#question12").val(data[12]);
        console.log(data[12]);

        if (go == 1) {
            transition("main_15", "main_16");
                $("html, body").scrollTop(0);
            go = 0;
        }
    });

    $(".ragime_buttons_2").click(function() {

        go = 1;
        var divID = this.id;
        $('#' + divID).toggleClass('ragime_buttons_2_hi');
    });

    function unique(list) {
        var result = [];
        $.each(list, function(i, e) {
            if ($.inArray(e, result) == -1) result.push(e);
        });
        return result;
    }

    // ***********
    // Question 13
    // *********** 
    $("#page_16").click(function() {
        if (go == 1) {
            transition_sideout("main_15", "main_16", "main_17");
            go = 0;
        }
    });

    $("#page_16_mobile").click(function() {
        if (go == 1) {
            transition("main_16", "main_17");
                $("html, body").scrollTop(0);
            go = 0;
        }
    });

    $(".matters_buttons").click(function() {
        go = 1;
        deselect();
        var divID = this.id;
        data[13] = divID;
        $("#question13").val(data[13]);
        printdata();
        $(this).toggleClass('matters_buttons_select');
    });

    // ***********
    // Question 14
    // *********** 
    $("#page_17").click(function() {
        if (go == 1) {
            $('#main_17').addClass("blur");
            transition_side("main_18");
            go = 0;
        }
    });

    $("#page_17_mobile").click(function() {
        if (go == 1) {
            transition("main_17", "main_18");
                $("html, body").scrollTop(0);
            go = 0;
        }
    });

    $(".p17_images").click(function() {

        go = 1;
        deselect();
        var divID = this.id;
        data[14] = divID;
        $("#question14").val(data[14]);
        $('.' + divID).toggleClass(divID + '_selected');
    });

    // ***********
    // Question 15
    // *********** 
    $("#page_18").click(function() {

        transition_sideout("main_17", "main_18", "main_19");
        data[15] = $("#endofday").slider("value");
        $("#question15").val(data[15]);
    });

    $("#page_18_mobile").click(function() {

        transition
("main_18", "main_19");
            $("html, body").scrollTop(0);
        data[15] = $("#endofday_v").slider("value");
        $("#question15").val(data[15]);
    });

    $("#endofday").slider({
        orientation: "horizontal",
        range: "min",
        min: 1,
        max: 100,
        value: 50,
        slide: function(event, ui) {

            console.log(ui.value);
            if (ui.value > 50) {
                $('#endofday span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            } else {
                $('#endofday span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            }
        }
    });

    $("#endofday_v").slider({
        orientation: "vertical",
        range: "min",
        min: 1,
        max: 100,
        value: 50,
        slide: function(event, ui) {

            console.log(ui.value);
            if (ui.value > 50) {
                $('#endofday_v span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            } else {
                $('#endofday_v span.ui-slider-handle.ui-state-default.ui-corner-all').css('background-image', 'url(img/pages/page4/slider_button_dark.png)');
            }
        }
    });

    $("#page_19").click(function() {

        // transition("main_19", "main_20");
        $("#postdataform").submit();
        printdata();
    });

    function transition(p1, p2) {

        $("#" + p1).fadeOut(1000);
        $("#" + p2).fadeIn(1000);

        // fade with callback
        // $("#" + p1).fadeOut('slow', function() {
        //     $("#" + p2).fadeIn(1000);
        // });
    }

    function transition_side(p2) {

        $("#" + p2).animate({
            width: 'toggle'
        });
    }

    function transition_sideout(p1, p2, p3) {

        $("#" + p1).fadeOut(1000);
        $("#" + p2).fadeOut(1000);
        $("#" + p3).fadeIn(1000);
        // $("#" + p1 + "," + "#" + p2).fadeOut('slow', function() {
        //     $("#" + p3).fadeIn(1000);
        // });
    }

    function printdata() {

        console.log(data);
    }
});
