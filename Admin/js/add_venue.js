// $Id: add_venue.js,v 1.6 2007/10/30 22:26:23 loyola Exp $

var categoryHelp=
    "The category the venue falls under. For examplethis could "
    + "be a conference, journal, a book chapter, etc.<br/><br/>"
    + "Please use the drop down menu to select an appropriate "
    + "category to classify the publication entry. If you cannot find an "
    + "appropriate category you can select 'Add New Category' from "
    + "the navigation menu.<br/><br/>";

function dataKeep(num) {
    var qsArray = new Array();
    var qsString = "";

    for (i = 0; i < document.forms["venueForm"].elements.length; i++) {
        var element = document.forms["venueForm"].elements[i];
        if ((element.type != "submit") && (element.type != "reset")
            && (element.type != "button")
            && (element.value != "") && (element.value != null)) {

            if ((element.type == "checkbox") || (element.type == "radio")) {
                if (element.checked) {
                    qsArray.push(element.name + "=" + element.value);
                }
            }
            else if (element.name == "venue_id") {
                qsArray.push(element.name + "=" + element.value);
                qsArray.push("status=change");
            }
            else if ((element.name == "type")  ||  (element.name == "v_usage")) {
                if (element.checked) {
                    qsArray.push(element.name + "=" + element.value);
                }
            }
            else if (element.name == "numNewOccurrences") {
                qsArray.push(element.name + "=" + num);
            }
            else {
                qsArray.push(element.name + "=" + element.value);
            }
        }
    }

    if (qsArray.length > 0) {
        qsString = qsArray.join("&");
        qsString.replace("\"", "?");
        qsString.replace(" ", "%20");
    }

    location.href = "http://{host}{self}?" + qsString;
}
function dataRemove(num) {
    var qsArray = new Array();
    var qsString = "";
    var indexYear = 0;
    var indexLocation = 0;
    var indexDate = 0;
    var indexUrl = 0;

    for (i = 0; i < document.forms["venueForm"].elements.length; i++) {
        var element = document.forms["venueForm"].elements[i];
        if ((element.type != "submit") && (element.type != "reset")
            && (element.type != "button")
            && (element.value != "") && (element.value != null)) {

            if ((element.type == "checkbox") || (element.type == "radio")) {
                if (element.checked) {
                    qsArray.push(element.name + "=" + element.value);
                }
            }
            else if (element.name == "venue_id") {
                qsArray.push(element.name + "=" + element.value);
                qsArray.push("status=change");
            }
            else if ((element.name == "type") ||  (element.name == "v_usage")) {
                if (element.checked) {
                    qsArray.push(element.name + "=" + element.value);
                }
            }
            else if (element.name == "numNewOccurrences") {
                numOccur = parseInt(element.value) - 1;
                qsArray.push(element.name + "=" + numOccur);
            }
            else if (element.name.indexOf("newOccurrenceLocation") >= 0) {
                if (element.name != "newOccurrenceLocation[" + num + "]") {
                    qsArray.push("newOccurrenceLocation["
                                 + indexLocation + "]="
                                 + element.value);
                    indexLocation++;
                }
            }
            else if (element.name.indexOf("newOccurrenceDate") >= 0) {
                if (element.name != "newOccurrenceDate[" + num + "]") {
                    qsArray.push(element.name + "=" + element.value);
                    indexDate++;
                }
            }
            else if (element.name.indexOf("newOccurrenceUrl") >= 0) {
                if (element.name != "newOccurrenceUrl[" + num + "]") {
                    qsArray.push("newOccurrenceUrl["
                                 + indexUrl + "]=" + element.value);
                    indexUrl++;
                }
            }
            else {
                qsArray.push(element.name + "=" + element.value);
            }
        }
    }

    if (qsArray.length > 0) {
        qsString = qsArray.join("&");
        qsString.replace("\"", "?");
        qsString.replace(" ", "%20");
    }

    location.href = "http://{host}{self}?" + qsString;
}
