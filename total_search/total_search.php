<?php
header("pragma: no-cache");
require_once "./common/queryapi530.html";
require_once "./common/WNUtils.html";
require_once "./common/WNSearch.html";
date_default_timezone_set('Asia/Seoul');

$collectionMappingDefine = array(
    'ALL' => array(
        array(
            'collectionName' => 'board',
            'viewCount' => 3,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDTITLE,URL,ALIAS'
        ),
        array(
            'collectionName' => 'infosearch',
            'viewCount' => 3,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,PATHSTRING,PATHURL,ALIAS'
        ),
        array(
            'collectionName' => 'member',
            'viewCount' => 5,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,DEPTTEL,DEPTPOS,ID,POSITION,ALIAS'
        ),
        array(
            'collectionName' => 'menu',
            'viewCount' => 5,
            'defaultSearchField' => 'TITLE,CONTENT',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,URL,ALIAS'
        ),
        array(
            'collectionName' => 'minwon',
            'viewCount' => 3,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER,ORIGINAL_NAME',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,URL,ORIGINAL_NAME,ATTACH_LINK,ALIAS'
        ),
        array(
            'collectionName' => 'multi',
            'viewCount' => 6,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,RENAME,URL,MENU_NAME,ALIAS'
        ),
        array(
            'collectionName' => 'webpage',
            'viewCount' => 3,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,PATHSTRING,PATHURL,ALIAS'
        )
    ),
    'board' => array(
        array(
            'collectionName' => 'board',
            'viewCount' => 10,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDTITLE,URL,ALIAS'
        )
    ),
    'infosearch' => array(
        array(
            'collectionName' => 'infosearch',
            'viewCount' => 10,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,PATHSTRING,PATHURL,ALIAS'
        )
    ),
    'member' => array(
        array(
            'collectionName' => 'member',
            'viewCount' => 10,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,DEPTTEL,DEPTPOS,ID,POSITION,ALIAS'
        )
    ),
    'menu' => array(
        array(
            'collectionName' => 'menu',
            'viewCount' => 15,
            'defaultSearchField' => 'TITLE,CONTENT',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,URL,ALIAS'
        )
    ),
    'minwon' => array(
        array(
            'collectionName' => 'minwon',
            'viewCount' => 10,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER,ORIGINAL_NAME',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,URL,ORIGINAL_NAME,ATTACH_LINK,ALIAS'
        )
    ),
    'multi' => array(
        array(
            'collectionName' => 'multi',
            'viewCount' => 12,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,RENAME,URL,MENU_NAME,ALIAS'
        )
    ),
    'webpage' => array(
        array(
            'collectionName' => 'webpage',
            'viewCount' => 10,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,PATHSTRING,PATHURL,ALIAS'
        )
    )
);

$isDebug = false;
$search = new Search();
$wnUtils = new WNUtils();

$collection  = $wnUtils->getCheckReq($_GET, "collection", "ALL");				    // 검색 대상 (전체 ALL)
$query = $wnUtils->getCheckReq($_GET, "query", "");							        // 검색어
$hiddenQuery = $wnUtils->getCheckReq($_GET, "hiddenQuery", "");						// 결과내 재검색시
$startCount	= $wnUtils->getCheckReq($_GET, "startCount", 0);						// 검색 요청할 시작 페이지인덱스
$sortField	= $wnUtils->getCheckReq($_GET, "sortField", "RANK");					// 검색 정렬 대상 필드
$popKeywordType	= $wnUtils->getCheckReq($_GET, "popKeywordType", "D");				// 인기검색어 선택 필드
$exceptKeyword = $wnUtils->getCheckReq($_GET, "exceptKeyword", "");
$mustKeyword = $wnUtils->getCheckReq($_GET, "mustKeyword", "");

$startDate = $wnUtils->getCheckReq($_GET, "startDate", "1980-01-01");				// 날짜 선택 필드
$startDate = str_replace("-", "/", $startDate);
$endDate = $wnUtils->getCheckReq($_GET, "endDate", "2030-12-31");				    // 날짜 선택 필드
$endDate = str_replace("-", "/", $endDate);

$isDetailSearch  = $wnUtils->getCheckReq($_GET, "isDetailSearch", 0);				// 상세검색 안할경우 0, 할경우 1
$searchField = $wnUtils->getCheckReq($_GET, "searchField", "ALL");					// 검색필드 설정

$ret = $search->w3SetCodePage(CHARSET);
$ret = $search->w3SetQueryLog(USE_QUERY_LOG_ON);

$query = $query . ($hiddenQuery == '' ? '' : ' ' . $hiddenQuery);
$realQuery = $query . (strcmp($exceptKeyword, "") == 0 ? '' : ' !' .$exceptKeyword);
$realQuery = $query . (strcmp($mustKeyword, "") == 0 ? '' : ' "' . $mustKeyword . '" ' );
$ret = $search->w3SetCommonQuery($realQuery, COMMON_OR_WHEN_NORESULT_OFF);

$currentCollectionMapping = $collectionMappingDefine[$collection];

foreach ($currentCollectionMapping as $value) {

    $collectionName = $value['collectionName'];
    $viewCount = $value['viewCount'];
    $sortCondition = $wnUtils->getSortCondition($sortField);
    $searchFieldCondition = $searchField == 'ALL' ? $value['defaultSearchField'] : $searchField;
    $documentField = $value['documentField'];

    #echo(printf('SETTING SEARCH CONDITION => collectionName:%s, viewCount:%s, sortField:%s, documentField:%s<br/>', $collectionName, $viewCount, $sortCondition, $documentField));

    $ret = $search->w3AddCollection($collectionName);
    $ret = $search->w3SetSearchField($collectionName, $searchFieldCondition);
    $ret = $search->w3SetDateRange($collectionName, $startDate, $endDate);
    $ret = $search->w3SetQueryAnalyzer($collectionName, USE_LA_ON, IGNORE_CASE_ON, USE_ORIGINAL_ON, USE_SYNONYM_ON);
    $ret = $search->w3SetHighlight($collectionName, USE_HIGHLIGHT_ON, USE_SNIPPET_ON);
    $ret = $search->w3SetPageInfo($collectionName, $startCount, $viewCount);
    $ret = $search->w3SetSortField($collectionName, $sortCondition);
    $settingDocumentField = str_replace('CONTENT', 'multi' == $collectionName ? 'CONTENT/10' : 'CONTENT/300', $documentField);
    #echo $collectionName . ' => ' .$settingDocumentField . ' : ' . $documentField .  '<br/>';
    $ret = $search->w3SetDocumentField($collectionName, $settingDocumentField);
    $ret = $search->w3SetHighlight($collectionName, USE_HIGHLIGHT_ON, USE_SNIPPET_ON);

}

$ret = $search->w3SetTraceLog(3);
$ret = $search->w3ConnectServer(SEARCH_IP, SEARCH_PORT, SEARCH_TIMEOUT);
$ret = $search->w3ReceiveSearchQueryResult(CONNECTION_CLOSE);

$totalSearchCount = 0;
$resultTotalSetDocument = array();
foreach ($currentCollectionMapping as $value) {

    $collectionName = $value['collectionName'];
    $collectionDocumentFieldArray = explode(",", $value['documentField']);
    //echo implode("|",$collectionDocumentFieldArray) . '<br/>';

    $collectionTotalCount = $search->w3GetResultTotalCount($collectionName);
    $collectionResultCount = $search->w3GetResultCount($collectionName);

    $resultTotalSetDocument[$collectionName . 'TotalCount'] = $collectionTotalCount;
    $resultTotalSetDocument[$collectionName . 'ResultCount'] = $collectionResultCount;

    $resultCollectionSetDocumentArray = array();
    #echo $collectionName . ' - ' . $collectionResultCount .  ' - ' . $collectionTotalCount . '<br/>';
    for( $i = 0 ; $i < $collectionResultCount ; $i++ ) {
        $resultCollectionSetDocument = array();
        foreach($collectionDocumentFieldArray as $collectionDocumentField) {
            #echo $collectionName . ' - ' . $collectionResultCount .  ' - ' . $collectionTotalCount  .  ' - ' . $collectionDocumentField . '<br/>';
            $columnValue = $search->w3GetField($collectionName, $collectionDocumentField, $i);
            $resultCollectionSetDocument[$collectionDocumentField] = str_replace('false', ' - ', $columnValue);

        }
        array_push($resultCollectionSetDocumentArray, $resultCollectionSetDocument);
    }
    $resultTotalSetDocument[$collectionName] = $resultCollectionSetDocumentArray;

    $totalSearchCount += $collectionTotalCount;
    #echo(printf('SEARCH RESULT => collectionName:%s, totalCount:%s, resultCount:%s <br/>', $collectionName, $collectionTotalCount, $collectionResultCount));

}

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "dev.truetech.info:7800/manager/WNRun.do?target=popword&collection=_ALL_&range=" . $popKeywordType);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
curl_close($ch);
$xml = simplexml_load_string($output);
$popkeywords = $xml->Query;

if($search->w3GetError() !=0) {
    $debugMsg = "ERROR : ".$search->w3GetErrorInfo()."<br/>\n";
    echo $debugMsg . '<br/>';
}

?>
<!DOCTYPE html>
<html lang="ko">
<head>
<title>목포시 통합검색</title>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta name="viewport" content="user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width">
<link rel="dns-prefetch" href="//fonts.googleapis.com" />
<link rel="dns-prefetch" href="//fonts.gstatic.com" />
<link rel="stylesheet" type="text/css" href="http://mp.mx.co.kr/style/common/common.css" /> <!-- 반영할때 확인 해야함 -->
<link rel="stylesheet" type="text/css" href="/total_search/style/total_search.css" />
<link rel="stylesheet" type="text/css" href="/total_search/style/glDatePicker.default.css" />
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="/total_search/js/jquery.cookie.js"></script>
<script src="/total_search/js/glDatePicker.min.js"></script>
<script src="/total_search/js/search.js"></script>
</head>
<body>
<dl id="skiptoContent">
  <dt><strong class="hidden">바로가기 메뉴</strong></dt>
  <dd><a href="#Nav" class="accessibility01">주요메뉴 바로가기</a></dd>
  <dd><a href="#container" class="accessibility02">본문내용 바로가기</a></dd>
</dl>
<!--wrap s-->
<div id="wrap" class="group">
	<div id="Nav">
    	<ul class="nav_list">
        	<li class="nav_total"><a href="ALL" <?php if($collection == 'ALL') { ?>class="on"<?php } ?>><span>통합검색</span></a></li>
            <li class="nav_menu"><a href="menu" <?php if($collection == 'menu') { ?>class="on"<?php } ?>><span>메뉴검색</span></a></li>
            <li class="nav_news"><a href="board" <?php if($collection == 'board') { ?>class="on"<?php } ?>"><span>목포소식</span></a></li>
            <li class="nav_staff"><a href="member" <?php if($collection == 'member') { ?>class="on"<?php } ?>><span>직원/업무</span></a></li>
            <li class="nav_area"><a href="webpage" <?php if($collection == 'webpage') { ?>class="on"<?php } ?>><span>분야별정보</span></a></li>
            <li class="nav_media"><a href="multi" <?php if($collection == 'multi') { ?>class="on"<?php } ?>><span>사진/동영상</span></a></li>
            <li class="nav_information"><a href="infosearch" <?php if($collection == 'infosearch') { ?>class="on"<?php } ?>><span>정보검색</span></a></li>
            <li class="nav_affairs_manual"><a href="minwon" <?php if($collection == 'minwon') { ?>class="on"<?php } ?>><span>민원사무편람</span></a></li>
        </ul>
        <div class="term">
        	<h2>기간</h2>
            <div class="range" id="dataRangeButton">
                <a id="range1" class="list1 on" title="전체" href=",">전체</a>
                <a id="range2" class="list2" title="1주" href="<?php echo date('Y-m-d', strtotime("-1 week")) ?>,<?php echo date('Y-m-d') ?>">1주</a>
                <a id="range3" class="list3" title="1개월" href="<?php echo date('Y-m-d', strtotime("-1 month")) ?>,<?php echo date('Y-m-d') ?>">1개월</a>
                <a id="range4" class="list4" title="1년" href="<?php echo date('Y-m-d', strtotime("-1 year")) ?>,<?php echo date('Y-m-d') ?>">1년</a>
            </div>
            <div class="date_search">
                <p>
                    <label class="blind" for="start_day">시작일</label>
                    <input id="start_day" class="hasDatepicker" type="text" readonly value="<?php echo $startDate == '' ? '시작일' : str_replace('/', '-', $startDate) ?>">
                    <img class="ui-datepicker-trigger" src="/total_search/images/calender.png" alt="달력" title="달력" style="width:33px; height:30px;" id="start_day_img">
                </p>
                <p>
                    <label class="blind" for="end_day">종료일</label>
                    <input id="end_day" class="hasDatepicker" type="text" readonly value="<?php echo $endDate == '' ? '종료일' : str_replace('/', '-', $endDate) ?>">
                    <img class="ui-datepicker-trigger" src="/total_search/images/calender.png" alt="달력" title="달력" style="width:33px; height:30px;" id="end_day_img">
                </p>
                <input class="date_apply" type="button" value="날짜적용" id="dateApplyButton">
            </div>
        </div>
        <div class="leftArea">
        	<h2>검색영역</h2>
            <div id="searchFieldSelectBox">
                <a id="search_field_all" class="list1<?php if($searchField == 'ALL') { ?> on<?php } ?>" href="ALL"><span>전체</span></a>
                <a id="search_field_title" class="list2<?php if($searchField == 'TITLE') { ?> on<?php } ?>" href="TITLE"><span>제목</span></a>
                <a id="search_field_content" class="list3<?php if($searchField == 'CONTENT') { ?> on<?php } ?>" href="CONTENT"><span>내용</span></a>
                <a id="search_field_writer" class="list4<?php if($searchField == 'WRITER') { ?> on<?php } ?>" href="WRITER"><span>작성자</span></a>
            </div>
        </div>
    </div>
    <!--container s-->
	<div id="container" class="group<?php if($totalSearchCount == 0) {?> noResult<?php } ?>">
    	<div id="top">
        	<div class="inner">
                <p class="logo"><span>건강의 섬 목포</span></p>
                <form id="searchForm" action="/total_search/total_search.html" method="get">
                <div class="top_search">
                    <fieldset>
                        <legend>통합검색</legend>
                        <input id="query" type="text" name="query" value="<?php echo $query == '' ? '무엇이든 찾아보세요' : $query ?>" name="query" autocomplete="off">
                        <input id="collection" name="collection" type="hidden" value="<?php echo $collection ?>">
                        <input id="sortField" name="sortField" type="hidden" value="<?php echo $sortField ?>">
                        <input id="searchField" name="searchField" type="hidden" value="<?php echo $searchField ?>">
                        <input id="popKeywordType" name="popKeywordType" type="hidden" value="<?php echo $popKeywordType ?>">
                        <input id="startDate" name="startDate" type="hidden" value="<?php echo $startDate ?>">
                        <input id="endDate" name="endDate" type="hidden" value="<?php echo $endDate ?>">
                        <input id="startCount" name="startCount" type="hidden" value="<?php echo $startCount ?>">
                        <input id="hiddenQuery" name="hiddenQuery" type="hidden" value="">
                        <label for="query"><a href="#none" id="searchButton"><img id="searchButtonImage" height="40" width="43" alt="검색" src="/total_search/images/search_icon.png"></a></label>
                        <a class="button_detail" id="detailSearchButton" href="#none"><span>상세검색</span></a>
                        <p>
                            <input id="rechk" type="checkbox" title="결과 내 재검색 하기" name="rechk">
                            <label for="rechk">결과 내 재검색</label>
                        </p>
                        <!--상세검색-->
                        <div class="detail_search" style="display:none;" id="detail_search_div">
                            <div class="detail_top">
                                <span class="dtit">상세검색</span>
                                <span class="dm">여러개의 단어를 입력할 때는 공백으로 구분해서 입력하세요.</span>
                                <span class="dclose"><a href="#none" id="detailSearchClose">닫기</a></span>
                            </div>
                            <fieldset class="detail_search_field">
                                <legend>상세검색</legend>
                                <!--<label for="correct">정확히 일치하는 단어/문장("")</label>
                                <input id="correct" name="correct" type="text" value=''>-->
                                <label for="mustKeyword">반드시 포함하는 단어(공백)</label>
                                <input id="mustKeyword" name="mustKeyword" type="text" value='<?php echo $mustKeyword ?>'>
                                <label for="exceptKeyword">제외하는 단어(!)</label>
                                <input id="exceptKeyword" name="exceptKeyword" type="text" value='<?php echo $exceptKeyword ?>'>
                            </fieldset>
                            <div class="detail_bottom">
                                <div class="btn">
                                    <input class="search" value="검색"  id="detailSearchDo"type="submit">
                                    <input class="cancel" value="초기화" type="reset" id="detailSearchCancel">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
                </form>
            </div>
        </div>
        <div id="content">
        	<div class="title<?php if($collection == 'ALL') {?> total_sch<?php } ?>">
                <p><span class="blue fw5">'<?php echo$query?>'</span>에 대한 전체 <span class="blue"><?php echo$totalSearchCount?></span>건의 결과를 찾았습니다.</p>
                <div id="sortRadioDiv" class="align">
                    <a class="list1<?php if($sortField == 'RANK'){?> on<?php } ?>" href="RANK"><span>정확도순</span></a>
                    <a class="list2<?php if($sortField == 'DATE'){?> on<?php } ?>" href="DATE"><span>최신순</span></a>
                </div>
            </div>
        	<!--result s-->
            <div class="result<?php if($collection == 'ALL') {?> total_sch<?php } ?>">
                <?php
                if($totalSearchCount == 0) {
                ?>
                <div class="result_no">
                    <p>'<?php echo $query ?>'에 대한<span>검색결과가 없습니다</span></p>
                    <ul>
                        <li>- 단어의 철자가 정확한지 확인해 보세요.</li>
                        <li>- 한글을 영어로 혹은 영어를 한글로 입력했는지 확인해 보세요.</li>
                        <li>- 검색어의 단어 수를 줄이거나, 보다 일반적인 검색어로 다시 검색해 보세요.</li>
                        <li>- 두 단어 이상의 검색어인 경우, 띄어쓰기를 확인해 보세요.</li>
                    </ul>
                </div>
                <?php
                }
                ?>
                <?php if(($collection == 'ALL' || $collection == 'menu') && array_key_exists('menuTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['menuTotalCount'] >  0) { ?>
                <div class="menu_search">
                    <h3>메뉴검색<span> [총 <?php echo array_key_exists('menuTotalCount', $resultTotalSetDocument) ? $resultTotalSetDocument['menuTotalCount'] : "0" ?>건]</span></h3>
                    <ul>
                        <?php
                        foreach ($resultTotalSetDocument['menu'] as $item) {
                        ?>
                            <li>
                                <?php
                                $urlPrefix = explode(".", $item['WRITER']);
                                echo str_replace('href="', 'href="/'.$urlPrefix[0], $item['URL']);
                                ?>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                    <?php if($collection == 'ALL') { ?><span class="more"><a class="collectionDepts" href="menu">+ 메뉴 더보기</a></span><?php } ?>
                    <?php if($collection != 'ALL' && array_key_exists('menuTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['menuTotalCount'] >  0) { ?>
                        <div class="paging">
                            <div class="num">
                                <?php echo $wnUtils->getNewPageLinks($startCount, $totalSearchCount, $viewCount, 10); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <?php if(($collection == 'ALL' || $collection == 'board') && array_key_exists('boardTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['boardTotalCount'] > 0) { ?>
                <div class="news_search">
                    <h3>목포소식 <span>[총 <?php echo array_key_exists('boardTotalCount', $resultTotalSetDocument) ? $resultTotalSetDocument['boardTotalCount'] : "0" ?>건]</span></h3>
                    <ul>
                        <?php
                        foreach ($resultTotalSetDocument['board'] as $item) {
                        ?>
                            <li>
                                <h4>
                                    <a href="<?php
                                    $urlPrefix = explode(".", ($item['URL']));
                                    echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['URL'])) . '?mode=view&idx=' . $item['DOCID'];
                                    ?>">
                                        <span class="menuName">[<?php echo $item['BOARDTITLE'] ?>]</span>
                                        <span class="tit"><span class="green"><?php echo $item['TITLE'] ?></span></span>
                                    </a>
                                    <span class="date">| <?php echo date("Y.m.d", strtotime($item['Date'])) ?></span>
                                    <a href="<?php
                                    $urlPrefix = explode(".", ($item['URL']));
                                    echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['URL'])) . '?mode=view&idx=' . $item['DOCID'];
                                    ?>" target="_blank" class="new_page">새창열기</a>
                                </h4>
                                <p><?php echo $item['CONTENT'] ?></p>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                    <?php if($collection == 'ALL') { ?><span class="more"><a class="collectionDepts" href="board">+ 목포소식 더보기</a></span><?php } ?>
                    <?php if($collection != 'ALL' && array_key_exists('boardTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['boardTotalCount'] > 0) { ?>
                        <div class="paging">
                            <div class="num">
                                <?php echo $wnUtils->getNewPageLinks($startCount, $totalSearchCount, $viewCount, 10); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <?php if(($collection == 'ALL'  || $collection == 'member') && array_key_exists('memberTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['memberTotalCount'] > 0) { ?>
                <div class="staff_table">
                    <h3>직원검색 <span>[총 <?php echo array_key_exists('memberTotalCount', $resultTotalSetDocument) ? $resultTotalSetDocument['memberTotalCount'] : "0"?>건]</span></h3>
                    <table>
                        <caption>직원업무안내표로 이름,부서,직책,업무,연락처 항목으로 구성</caption>
                        <thead>
                          <tr>
                            <th scope="col" style="width: 10%;">이름</th>
                            <th scope="col" style="width: 10%;">부서</th>
                            <th scope="col" style="width: 10%;">직책</th>
                            <th scope="col" style="width: 55%;">업무</th>
                            <th scope="col" style="width: 15%;">연락처</th>
                          </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($resultTotalSetDocument['member'] as $item) {
                        ?>
                        <tr>
                            <td><?php echo $item['TITLE'] ?></td>
                            <td><?php echo array_key_exists('POSITION', $item) ? $item['POSITION'] : "-" ?></td>
                            <td><?php echo $item['DEPTPOS'] ?></td>
                            <td><?php echo $item['CONTENT'] ?></td>
                            <td><?php echo array_key_exists('DEPTTEL', $item) ? $item['DEPTTEL'] : "-"  ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                        </tbody>
                    </table>
                    <?php if($collection == 'ALL') { ?><span class="more"><a class="collectionDepts" href="member">+ 직원/업무 더보기</a></span><?php } ?>
                    <?php if($collection != 'ALL' && array_key_exists('memberTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['memberTotalCount'] > 0) { ?>
                        <div class="paging">
                            <div class="num">
                                <?php echo $wnUtils->getNewPageLinks($startCount, $totalSearchCount, $viewCount, 10); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <?php if(($collection == 'ALL' || $collection == 'webpage') && array_key_exists('webpageTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['webpageTotalCount'] > 0) { ?>
                <div class="area_search">
                	<h3>분야별정보 <span>[총 <?php echo $resultTotalSetDocument['webpageTotalCount'] ?>건]</span></h3>
                    <ul>
                        <?php
                        foreach ($resultTotalSetDocument['webpage'] as $item) {
                        ?>
                        <li>
                            <h4>
                                <a href="<?php
                                    $urlPrefix = explode(".", ($item['PATHURL']));
                                    echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['PATHURL']))
                                ?>">
                                    <span class="menuName">[<?php echo str_replace("|", " > ", $item['PATHSTRING']) ?>]</span>
                                    <span class="tit"><span class="green"><?php echo $item['TITLE'] ?></span></span>
                                </a>
                                <span class="date">| <?php echo date("Y.m.d", strtotime($item['Date'])) ?></span>
                                <a href="<?php
                                $urlPrefix = explode(".", ($item['PATHURL']));
                                echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['PATHURL']))
                                ?>" target="_blank" class="new_page">새창열기</a>
                            </h4>
                            <p><?php echo $item['CONTENT'] ?></p>
                        </li>
                        <?php
                        }
                        ?>
                   	</ul>
                    <?php if($collection == 'ALL') { ?><span class="more"><a class="collectionDepts" href="webpage">+ 분야별정보 더보기</a></span><?php } ?>
                    <?php if($collection != 'ALL' && array_key_exists('webpageTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['webpageTotalCount'] > 0) { ?>
                        <div class="paging">
                            <div class="num">
                                <?php echo $wnUtils->getNewPageLinks($startCount, $totalSearchCount, $viewCount, 10); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <?php if(($collection == 'ALL' || $collection == 'multi') && array_key_exists('multiTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['multiTotalCount'] > 0) { ?>
                <div class="media">
                    <h3>사진/동영상 <span>[총 <?php echo $resultTotalSetDocument['multiTotalCount'] ?>건]</span></h3>
                    <ul>
                        <?php
                        if(array_key_exists('multiTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['multiTotalCount'] > 0) {
                            foreach ($resultTotalSetDocument['multi'] as $item) {
                        ?>
                        <li>
                            <a href="<?php
                            $urlPrefix = explode(".", ($item['URL']));
                            echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['URL'])) . '?mode=view&idx=' . $item['DOCID'];
                            ?>" target="_blank">
                            <span class="img">
                                <img src="<?php
                                    $imagePath = str_replace('_data', 'ybmodule.file', $item['RENAME']);
                                    if(strcmp($item['WRITER'], 'movie') == 0) {
                                        $imagePath .= ".gif";
                                    }
                                ?>" alt="<?php echo $item['TITLE']?>"
                                     onerror="this.src='/total_search/images/pho1.jpg'"/>
                                <?php if(strcmp($item['WRITER'], 'movie') == 0) {?><span class="play"></span><?php } ?>
                            </span>
                                <span class="menuName"><?php echo $item['MENU_NAME'] ?></span>
                                <span class="green"><?php echo $item['CONTENT'] ?></span>
                                <span class="date"><?php echo date("Y.m.d", strtotime($item['Date'])) ?></span>
                            </a>
                        </li>
                        <?php
                            }
                        }
                        ?>
                    </ul>
                    <?php if($collection == 'ALL') { ?><span class="more"><a class="collectionDepts" href="multi">+ 사진/동영상 더보기</a></span><?php } ?>
                    <?php if($collection != 'ALL' && array_key_exists('multiTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['multiTotalCount'] > 0) { ?>
                        <div class="paging">
                            <div class="num">
                                <?php echo $wnUtils->getNewPageLinks($startCount, $totalSearchCount, $viewCount, 10); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <?php if(($collection == 'ALL' || $collection == 'infosearch') && array_key_exists('infosearchTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['infosearchTotalCount'] > 0) { ?>
                <div class="information">
                	<h3>정보검색 <span>[총 <?php echo $resultTotalSetDocument['infosearchTotalCount']?>건]</span></h3>
                    <ul>
                        <?php
                        foreach ($resultTotalSetDocument['infosearch'] as $item) {
                        ?>
                    	<li>
                            <h4>
                                <a href="<?php
                                $urlPrefix = explode(".", ($item['PATHURL']));
                                echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['PATHURL'])) . '?mode=view&idx=' . $item['DOCID'];
                                ?>">
                                    <span class="tit">
                                        <span class="green"><?php echo $item['TITLE'] ?></span>
                                    </span>
                                </a>
                                <span class="date">| <?php echo date("Y.m.d", strtotime($item['Date'])) ?></span>
                                <a class="new_page" href="<?php
                                $urlPrefix = explode(".", ($item['PATHURL']));
                                echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['PATHURL'])) . '?mode=view&idx=' . $item['DOCID'];
                                ?>" target="_blank">새창열기</a>
                            </h4>
                            <p class="location"><a href="#none"><?php echo $item['WRITER'] . ' > ' . str_replace("|", " > ", $item['PATHSTRING']) ?></a></p>
                            <!--ul class="file_box">
                                <li class="hwp"><a href="#none">2017년 상반기 규제개혁위원회 회의 결과 1부.hwp</a></li>
                            </ul>-->
                            <p><?php echo $item['CONTENT'] ?></p>
                        </li>
                        <?php
                        }
                        ?>
                    </ul>
                    <?php if($collection == 'ALL') { ?><span class="more"><a class="collectionDepts" href="infosearch">+ 정보검색 더보기</a></span><?php } ?>
                    <?php if($collection != 'ALL' && array_key_exists('infosearchTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['infosearchTotalCount'] > 0) { ?>
                        <div class="paging">
                            <div class="num">
                                <?php echo $wnUtils->getNewPageLinks($startCount, $totalSearchCount, $viewCount, 10); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <?php if(($collection == 'ALL' || $collection == 'minwon') && array_key_exists('minwonTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['minwonTotalCount'] > 0) { ?>
                    <div class="affairs_manual">
                        <h3>민원사무편람 <span>[총 <?php echo array_key_exists('minwonTotalCount', $resultTotalSetDocument) ? $resultTotalSetDocument['minwonTotalCount'] : "0" ?>건]</span></h3>
                        <ul>
                            <?php
                            foreach ($resultTotalSetDocument['minwon'] as $item) {
                            ?>
                            <li>
                                <h4>
                                    <a href="<?php
                                    $urlPrefix = explode(".", ($item['URL']));
                                    echo 'http://' . str_replace('_80', '/' . $urlPrefix[0], str_replace('|', '/', $item['URL'])) . '?mode=view&idx=' . $item['DOCID'];
                                    ?>">
                                <span class="tit">
                                    <span class="green"><?php echo $item['TITLE'] ?></span>
                                </span>
                                    </a>
                                    <span class="date">| <?php echo date("Y.m.d", strtotime($item['Date'])) ?></span>
                                    <a class="new_page" href="#none" target="_blank">새창열기</a>
                                </h4>
                                <p><?php echo $item['CONTENT'] ?></p>
                                <?php if(array_key_exists('ORIGINAL_NAME', $item) && $item['ORIGINAL_NAME']) {?>
                                <ul class="file_box">
                                    <li class="hwp"><a href="<?php echo str_replace('_data', 'ybmodule.file', $item['ATTACH_LINK']) ?>">"<?php echo $item['ORIGINAL_NAME'] ?>"</a></li>
                                </ul>
                                <?php } ?>
                            </li>
                            <?php
                            }
                            ?>
                        </ul>
                        <?php if($collection == 'ALL') { ?><span class="more"><a class="collectionDepts" href="minwon">+ 정보검색 더보기</a></span><?php } ?>
                        <?php if($collection != 'ALL'&& array_key_exists('minwonTotalCount', $resultTotalSetDocument) && $resultTotalSetDocument['minwonTotalCount'] > 0) { ?>
                            <div class="paging">
                                <div class="num">
                                    <?php echo $wnUtils->getNewPageLinks($startCount, $totalSearchCount, $viewCount, 10); ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
			<!--result e-->
			<!--aside s-->
            <div id="aside">
            	<div class="popular" id="popkeywordArea">
                    <h3>인기검색어</h3>
                    <ul class="tab">
                        <li><a <?php if($popKeywordType == 'D') { ?> class="on"  <?php } ?> href="D">일간</a></li>
                        <li><a <?php if($popKeywordType == 'W') { ?> class="on"  <?php } ?> href="W">주간</a></li>
                    </ul>
                    <ol class="list">
                        <?php
                        for( $i = 0 ; $i < count($popkeywords) ; $i++ ) {
                        ?>
                        <li>
                            <a href="<?php echo $popkeywords[$i] ?>">
                                <span class="num<?php if($i < 5) { ?> top<?php } ?>"><?php echo $i + 1 ?></span>
                                <span class="tit"><?php echo $popkeywords[$i] ?></span>
                                <span class="rank">
                                    <?php
                                    if($popkeywords[$i]['updown'] == 'C') {
                                    ?>
                                        <em> - </em>
                                    <?php
                                    } else if ($popkeywords[$i]['updown'] == 'U') {
                                    ?>
                                        <em class="up"><?php echo $popkeywords[$i]['count'] ?></em>
                                    <?php
                                    } else if ($popkeywords[$i]['updown'] == 'D') {
                                    ?>
                                        <em class="dn"><?php echo $popkeywords[$i]['count'] ?></em>
                                    <?php
                                    } else if ($popkeywords[$i]['updown'] == 'N') {
                                    ?>
                                        <em class="new"></em>
                                    <?php
                                    }
                                    ?>
                                </span>
                            </a>
                        </li>
                        <?php
                        }
                        ?>
                    </ol>
                </div>
                <div class="mykeyword" id="myKeywordAreaDiv">
                	<h3>내가 찾은 검색어</h3>
                    <ul id="myKeywordArea">
                    	<li>
                            <a href="#none">관광명소 가볼만한곳</a>
                            <a href="#none" class="del"><span>삭제</span></a>
                        </li>
                        <li>
                        	<a href="#none">목포소개</a>
                            <a href="#none" class="del"><span>삭제</span></a>
                        </li>
                        <li>
                        	<a href="#none">목포시 복지</a>
                            <a href="#none" class="del"><span>삭제</span></a>
                        </li>
                        <li>
                        	<a href="#none">목포시복지제도가 궁금궁금궁금</a>
                            <a href="#none" class="del"><span>삭제</span></a>
                        </li>
                    </ul>
                </div>
            </div>
        	<!--aside e-->
        </div>
    </div>
	<!--container e-->
</div>
<!--wrap e-->
<div id="footer">
	<ul class="footer_link">
    	<li><a href="#none">개인정보처리방침</a></li>
        <li><a href="#none">이메일주소무단수집거부</a></li>
        <li><a href="#none">RSS이용안내</a></li>
        <li><a href="#none">뷰어프로그램다운로드</a></li>
    </ul>
    <p class="add">58613) 전라남도 목포시 양을로 203 (용당동) 목포시청 대표전화 061-272-2171 / 대표팩스 061-270-3598</p>
    <p class="copy">
        본 사이트는 이메일 주소가 무단수집 되는것을 거부하며, 위반시 정보통신망법에 의해 처벌됨을 유념하십시오.
        <span>COPYRIGHT © MOKPO-CITY. ALL RIGHTS RESERVED.</span>
    </p>
</div>
</body>
</html>
