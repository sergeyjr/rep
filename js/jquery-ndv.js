$(document).ready(function() {

//Функция динамического поиска
$.LiveSearch=function() {
var remote_url=window.location.href.split("?")[0].split("#")[0];
var remote_name=$("#client_name").val();
var remote_sort=$("#sort").val();
var remote_data_num=$("#data_num").val();
$("#progress").show();
$.ajax({
type: 'GET',
url: remote_url,
data: {
action: 'live_search', 
client_name: remote_name, 
sort: remote_sort, 
data_num: remote_data_num 
},
async: true,
success: function (result) {
var dataObj=$.parseJSON(result);
$("#progress").hide();
if (dataObj.info.length > 0) {
var tbody_content;
$.each(dataObj.info, function(idx, obj) {
tbody_content+="<TR>";
tbody_content+="<TD>"+(idx+1)+"</TD>";
tbody_content+="<TD>"+obj.name+"</TD>";
tbody_content+="<TD>"+obj.address+"</TD>";
tbody_content+="<TD>"+obj.emails+"</TD>";
tbody_content+="<TD>"+obj.emails+"</TD>";
tbody_content+="<TD>"+obj.phones+"</TD>";
tbody_content+="</TR>";
});
$("#processing_time").html(dataObj.processing_time);
$("#results").show();
$("#no_results").hide();
$("#content").html(tbody_content);
}
else {
$("#results").hide();
$("#no_results").show();
}
}
});
};

$("#client_name").live("keyup", function(e) {
var radio_checked=$('input[name=live_search]:radio:checked').val();
if (radio_checked == 1) {
if (e.which == 13) { // enter click
e.preventDefault();
}
$.LiveSearch();
}
else {
if (e.which == 13) { // enter click
$("#filter_form").submit();
}
}
});

$("#sort, #data_num").live("change", function(e) {
var radio_checked=$('input[name=live_search]:radio:checked').val();
if (radio_checked == 1) {
$.LiveSearch();
}
else {
$("#filter_form").submit();
}
});

$("#search_button").click(function(e) {
var radio_checked=$('input[name=live_search]:radio:checked').val();
if (radio_checked == 1) {
e.preventDefault();
$("#client_name").focus();
}
});

$("#live_search").click(function() {
$("#search_button").attr("disabled", false);
$("#client_name").focus();
});

$("#live_search2").click(function() {
$("#search_button").attr("disabled", true);
$("#client_name").focus();
});

$("#client_name").focus();
$("#search_button").attr("disabled", true);

});
