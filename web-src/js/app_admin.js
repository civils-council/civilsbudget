var innSearch = document.getElementById('inn-search');
innSearch.addEventListener("input", checkInn);

function isNumberKey(e){
    var charCode = (e.which) ? e.which : event.keyCode;

    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }

    if (innSearch.value.length >= 10) {
        return false;
    }

    return true;
}

function checkInn() {
    if (innSearch.value.length === 10) {
        httpRequest(innSearch.getAttribute('data-search-url') + innSearch.value + "/search", AnalyseResponse);
    }
}

function httpRequest(url, callback)
{
    var xmlHttp = new XMLHttpRequest();
    xmlHttp.onreadystatechange = function() {
        if (xmlHttp.readyState == 4 && xmlHttp.status == 200)
            callback(xmlHttp.responseText);
    }
    xmlHttp.open("GET", url, true);
    xmlHttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xmlHttp.send(null);
}

function AnalyseResponse(response) {
    var searchResult = document.getElementById('inn-search-result');
    searchResult.innerHTML = response;
}
