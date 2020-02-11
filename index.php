<!DOCTYPE html>
<!-- bu yazılım Dr. Zafer Akçalı tarafından oluşturulmuştur 
sadece csv dosyalarını okumak ve depCSV, academicsCSV değişkenlerini atamak için php kullanılmıştır -->
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Web of Science'da Ayrıntılı Arama</title>
<script src="./papaparse.min.js"></script>
<style>
#selectAcademician
{
 width:520px;   
}
* {box-sizing: border-box}

/* Style the tab */
.tab {
  float: left;
  border: 1px solid #ccc;
  background-color: #f1f1f1;
  width: 10%;
  height: 960px;
}

/* Style the buttons that are used to open the tab content */
.tab button {
  display: block;
  background-color: inherit;
  color: black;
  padding: 22px 16px;
  width: 100%;
  border: none;
  outline: none;
  text-align: left;
  cursor: pointer;
  transition: 0.3s;
}

/* Change background color of buttons on hover */
.tab button:hover {
  background-color: #ddd;
}

/* Create an active/current "tab button" class */
.tab button.active {
  background-color: #ccc;
}

/* Style the tab content */
.tabcontent {
  float: left;
  padding: 0px 12px;
  border: 1px solid #ccc;
  width: 90%;
  border-left: none;
  height: 960px;
}


/* Create two equal columns that floats next to each other */
.column {
  float: left;
  width: 35%;
  padding: 10px;
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
</style>
</head>
<body>

<div class="tab">
  <button class="tablinks active" onclick="openSection(event, 'Makale')"id="defaultOpen">Makale ve atıf ara</button>
  <button class="tablinks" onclick="openSection(event, 'Yazar')">Yazar ara</button>
  <button class="tablinks" onclick="openSection(event, 'BelgeBilgi')">Belge-bilgi indir</button>
</div>
<div id="Makale" class="tabcontent">
	Hazır gelen metni değiştirebilirsiniz. Aşağıdaki metni ayrıca seçip kopyalamanıza gerek yoktur. <br />
	Açılan WOS sayfasındaki "Search" üzerindeki kutuya doğrudan yapıştırabilirsiniz <br />
	Web of Science'da AdvancedSearch arama kutusuna yazılacak metni giriniz, "WOS arama sayfası" na tıklayınız <br />
	<textarea rows = "20" cols = "100" name = "Uzun Yazı" id="searchText"></textarea> <br />

<button onclick="openWOSw()">WOS arama sayfası</button> <a href="advancedsearch.png"> İlk gidişte (bir seferlik) sayfayı kapatınız veya AdvancedSearch menüsüne tıklayınız</a> 
 <br /> <br />
    Hazır arama metinleri. Seçerseniz sayfa başındaki kutuya aktarılacak ve clipboarda kopyalanmış olacaktır. <br />  
<select id="selectDepartment" onchange="copyQueryText(this.options[this.selectedIndex].value)">
	<option value="">Anabilim / Bilim Dalı / Şehir Seçiniz</option>
	<!-- rest is created by window.onload = async function()-->
</select> 
<br /> <br />
<a href="WOSnumber.png"> WOS numarası makalenin altındadır (göster)</a> <br />
<div class="row">
   <div class="column">
Makalenin WOS numarasını yazınız <br /> WOS:<input type=text name = WOSnumber id="WOSnumber"></text> <br />
<button onclick="displayWOSdocument()" title="Üniversite içinden">Makaleyi göster</button> <button onclick="displayWOScitation()" title="Üniversite içinden">Atıflarını göster</button><br />
<button onclick="displayWOSfree()" title="Üniversite dışından ve proxy yok ise">Kısıtlı erişim</button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button onclick="displayWOScitfree()" title="Üniversite dışından ve proxy yok ise">Kısıtlı erişim</button> <br />
<br />
Makalenin PubmedID numarasını yazınız <br />pmid:<input type=text name = pmidnumber id="pmidnumber"></text> <br />
<button onclick="displayPIDdocument()">Makaleyi göster</button>
<button onclick="displayPIDcitation()">Atıflarını göster</button>
<button onclick="displayPIDold()">Eski Pubmed</button> <br /> <br />
Makalenin TRdizin kodunu yazınız <br /><input type=text name = TRDizin kodu id="trdizinid"></text>=<br />
<button onclick="displayTRdizindocument()">Makaleyi göster</button><br />
	</div>
   <div class="column">
Makalenin Elsevier numarasını (eid) yazınız <br /> eid: 2-s2.0-<input type=text name = eidnumber id="eidnumber"></text> <br />
<button onclick="displayEIDdocument()" title="Üniversite içinden">Makaleyi göster</button> <button onclick="displayEIDcitation()" title="Üniversite içinden">Atıflarını göster</button><br /> <br /> <br />   
Makalenin DOI numarasını (doi) yazınız <br />https://doi.org/<input type=text name = doidnumber id="doinumber"></text>  <br /> 
<button onclick="displayDOIdocument()">Makaleyi göster</button>
<button onclick="displayDOIWosdocument()" title="Makale WOS'da taranıyorsa görebilirsiniz">WOS'da göster</button><br /> <br /> 

	</div>
</div>
</div>
<div id="Yazar" class="tabcontent">
 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="yazar-id.htm" target="_blank">Yazarların ID'lerine buradan ulaşabilirsiniz (tıklayınız)</a> <br /> 
<div class="row">
   <div class="column">
Yazarın ResearcherID'ini (Publons) yazınız <br /> <input type=text name = researcherIDnumber id="ridnumber"></text> <br />
<button onclick="displayridResearcher()">Yazarı göster</button>
<button onclick="copyrid()">Arama metni</button> <br /> <br />
Yazarın ORCID'ini  yazınız <br /> http://orcid.org/<input type=text name = orcId id="orcidnumber"></text>
<button onclick="previousOrcid()"><</button> <button onclick="nextOrcid()">></button> <br />
<button onclick="displayorcidResearcher()">Yazarı göster</button> <button onclick="copyorcid()">Arama metni</button> <br /> <br /> 
Yazarın Elsevier pure ismini  yazınız <br /> <input type=text name = baskent pure id="purename"></text> <br />
<button onclick="displaypureResearcher()">Yazarı göster</button> <br /> <br /> 
	</div>
   <div class="column">
Yazarın ScopusID'ini  yazınız <br /> 
<input type=text name = scopusId id="sidnumber"></text> &nbsp;
<button onclick="previousSid()"><</button> <button onclick="nextSid()">></button><br />  
<button onclick="displaysidResearcher()">Yazarı göster</button> <button onclick="displayMendeley()">Mendeley</button> <br />  <br />
Yazarın YÖK authorID'ini  yazınız <br /> <input type=text name = yokId id="yokauthorID"></text> <br />
<button onclick="displayyokResearcher()">Yazarı göster</button>  <br /> <br /> 
Yazarın adını ve soyadını yazınız <br /> <input type=text name = namesurname id="namesurname"></text> <br />
<button onclick="displaypubmedResearcher()" title="Pubmed'de arama yaparken Türkçe harf kullanmayınız">Pubmed'de</button>
<button onclick="displayTRdizinResearcher()" title="TRdizin'de arama yaparken Türkçe harf kullanınız">TRdizin'de</button><br />
<button onclick="displayTRdizindeWOS()" title="TRdizin'de arama yaparken Türkçe harf kullanınız" >TRdizin'de WOS makalesi bul</button>
	</div>
</div>
Başkent Üniversitesi Sayfaları <br />
 <button onclick="displayyokBaskent()">YÖKSİS Sayfası</button>
 <button onclick="displayTRdizinBaskent()">TRdizin Sayfası</button>
 <button onclick="displaypureBaskent()">Elsevier Pure Sayfası</button>
 <button onclick="displayMendeleyBaskent()">Mendeley Sayfası</button>
 <button onclick="displayopenAccessBaskent()">Açık Erişim Sayfası</button><br />
 <button onclick="displaypublonsBaskent()">Publons Sayfası</button> <br /><br />
 Başkent Üniversitesi Tıp Fakültesi Sayfaları <br />
 <button onclick="displayyokMed()">YÖKSİS Sayfası</button>
 <button onclick="displayTRdizinMed()">TRdizin Sayfası</button>
 <button onclick="displaypureMed()">Elsevier Pure Sayfası</button> 
 <button onclick="displayscopusBaskentMed()">Scopus Sayfası</button> 
 <button onclick="displayopenAccessMed()">Açık Erişim Sayfası</button> <br /> <br />
 <!-- Akademisyen seçimi menüsü -->
<label for="Akademisyen seçimi">Akademisyen ismini veya bölümünü yazarak seçiniz</label>  <br />
<input list="academicians" id="selectAcademician" onchange="copyAcademician()" name="acadname"/> <button onclick="clearAcademician()">Sil</button> 
<datalist id="academicians">
	<!-- rest is created by window.onload = async function()-->
</datalist>
<br />
<button onclick="previousAcademician()"><</button> <button onclick="nextAcademician()">></button><br /> 
</div>
<div id="BelgeBilgi" class="tabcontent">
<a href="https://baskentedutr-my.sharepoint.com/:b:/g/personal/tipdekanlikbilisim_baskent_edu_tr/EQl1QFwaDPpHqdW9xrR_LMIBAF0L2HtjGxo7n7CtRIxLaQ?e=bvTlZX" target="_blank">
Web of Scienca'da Makale-Atıf-Yazar Nasıl aranır? (Tıklayarak okuyabilirsiniz)</a><br /><br />
 BUTF kaynaklı, SCI-E, SSCI, AHCI'de kayıtlı yayınların 2020 Ocak ayı geçici analizleri <br />
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<a href="https://baskentedutr-my.sharepoint.com/:f:/g/personal/tipdekanlikbilisim_baskent_edu_tr/EjsVaDV3-OxEnbiMvCzz6p4BKHEcWsL0JjjCC7giV-cqBg?e=Q1Js3e" target="_blank">
Klasörden indiriniz(tıklayınız)</a><br /> <br />
Sadece aşağıdaki indekslerin kutusu seçilerek oluşturulmuştur <br />
<img src="moresettings.png" alt="Seçili indeksler">
</div>


<script>
WOSadvancedURL = "http://apps.webofknowledge.com/WOS_AdvancedSearch_input.do?&product=WOS&search_mode=AdvancedSearch";
var queryT = [];
var academics = [[],[]];
var currentAcad;
var sidColumn=0;
var orcidColumn=0;

function openSection(evt, sectionName) {
  // Declare all variables
  var i, tabcontent, tablinks;

  // Get all elements with class="tabcontent" and hide them
  tabcontent = document.getElementsByClassName("tabcontent");
  for (i = 0; i < tabcontent.length; i++) {
    tabcontent[i].style.display = "none";
  }

  // Get all elements with class="tablinks" and remove the class "active"
  tablinks = document.getElementsByClassName("tablinks");
  for (i = 0; i < tablinks.length; i++) {
    tablinks[i].className = tablinks[i].className.replace(" active", "");
  }

  // Show the current tab, and add an "active" class to the link that opened the tab
  document.getElementById(sectionName).style.display = "block";
  evt.currentTarget.className += " active";
}
// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();

function displayWOSdocument() {
	w=document.getElementById('WOSnumber').value;
	urlText = "http://ws.isiknowledge.com/cps/openurl/service?url_ver=Z39.88-2004&rft_id=info:ut/"+w;
	window.open(urlText,"_blank");
	}
function displayWOScitation() {
	w=document.getElementById('WOSnumber').value;
	urlText = "http://ws.isiknowledge.com/cps/openurl/service?url_ver=Z39.88-2004&rft_id=info:ut/"+w+"&svc_val_fmt=info:ofi/fmt:kev:mtx:sch_svc&svc.citing=yes";
	window.open(urlText,"_blank");
	}
function displayWOSfree() {
	w=document.getElementById('WOSnumber').value;
	urlText = "https://gateway.webofknowledge.com/gateway/Gateway.cgi?GWVersion=2&SrcApp=Publons&SrcAuth=Publons_CEL&KeyUT=WOS:"+w+"&DestLinkType=FullRecord&DestApp=WOS_CPL";
	window.open(urlText,"_blank");
	}
function displayWOScitfree() {
	w=document.getElementById('WOSnumber').value;
	urlText = "https://gateway.webofknowledge.com/gateway/Gateway.cgi?GWVersion=2&SrcApp=Publons&SrcAuth=Publons_CEL&KeyUT=WOS:"+w+"&DestLinkType=CitingArticles&DestApp=WOS_CPL";
	window.open(urlText,"_blank");
	}
function displayEIDdocument() {
	w=document.getElementById('eidnumber').value;
	urlText = "https://www.scopus.com/record/display.uri?eid=2-s2.0-"+w+"&origin=resultslist";
	window.open(urlText,"_blank");
	}
function displayPIDdocument() {
	w=document.getElementById('pmidnumber').value;
	urlText = "https://pubmed.ncbi.nlm.nih.gov/"+w;
	window.open(urlText,"_blank");
	}
function displayPIDcitation() {
	w=document.getElementById('pmidnumber').value;
	urlText = "https://pubmed.ncbi.nlm.nih.gov/"+w+"#citedby";
	window.open(urlText,"_blank");
	}
function displayPIDold() {
	w=document.getElementById('pmidnumber').value;
	urlText = "https://www.ncbi.nlm.nih.gov/pubmed/"+w;
	window.open(urlText,"_blank");
	}
function displayDOIdocument() {
	w=document.getElementById('doinumber').value;
	urlText = "https://doi.org/"+w;
	window.open(urlText,"_blank");
	}
function displayDOIWosdocument() {
	w=document.getElementById('doinumber').value;
	urlText = "http://ws.isiknowledge.com/cps/openurl/service?url_ver=Z39.88-2004&rft_id=info:doi/"+w;
	window.open(urlText,"_blank");
	}
function displayTRdizindocument() {
	w=document.getElementById('trdizinid').value;
	urlText = "https://trdizin.gov.tr/publication/paper/detail/"+w;
	window.open(urlText,"_blank");
	}
function displayEIDcitation() {
	w=document.getElementById('eidnumber').value;
	urlText = "https://www.scopus.com/search/submit/citedby.uri?eid=2-s2.0-"+w+"&src=s&origin=resultslist";
	window.open(urlText,"_blank");
	}
function displayridResearcher() {
	w=document.getElementById('ridnumber').value;
	urlText = "https://publons.com/researcher/"+w+"/";
	window.open(urlText,"_blank");
	}
function copyrid() {
	w=document.getElementById('ridnumber').value;
	document.getElementById('searchText').value = "AI="+w;
// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();

	}
function copyorcid() {
	w=document.getElementById('orcidnumber').value;
	document.getElementById('searchText').value = "AI="+w;
// Get the element with id="defaultOpen" and click on it
document.getElementById("defaultOpen").click();

	}

function displaypublonsBaskent() {
	urlText = "https://publons.com/institution/7774/";
	window.open(urlText,"_blank");
	}
function displaysidResearcher() {
	w=document.getElementById('sidnumber').value;
	urlText = "http://www.scopus.com/authid/detail.uri?origin=resultslist&authorId="+w;
	window.open(urlText,"_blank");
	}
function displayMendeley() {
	w=document.getElementById('sidnumber').value;
	urlText = "https://www.mendeley.com/authors/"+w+"/";
	window.open(urlText,"_blank");
	}
function displayorcidResearcher() {
	w=document.getElementById('orcidnumber').value;
	urlText = "https://orcid.org/"+w;
	window.open(urlText,"_blank");
	}
function displaypureResearcher() {
	w=document.getElementById('purename').value;
	urlText = "https://baskent.elsevierpure.com/en/persons/"+w;
	window.open(urlText,"_blank");
}
function displayyokResearcher() {
	w=document.getElementById('yokauthorID').value;
	urlText = "https://akademik.yok.gov.tr/AkademikArama/AkademisyenGorevOgrenimBilgileri?islem=direct&authorId="+w;
	window.open(urlText,"_blank");
	}
function displaypubmedResearcher() {
	w=document.getElementById('namesurname').value;
	urlText = "https://pubmed.ncbi.nlm.nih.gov/?term="+w.replace(" ","+")+"%5BAuthor%5D&sort=date";
	window.open(urlText,"_blank");
	}
function displayTRdizinResearcher() {
	w=document.getElementById('namesurname').value;
	urlText = "https://trdizin.gov.tr/search/searchResults.xhtml?from=1963&to=2030&database=Fen-Sosyal&query=TRDDocument.authors-AND-"+w.replace(" ","%20");
	window.open(urlText,"_blank");
	}
function displayTRdizindeWOS() {
	w=document.getElementById('namesurname').value;
	urlText = "https://trdizin.gov.tr/search/wos/results.xhtml?query="+w.replace(" ","%20");
	window.open(urlText,"_blank");
	}
function displayyokMed() {
	urlText = "https://akademik.yok.gov.tr/AkademikArama/AkademisyenArama?islem=SQvnIyDdbo2J6300YcDPOXzTRrtei7zZkkQPhHRJz_vJ7Auqd-BXdaB8UCTSgbo7";
	window.open(urlText,"_blank");
	}
function displayyokBaskent() {
	urlText = "https://akademik.yok.gov.tr/AkademikArama/AkademisyenArama?islem=RiKy9zt6sSjdnu7RBj1k589kb8kPaQOJB19t78z4z2hZ0rDnHIo0dVL3b_ZK_h53";
	window.open(urlText,"_blank");
	}
function displaypureBaskent() {
	urlText = "https://baskent.elsevierpure.com/en/";
	window.open(urlText,"_blank");
	}
function displayopenAccessBaskent () {
	urlText = "http://dspace.baskent.edu.tr";
	window.open(urlText,"_blank");
}
function displayMendeleyBaskent() {
	urlText = "https://www.mendeley.com/institutions/eab8ce76-317a-11e6-a2b8-022bf5005b9f/";
	window.open(urlText,"_blank");
}
function displayTRdizinBaskent () {
	urlText = "https://trdizin.gov.tr/search/searchResults.xhtml?from=1963&to=2030&database=Fen-Sosyal&order=TRDDocument.year-DESC&query=TRDDocument.authorInstitutions-AND-Başkent+Üniversitesi";
	window.open(urlText,"_blank");
}
function displayTRdizinMed () {
	urlText = "https://trdizin.gov.tr/search/searchResults.xhtml?from=1963&to=2030&database=Fen-Sosyal&order=TRDDocument.year-DESC&query=TRDDocument.authorInstitutions-AND-Başkent+Üniversitesi+Tıp+Fakültesi";
	window.open(urlText,"_blank");
}
function displaypureMed() {
	urlText = "https://baskent.elsevierpure.com/en/organisations/faculty-of-medicine/persons/";
	window.open(urlText,"_blank");
	}
function displayscopusBaskentMed() {
	urlText = "https://www.scopus.com/affil/profile.uri?afid=60073067";
	window.open(urlText,"_blank");
	}
function displayopenAccessMed() {
	urlText = "http://dspace.baskent.edu.tr/handle/11727/1403";
	window.open(urlText,"_blank");
}

function copyQueryText(chosen) {
	var i= Number (chosen);
	document.getElementById('searchText').value=queryT[i];
	let copyText = document.getElementById('searchText');
	copyText.select();
	document.execCommand("copy");
}
function copyAcademician() {
sidColumn=11; // Default Scopus id-1, for any academician
orcidColumn=9; // Default Orcid-1, for any academician
for (var i=0;i<academics.length;i++) {
let acad = academics[i][2] + " "+ academics[i][3] + ", " + academics[i][4] + ", " + academics[i][5];
		if (acad == document.getElementById('selectAcademician').value) 		
		{ currentAcad = i;
//		console.log (currentAcad);
		document.getElementById('yokauthorID').value = academics[i][6];
		document.getElementById('ridnumber').value = academics[i][7];
		document.getElementById('orcidnumber').value = academics[i][9];
		document.getElementById('sidnumber').value = academics[i][11];
		document.getElementById('purename').value = academics[i][16];
		document.getElementById('namesurname').value = academics[i][2] + " " + academics[i][3] ;
		break; }
		}
}
function clearAcademician() {
document.getElementById('selectAcademician').value = "";
document.getElementById('selectAcademician').focus();
}
function previousAcademician() {
//	console.log (currentAcad);
	if (currentAcad >0)  { 
	currentAcad--;
	document.getElementById('selectAcademician').value = academics[currentAcad][2] + " "+ academics[currentAcad][3] + ", " + academics[currentAcad][4] + ", " + academics[currentAcad][5];
	copyAcademician();	
	}
}
function nextAcademician() {
//	console.log (currentAcad);
	if (currentAcad < academics.length-1)  { 
	currentAcad++;
	document.getElementById('selectAcademician').value = academics[currentAcad][2] + " "+ academics[currentAcad][3] + ", " + academics[currentAcad][4] + ", " + academics[currentAcad][5];
	copyAcademician();	
	}
}

// Displays next Scopus ID, if Academician has another one
function nextSid() {
		if (sidColumn < 15 && sidColumn > 10) { // ensure an academician is selected
		document.getElementById('sidnumber').value = academics[currentAcad][++sidColumn];
		}
}
function previousSid() {
		if (sidColumn > 11) {
		document.getElementById('sidnumber').value = academics[currentAcad][--sidColumn];
		}
}
function nextOrcid() {
		if (orcidColumn < 10 && orcidColumn > 8) { // ensure an academician is selected
		document.getElementById('orcidnumber').value = academics[currentAcad][++orcidColumn];
		}
}
function previousOrcid() {
		if (orcidColumn > 9) {
		document.getElementById('orcidnumber').value = academics[currentAcad][--orcidColumn];
		}
}
function openWOSw (){
	let copyText = document.getElementById('searchText');
	copyText.select();
	document.execCommand("copy");
	window.open(WOSadvancedURL,"_blank");
}

window.onload = function() { 
// read query texts from server by using papaparse.min.js library instead of inline editing 
// Başkent Üniversitesi
//	queryT [0] = "AD=(baskent univ)" ;
//	.
//	.
// İstanbul'dan en az 1
// queryT [83] = "( AD=(baskent univ SAME (Istanbul*) )) NOT AD=(Dis hekimligi OR Dent* OR Nursing OR Periodontol* )" ;

// let csvurl= 'https://cors-anywhere.herokuapp.com/http://tip2.baskent.edu.tr/maya/departmentQuery.csv'; // different domain
// let csvurl= 'http://tip2.baskent.edu.tr/maya/departmentQuery.csv'; // same domain
// let response = await fetch (csvurl);
// let depCSV = await response.text();

let depCSV = <?php echo json_encode(file_get_contents('departmentQuery.csv')); ?>; 
//read depCSV content from same folder with PHP, instead of javascript aync function fetch
let results = Papa.parse(depCSV);
for (var i=0; i<results.data.length; i++) {
queryT[i] = results.data[i][1]; }

// Append to selectList
// 	<option value="0">Başkent Üniversitesi</option>
//	.
//	.
//	<option value="83">İstanbul'dan en az 1</option>

 <!-- Bölüm seçimi menüsünü oluşturur -->
var selectList = document.getElementById('selectDepartment');

 for (var i = 0; i < results.data.length; i++) {
    var option = document.createElement("option");
    option.value = i; // results.data [i][0];
    option.text = results.data[i][0];
    selectList.appendChild(option); }

let academicsCSV = <?php echo json_encode(file_get_contents('yazar-id.csv')); ?>; 
 <!-- Akademisyen seçimi menüsünü oluşturur -->
 let academicResults = Papa.parse(academicsCSV);
academics = academicResults.data; // Merkez
		
var acadList = document.getElementById('academicians');		
for (let i = 0; i < academics.length; i++) {
    let option = document.createElement("option");
    option.value = academics[i][2] + " "+ academics[i][3] + ", " + academics[i][4] + ", " + academics[i][5]; // ad, soyad, ABD, BD
    acadList.appendChild(option); }
}

</script>
</body>
</html>