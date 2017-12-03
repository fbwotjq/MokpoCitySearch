<?php
header("pragma: no-cache");
require_once "./common/queryapi530.html";
require_once "./common/WNUtils.html";
require_once "./common/WNSearch.html";

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
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,URL,ORIGINAL_NAME,RE_NAME,TABLE_IDX,IDX,ALIAS'
        ),
        array(
            'collectionName' => 'multi',
            'viewCount' => 6,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,RENAME,URL,IDX,ALIAS'
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
            'collectionName' => 'menu',
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
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,URL,ORIGINAL_NAME,RE_NAME,TABLE_IDX,IDX,ALIAS'
        )
    ),
    'multi' => array(
        array(
            'collectionName' => 'multi',
            'viewCount' => 12,
            'defaultSearchField' => 'TITLE,CONTENT,WRITER',
            'documentField' => 'DOCID,Date,TITLE,CONTENT,WRITER,BOARDID,RENAME,URL,IDX,ALIAS'
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
$startCount	= $wnUtils->getCheckReq($_GET, "startCount", 0);						// 검색 요청할 시작 페이지인덱스
$sortField	= $wnUtils->getCheckReq($_GET, "sortField", "RANK");					// 검색 정렬 대상 필드
$popKeywordType	= $wnUtils->getCheckReq($_GET, "popKeywordType", "D");				// 인기검색어 선택 필드

$startDate = $wnUtils->getCheckReq($_GET, "startDate", "1980-01-01");				// 날짜 선택 필드
$startDate = str_replace("-", "/", $startDate);
$endDate = $wnUtils->getCheckReq($_GET, "endDate", "2030-12-31");				    // 날짜 선택 필드
$endDate = str_replace("-", "/", $endDate);

$isDetailSearch  = $wnUtils->getCheckReq($_GET, "isDetailSearch", 0);				// 상세검색 안할경우 0, 할경우 1
$searchField = $wnUtils->getCheckReq($_POST, "searchField", "ALL");					// 검색필드 설정

$ret = $search->w3SetCodePage(CHARSET);
$ret = $search->w3SetQueryLog(USE_QUERY_LOG_ON);
$ret = $search->w3SetCommonQuery($query, COMMON_OR_WHEN_NORESULT_OFF);

$currentCollectionMapping = $collectionMappingDefine[$collection];

foreach ($currentCollectionMapping as $value) {

    $collectionName = $value['collectionName'];
    $viewCount = $value['viewCount'];
    $sortCondition = $wnUtils->getSortCondition($sortField);
    $searchFieldCondition = $value['defaultSearchField'];
    $documentField = $value['documentField'];

    //echo(printf('SETTING SEARCH CONDITION => collectionName:%s, viewCount:%s, sortField:%s <br/>', $collectionName, $viewCount, $sortCondition));

    $ret = $search->w3AddCollection($collectionName);
    $ret = $search->w3SetDateRange($collectionName, $startDate, $endDate);
    $ret = $search->w3SetQueryAnalyzer($collectionName, USE_LA_ON, IGNORE_CASE_ON, USE_ORIGINAL_ON, USE_SYNONYM_ON);
    $ret = $search->w3SetHighlight($collectionName, USE_HIGHLIGHT_ON, USE_SNIPPET_ON);
    $ret = $search->w3SetPageInfo($collectionName, $startCount, $viewCount);
    $ret = $search->w3SetSortField($collectionName, $sortCondition);
    $ret = $search->w3SetDocumentField($collectionName, $documentField);

}

$ret = $search->w3SetTraceLog(3);
$ret = $search->w3ConnectServer(SEARCH_IP, SEARCH_PORT, SEARCH_TIMEOUT);
$ret = $search->w3ReceiveSearchQueryResult(CONNECTION_CLOSE);

$totalSearchCount = 0;
$resultTotalSetDocument = array();
foreach ($currentCollectionMapping as $value) {

    $collectionName = $value['collectionName'];
    $collectionDocumentFieldArray = explode(",", $value['documentField']);

    $collectionTotalCount = $search->w3GetResultTotalCount($collectionName);
    $collectionResultCount = $search->w3GetResultCount($collectionName);

    $resultTotalSetDocument[$collectionName . 'TotalCount'] = $collectionTotalCount;
    $resultTotalSetDocument[$collectionName . 'ResultCount'] = $collectionResultCount;

    for( $i = 0 ; $i < $collectionResultCount ; $i++ ) {
        $resultCollectionSetDocument = array();
        foreach($collectionDocumentFieldArray as $collectionDocumentField) {

            $columnValue = $search->w3GetField($collectionName, $collectionDocumentField, $i);
            $resultCollectionSetDocument[$collectionDocumentField] = $columnValue;

        }
    }
    $resultTotalSetDocument[$collectionName] = $resultCollectionSetDocument;

    $totalSearchCount += $collectionTotalCount;
    #echo(printf('SEARCH RESULT => collectionName:%s, totalCount:%s, resultCount:%s <br/>', $collectionName, $collectionTotalCount, $collectionResultCount));

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
            <div class="range">
                <a id="range1" class="list1 on" title="전체" href="#none">전체</a>
                <a id="range2" class="list2" title="1주" href="#none">1주</a>
                <a id="range3" class="list3" title="1개월" href="#none">1개월</a>
                <a id="range4" class="list4" title="1년" href="#none">1년</a>
            </div>
            <div class="date_search">
                <p>
                    <label class="blind" for="start_day">시작일</label>
                    <input id="start_day" class="hasDatepicker" type="text" readonly value="시작일">
                    <img class="ui-datepicker-trigger" src="/total_search/images/calender.png" alt="달력" title="달력" style="width:33px; height:30px;">
                </p>
                <p>
                    <label class="blind" for="end_day">종료일</label>
                    <input id="end_day" class="hasDatepicker" type="text" readonly value="종료일">
                    <img class="ui-datepicker-trigger" src="/total_search/images/calender.png" alt="달력" title="달력" style="width:33px; height:30px;">
                </p>
                <input class="date_apply" type="button" value="날짜적용">
            </div>
        </div>
        <div class="leftArea">
        	<h2>검색영역</h2>
            <div>
                <a class="list1 on" href="#none"><span>전체</span></a>
                <a class="list2" href="#none"><span>제목</span></a>
                <a class="list3" href="#none"><span>내용</span></a>
                <a class="list4" href="#none"><span>작성자</span></a>
            </div>
        </div>
    </div>
    <!--container s-->
	<div id="container" class="group">
    	<div id="top">
        	<div class="inner">
                <p class="logo"><span>건강의 섬 목포</span></p>
                <div class="top_search">
                    <fieldset>
                        <legend>통합검색</legend>
                        <input id="query" type="text" onkeypress="javascript:pressCheck((event),this);" value="무엇이든 찾아보세요" name="query" autocomplete="off">
                        <label for="query"><a href="#none"><img height="40" width="43" alt="검색" src="/total_search/images/search_icon.png"></a></label>
                        <a class="button_detail" href="#none"><span>상세검색</span></a>
                        <p>
                            <input id="rechk" type="checkbox" title="결과 내 재검색 하기" name="rechk">
                            <label for="rechk">결과 내 재검색</label>
                        </p>
                        <!--상세검색-->
                        <div class="detail_search" style="display:none;">
                            <div class="detail_top">
                                <span class="dtit">상세검색</span>
                                <span class="dm">여러개의 단어를 입력할 때는 공백으로 구분해서 입력하세요.</span>
                                <span class="dclose"><a href="#none">닫기</a></span>
                            </div>
                            <fieldset class="detail_search_field">
                                <legend>상세검색</legend>
                                <label for="correct">정확히 일치하는 단어/문장("")</label>
                                <input id="correct" name="correct" type="text" value=''>
                                <label for="surely">반드시 포함하는 단어(공백)</label>
                                <input id="surely" name="surely" type="text" value=''>
                                <label for="except">제외하는 단어(!)</label>
                                <input id="except" name="except" type="text" value=''>
                            </fieldset>
                            <div class="detail_bottom">
                                <div class="btn">
                                    <input class="search" value="검색" onclick="doSearch();" type="submit">
                                    <input class="cancel" value="초기화" type="reset">
                                </div>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div id="content">
        	<div class="title total_sch">
                <p><span class="blue fw5">"<?=$query?>"</span>에 대한 전체 <span class="blue"><?=$totalSearchCount?></span>건의 결과를 찾았습니다.</p>
                <div class="align">
                    <a class="list1" href="#none"><span>정확도순</span></a>
                    <a class="list2 on" href="#none"><span>최신순</span></a>
                </div>
            </div>
        	<!--result s-->
            <div class="result total_sch">
                <?php if($collection == 'ALL' || $collection == 'menu') { ?>
                <div class="menu_search">
                    <h3>메뉴검색<span> [총 <?= $resultTotalSetDocument['menuTotalCount'] ?>건]</span></h3>
                    <ul>
                    	<li><a href="#none">관광 > 관광명소 > <span class="blue">목포</span>절경 > <span class="blue">목포</span>타워</a></li>
                        <li><a href="#none">보건소 > <span class="blue">목포</span>복지 > <span class="blue">목포</span>에서 제공하는 혜택</a></li>
                        <li><a href="#none">군청 > <span class="blue">목포</span>소개  > 군청안내 > 행정조직도</a></li>
                        <li><a href="#none">관광 > 관광명소 > <span class="blue">목포</span>절경 > <span class="blue">목포</span>타워</a></li>
                        <li><a href="#none">보건소 > <span class="blue">목포</span>복지 > 목포에서 제공하는 혜택</a></li>
                    </ul>
                    <span class="more"><a href="#none">+ 메뉴 더보기</a></span>
                </div>
                <?php } ?>
                <?php if($collection == 'ALL' || $collection == 'board') { ?>
                <div class="news_search">
                	<h3>목포소식 <span>[총 <?= $resultTotalSetDocument['boardTotalCount'] ?>건]</span></h3>
                    <ul>
                    	<li>
                        	<h4>
                                <a href="#none">
                                    <span class="menuName">[보도자료]</span>
                                    <span class="tit"><span class="blue">목포</span> <span class="green">자연그대로 한우 브랜드 개발 선호도 조사</span></span>
                                </a>
                                <span class="date">| 2017.02.02</span>
                                <a href="#none" target="_blank" class="new_page">새창열기</a>
                            </h4>
                            <p>목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는...</p>
                        </li>
                        <li>
                        	<h4>
                                <a href="#none">
                                    <span class="menuName">[보도자료]</span>
                                    <span class="tit"><span class="blue">목포</span> <span class="green">자연그대로 한우 브랜드 개발 선호도 조사</span></span>
                                </a>
                                <span class="date">| 2017.02.02</span>
                                <a href="#none" target="_blank" class="new_page">새창열기</a>
                            </h4>
                            <p>목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는...</p>
                        </li>
                    </ul>
                    <span class="more"><a href="#none">+ 목포소식 더보기</a></span>
                </div>
                <?php } ?>
                <?php if($collection == 'ALL' || $collection == 'member') { ?>
                <div class="staff_table">
                    <h3>직원검색 <span>[총 <?= $resultTotalSetDocument['memberTotalCount'] ?>건]</span></h3>
                    <table>
                        <caption>직원업무안내표로 이름,부서,직책,업무,연락처 항목으로 구성</caption>
                        <thead>
                          <tr>
                            <th scope="col">이름</th>
                            <th scope="col">부서</th>
                            <th scope="col">직책</th>
                            <th scope="col">업무</th>
                            <th scope="col">연락처</th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>김희수</td>
                            <td>기획실</td>
                            <td>담당</td>
                            <td><span class="blue">목포</span>시 각종 시책의 종합기획 조정통제</td>
                            <td>061-550-5030</td>
                          </tr>
                          <tr>
                            <td>이동일</td>
                            <td>기획실</td>
                            <td>직원</td>
                            <td>시정 현황 및 계획작성</td>
                            <td>061-550-5031</td>
                          </tr>
                          <tr>
                            <td>이승옥</td>
                            <td>기획실</td>
                            <td>직원</td>
                            <td>시장지시사항 관리, 시민제안 제도 운영</td>
                            <td>061-550-5032</td>
                          </tr>
                          <tr>
                            <td>김두길</td>
                            <td>기획실</td>
                            <td>직원</td>
                            <td>기획자료정리</td>
                            <td>061-550-5033</td>
                          </tr>
                        </tbody>
                      </table>
                    <span class="more"><a href="#none">+ 직원/업무 더보기</a></span>
                </div>
                <?php } ?>
                <?php if($collection == 'ALL' || $collection == 'webpage') { ?>
                <div class="area_search">
                	<h3>분야별정보 <span>[총 <?= $resultTotalSetDocument['webpageTotalCount'] ?>건]</span></h3>
                    <ul>
                        <li>
                            <h4>
                                <a href="#none">
                                    <span class="menuName">[보도자료]</span>
                                    <span class="tit"><span class="blue">목포</span> <span class="green">자연그대로 한우 브랜드 개발 선호도 조사</span></span>
                                </a>
                                <span class="date">| 2017.02.02</span>
                                <a href="#none" target="_blank" class="new_page">새창열기</a>
                            </h4>
                            <p>목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는...</p>
                        </li>
                        <li>
                            <h4>
                                <a href="#none">
                                    <span class="menuName">[보도자료]</span>
                                    <span class="tit"><span class="blue">목포</span> <span class="green">자연그대로 한우 브랜드 개발 선호도 조사</span></span>
                                </a>
                                <span class="date">| 2017.02.02</span>
                                <a href="#none" target="_blank" class="new_page">새창열기</a>
                            </h4>
                            <p>목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는 한우브랜드 개발에 대해  후보안을 붙임과 같이 제안하오니, 선호도 조사에 참여하여 주시면 감사하겠습니다.호도 조사에 참여하여 주시면 감사하겠습니다.목포자연그대로 농축산업 조기정착을 위해 추진하고 있는...</p>
                        </li>
                   	</ul>
                    <span class="more"><a href="#none">+ 분야별정보 더보기</a></span>
                </div>
                <?php } ?>
                <?php if($collection == 'ALL' || $collection == 'multi') { ?>
                <div class="media">
                	<h3>사진/동영상 <span>[총 <?= $resultTotalSetDocument['multiTotalCount']?>건]</span></h3>
                    <ul>
                    	<li>
                            <a href="#none" target="_blank">
                                <span class="img">
                                    <img src="/total_search/images/pho1.jpg" alt="도시교통분과위원회 회의 영상" />
                                    <span class="play"></span>
                                </span>
                                <span class="menuName">[동영상으로보는목포]</span>
                                <span class="green">도시교통분과위원회 회의 영상</span>
                                <span class="date">2015.11.07</span>
                            </a>
                        </li>
                        <li>
                            <a href="#none" target="_blank">
                                <span class="img"><img src="/total_search/images/pho2.jpg" alt="목포청해진 유적지" /></span>
                                <span class="menuName">[포토갤러리]</span>
                                <span class="green"><span class="blue">목포</span>청해진 유적지</span>
                                <span class="date">2015.11.07</span>
                            </a>
                        </li>
                        <li>
                        	<a href="#none" target="_blank">
                                <span class="img"><img src="/total_search/images/pho3.jpg" alt="목포 청산도 전경" /></span>
                                <span class="menuName">[사진게시판]</span>
                                <span class="green"><span class="blue">목포</span> 청산도 전경</span>
                                <span class="date">2015.11.07</span>
                            </a>
                        </li>
                        <li>
                        	<a href="#none" target="_blank">
                                <span class="img"><img src="/total_search/images/pho4.jpg" alt="청정바다수도목포 선포식" /></span>
                                <span class="menuName">[보도자료]</span>
                                <span class="green">청정바다수도<span class="blue">목포</span> 선포식</span>
                                <span class="date">2015.11.07</span>
                            </a>
                        </li>
                        <li>
                        	<a href="#none" target="_blank">
                                <span class="img"><img src="/total_search/images/pho5.jpg" alt="목포 윤선도 원림 곡수당" /></span>
                                <span class="menuName">[사진게시판]</span>
                                <span class="green"><span class="blue">목포</span> 윤선도 원림 곡수당</span>
                                <span class="date">2015.11.07</span>
                            </a>
                        </li>
                        <li>
                        	<a href="#none" target="_blank">
                                <span class="img"><img src="/total_search/images/pho6.jpg" alt="보길도 세연정" /></span>
                                <span class="menuName">[사진게시판]</span>
                                <span class="green">보길도 세연정</span>
                                <span class="date">2015.11.07</span>
                            </a>
                        </li>
                    </ul>
                    <span class="more"><a href="#none">+ 사진/동영상 더보기</a></span>
                </div>
                <?php } ?>
                <?php if($collection == 'ALL' || $collection == 'infosearch') { ?>
                <div class="information">
                	<h3>정보검색 <span>[총 <?= $resultTotalSetDocument['infosearchTotalCount']?>건]</span></h3>
                    <ul>
                    	<li>
                            <h4>
                                <a href="#none">
                                    <span class="tit">
                                        <span class="green">2017년도 상반기 <span class="blue">목포</span>시 규제개혁위원회 회의 결과 (각종위원회개최내용및결과)</span>
                                    </span>
                                </a>
                                <span class="date">| 2017.02.02</span>
                                <a class="new_page" href="#none" target="_blank">새창열기</a>
                            </h4>
                            <p class="location"><a href="#none">목포시청 > 정부3.0정보공개 > 행정정보사전공표목록 > 수시적행정정보공표대상 > 각종위원회개최내용및결과</a></p>
                            <ul class="file_box">
                                <li class="hwp"><a href="#none">2017년 상반기 규제개혁위원회 회의 결과 1부.hwp</a></li>
                            </ul>
                        </li>
                        <li>
                        	<h4>
                                <a href="#none">
                                    <span class="tit">
                                        <span class="green">2017년도 상반기 <span class="blue">목포</span>시 규제개혁위원회 회의 결과 (각종위원회개최내용및결과)</span>
                                    </span>
                                </a>
                                <span class="date">| 2017.02.02</span>
                                <a class="new_page" href="#none" target="_blank">새창열기</a>
                            </h4>
                            <p class="location"><a href="#none">목포시청 > 정부3.0정보공개 > 행정정보사전공표목록 > 수시적행정정보공표대상 > 각종위원회개최내용및결과</a></p>
                            <ul class="file_box">
                                <li class="hwp"><a href="#none">2017년 상반기 규제개혁위원회 회의 결과 1부.hwp</a></li>
                                <li class="xls"><a href="#none">2017년 상반기 규제개혁위원회 회의 결과 2부_1.xls</a></li>
                                <li class="pdf"><a href="#none">2017년 상반기 규제개혁위원회 회의 결과 3부_1.pdf</a></li>
                            </ul>
                        </li>
                    </ul>
                    <span class="more"><a href="#none">+ 정보검색 더보기</a></span>
                </div>
                <?php } ?>
            </div>
			<!--result e-->
			<!--aside s-->
            <div id="aside">
            	<div class="popular">
                    <h3>인기검색어</h3>
                    <ul class="tab">
                        <li><a href="#none">일간</a></li>
                        <li><a class="on" href="#none">주간</a></li>
                    </ul>
                    <ol class="list">
                        <li>
                            <a href="#none">
                                <span class="num top">1</span>
                                <span class="tit">조직도</span>
                                <span class="rank"><em class="new"></em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num top">2</span>
                                <span class="tit">목포</span>
                                <span class="rank"><em class="up">2</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num top">3</span>
                                <span class="tit">복지</span>
                                <span class="rank"><em class="dn">2</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num top">4</span>
                                <span class="tit">민원출장소안내사무소개</span>
                                <span class="rank"><em class="dn">2</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num top">5</span>
                                <span class="tit">청산도축제</span>
                                <span class="rank"><em class="dn">3</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num">6</span>
                                <span class="tit">장애인</span>
                                <span class="rank"><em class="up">5</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num">7</span>
                                <span class="tit">청소년</span>
                                <span class="rank"><em class="dn">2</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num">8</span>
                                <span class="tit">드림스타트</span>
                                <span class="rank"><em class="up">1</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num">9</span>
                                <span class="tit">다문화가족</span>
                                <span class="rank"><em>-</em></span>
                            </a>
                        </li>
                        <li>
                            <a href="#none">
                                <span class="num">10</span>
                                <span class="tit">재난안전</span>
                                <span class="rank"><em class="up">5</em></span>
                            </a>
                        </li>
                    </ol>
                </div>
                
                <div class="mykeyword">
                	<h3>내가 찾은 검색어</h3>
                    <ul>
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
