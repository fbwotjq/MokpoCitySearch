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

});