function setMyKeyword(keyword) {

    if(keyword != undefined && keyword != '' && keyword.replace(/^\s+|\s+$/gm, '') != '') {

        var myKeyword = $.cookie('my_keyword');
        if(myKeyword != undefined && myKeyword != '' && myKeyword.replace(/^\s+|\s+$/gm, '') != '') {
            var array = myKeyword.split(",");
            var hasKeyword = false;
            for (i = 0; i < array.length ; i++) {
                if(array[i] == keyword) {
                    hasKeyword = true;
                }
            }
            if(hasKeyword == false) {
                array.push(keyword);
                if(array.length > 5) {
                    array.shift();
                }
                $.cookie('my_keyword', array.toString());
            }
        } else {
            $.cookie('my_keyword', keyword);
        }

    }

}

function printMyKeyword() {

    var myKeyword = $.cookie('my_keyword');
    if(myKeyword != undefined && myKeyword != '' && myKeyword.replace(/^\s+|\s+$/gm,'') != '') {
        $('#myKeywordArea').empty();
        var array = myKeyword.split(",");
        for (var i = 0; i < array.length ; i++) {
            $('#myKeywordArea').append("<li><a href='" + array[i] + "'>" + array[i] + "</a><a href='" + array[i] + "' class='del'><span class='delText' id='" + array[i] + "'>삭제</span></a></li>");
        }
    } else {
        $('#myKeywordAreaDiv').hide();
    }
    delMyKeywordEventBinding();

    $('#myKeywordArea > li > a').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        var query = $(this).attr('href');
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        } else {
            $('#query').val(query);
        }

        $('#searchForm').submit();

    });

}

function delMyKeywordEventBinding() {

    $('.delText').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var keyword = $(this).attr('id');
        var myKeyword = $.cookie('my_keyword');
        var array = myKeyword.split(",");
        for (var i = 0; i < array.length ; i++) {
            if(keyword === array[i]) {
                array.splice(i, 1);
                break;
            }
        }
        if(array.length > 0) {
            $.cookie('my_keyword', array.toString());
        } else {
            $.cookie('my_keyword', '');
        }
        printMyKeyword();
    });

    $('.del').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var keyword = $(this).attr('href');
        var myKeyword = $.cookie('my_keyword');
        var array = myKeyword.split(",");
        for (var i = 0; i < array.length ; i++) {
            if(keyword === array[i]) {

            }
        }
        printMyKeyword();
    });
}

function goPage(number) {
    $('#startCount').val(number);
    var query = $('#query').val();
    if(query === '무엇이든 찾아보세요') {
        $('#query').val('');
    }
    $('#searchForm').submit();
}

$(document).ready(function() {

    $('#searchForm').submit(function(event) {

        event.preventDefault();
        var query = $('#query').val();
        setMyKeyword(query);
        this.submit();

    });

    $('#query').click(function() {
        var query = $(this).val();
        if(query === '무엇이든 찾아보세요') {
            $(this).val('');
        }
    });

    $('#searchButton').click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        var query = $('#query').val();
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        }
        $('#searchForm').submit();
    });

    $('#searchButtonImage').click(function(event) {
        event.preventDefault();
        event.stopPropagation();
        var query = $('#query').val();
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        }
        $('#searchForm').submit();
    });
    
    $('#Nav > ul > li > a').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var query = $('#query').val();
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        }
        var collection = $(this).attr('href');
        $('#collection').val(collection);
        $('#searchForm').submit();
    });

    $('.collectionDepts').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var query = $('#query').val();
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        }
        var collection = $(this).attr('href');
        $('#collection').val(collection);
        $('#searchForm').submit();
    });

    $('#sortRadioDiv > a').click(function (event) {
        event.preventDefault();
        event.stopPropagation();
        var query = $('#query').val();
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        }
        var rank = $(this).attr('href');
        $('#sortField').val(rank);
        $('#searchForm').submit();
    });

    $('#popkeywordArea > ul > li > a').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        var query = $('#query').val();
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        }

        var popKeywordType = $(this).attr('href');
        $('#popKeywordType').val(popKeywordType);
        $('#searchForm').submit();

    });

    $('#popkeywordArea > ol > li > a').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        var query = $(this).attr('href');
        $('#query').val(query);
        $('#searchForm').submit();

    }); 

    $("input[name=query]").keydown(function (event) {

        var query = $('#query').val();
        if(event.keyCode == 13) {
            event.preventDefault();
            if(query != undefined && query.replace(/^\s+|\s+$/gm, '') != '') {
                $('#searchForm').submit();
            } else {
                alert('검색어를 입력하여 주세요.');
            }
        }

    });

    printMyKeyword();

    $('#range1').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        $('#range1').attr('class', 'list1 on');
        $('#range2').attr('class', 'list2');
        $('#range3').attr('class', 'list3');
        $('#range4').attr('class', 'list4');
        var ranges = $(this).attr('href');
        var rangesSplits = ranges.split(',');
        $("#start_day").val(rangesSplits[0]);
        $("#end_day").val(rangesSplits[1]);
    });

    $('#range2').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        $('#range1').attr('class', 'list1');
        $('#range2').attr('class', 'list2 on');
        $('#range3').attr('class', 'list3');
        $('#range4').attr('class', 'list4');
        var ranges = $(this).attr('href');
        var rangesSplits = ranges.split(',');
        $("#start_day").val(rangesSplits[0]);
        $("#end_day").val(rangesSplits[1]);
    });

    $('#range3').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        $('#range1').attr('class', 'list1');
        $('#range2').attr('class', 'list2');
        $('#range3').attr('class', 'list3 on');
        $('#range4').attr('class', 'list4');
        var ranges = $(this).attr('href');
        var rangesSplits = ranges.split(',');
        $("#start_day").val(rangesSplits[0]);
        $("#end_day").val(rangesSplits[1]);
    });

    $('#range4').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        $('#range1').attr('class', 'list1');
        $('#range2').attr('class', 'list2');
        $('#range3').attr('class', 'list3');
        $('#range4').attr('class', 'list4 on');
        var ranges = $(this).attr('href');
        var rangesSplits = ranges.split(',');
        $("#start_day").val(rangesSplits[0]);
        $("#end_day").val(rangesSplits[1]);
    });

    $('#search_field_all').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        var searchField = $(this).attr('href');
        $('#searchField').val(searchField );
        $('#search_field_all').attr('class', 'list1 on');
        $('#search_field_title').attr('class', 'list2');
        $('#search_field_content').attr('class', 'list3');
        $('#search_field_writer').attr('class', 'list4');
        $('#searchForm').submit();

    });

    $('#search_field_title').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        var searchField = $(this).attr('href');
        $('#searchField').val(searchField );
        $('#search_field_all').attr('class', 'list1');
        $('#search_field_title').attr('class', 'list2 on');
        $('#search_field_content').attr('class', 'list3');
        $('#search_field_writer').attr('class', 'list4');
        $('#searchForm').submit();

    });

    $('#search_field_content').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        var searchField = $(this).attr('href');
        $('#searchField').val(searchField );
        $('#search_field_all').attr('class', 'list1');
        $('#search_field_title').attr('class', 'list2');
        $('#search_field_content').attr('class', 'list3 on');
        $('#search_field_writer').attr('class', 'list4');
        $('#searchForm').submit();

    });

    $('#search_field_writer').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        var searchField = $(this).attr('href');
        $('#searchField').val(searchField );
        $('#search_field_all').attr('class', 'list1');
        $('#search_field_title').attr('class', 'list2');
        $('#search_field_content').attr('class', 'list3');
        $('#search_field_writer').attr('class', 'list4 on');
        $('#searchForm').submit();

    });

    $('#start_day').glDatePicker({
        onClick: function(target, cell, date, data) {
            var month = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1));
            var dated = (date.getDate() + 1 < 10 ? '0' + (date.getDate() + 1) : (date.getDate() + 1));
            target.val(date.getFullYear() + '-' + month + '-' + dated);
            if(data != null) {
                alert(data.message + '\n' + dated);
            }
        }
    });

    $('#end_day').glDatePicker({
        onClick: function(target, cell, date, data) {
            var month = (date.getMonth() + 1 < 10 ? '0' + (date.getMonth() + 1) : (date.getMonth() + 1));
            var dated = (date.getDate() + 1 < 10 ? '0' + (date.getDate() + 1) : (date.getDate() + 1));
            target.val(date.getFullYear() + '-' + month + '-' + dated);
            if(data != null) {
                alert(data.message + '\n' + dated);
            }
        }
    });

    $('#start_day_img').click(function () {
        $('#start_day').click();
    });

    $('#end_day_img').click(function () {
        $('#start_day').click();
    });

    $('#dateApplyButton').click(function () {

        var startDate = $('#start_day').val();
        var endDate = $('#end_day').val();
        $('#startDate').val(startDate);
        $('#endDate').val(endDate);

    });

    $('#detailSearchClose').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        $('#detail_search_div').hide();

    });

    $('#detailSearchDo').click(function (event) {

        event.preventDefault();
        event.stopPropagation();

        var query = $('#query').val();
        if(query === '무엇이든 찾아보세요') {
            $('#query').val('');
        }

        $('#searchForm').submit();

    });

    $('#detailSearchCancel').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        $('#correct').val('');
        $('#surely').val('');
        $('#except').val('');

    });

    $('#detailSearchButton').click(function (event) {

        event.preventDefault();
        event.stopPropagation();
        if($("#detail_search_div").css("display") == "none") {
            $('#detail_search_div').show();
        } else {
            $('#detail_search_div').hide();
        }

    });

    $('#rechk').change(function () {

        if ($(this).is(":checked") == true) {
            var query = $('#query').val();
            $('#hiddenQuery').val(query);
            $('#query').val('');
        } else {
            var hiddenQuery = $('#hiddenQuery').val();
            $('#query').val(hiddenQuery);
            $('#hiddenQuery').val('');
        }
    });

});