function add_episode(counter,season) {
	var countn = (counter + 1);
	$("#AddEpisode_"+season).remove();
	$('#SeasonDIV-'+season).append(
		'<span id=add-seperator class=or-seperator style=margin: 24px auto 34px;><em>Episode (Season '+season+')</em></span>'
		+
		'<div style="margin-top: -24px;">'
		+
		'	<div class="add-url-input cf"><input id="add-url" type="text" required="required" name="episode_name_'+season+'[]" class="field" placeholder="Episode Name"></div>'
		+
		'	<div class="add-url-input cf"><input id="add-url" type="text" name="episodes_movie_flv_'+season+'[]" class="field" placeholder="Episode Link (flv, mp4 and etc.)"></div>'
		+
		'	<div class="add-url-input cf"><input id="add-url" type="text" name="episodes_movie_iframe_'+season+'[]" class="field" placeholder="Episode Iframe Link"></div>'
		+
		'</div>'
		+
		'<input id="AddEpisode_'+season+'" class="AddEpisode" type="button" value="Add Episode" onclick="add_episode('+countn+','+season+')" style="float: right!important; margin-top: 10px; margin-bottom: 10px; background-color: #4CAF50 !important; text-decoration: none; border: none; font-size: 16px; padding: 6px 18px; border-radius: 40px; color: #FFF; text-align: center; letter-spacing: 0.5px; position: relative; cursor: pointer; display: inline-block; overflow: hidden; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; vertical-align: middle; z-index: 1;">'
	);
}

function add_season(count) {
	var counts = (count + 1);
	$("#SeasonButton").remove();
	$('#episodes_result').append(
		'<div id="SeasonDIV-'+counts+'" style="clear: both; padding-top: 20px;"><input id="SeasonButton" type="button" value="Create Season" onclick="add_season('+counts+')" style="float: right!important; margin-top: 10px; margin-bottom: 10px; background-color: #EC3A39 !important; text-decoration: none; border: none; font-size: 16px; padding: 6px 18px; border-radius: 40px; color: #FFF; text-align: center; letter-spacing: 0.5px; position: relative; cursor: pointer; display: inline-block; overflow: hidden; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; vertical-align: middle; z-index: 1;">'
		+
		'<input type="hidden" name="season[]" value="">'
		+
		'<span id="add-seperator" class="or-seperator" style="margin: 24px auto 34px; border-color: #EC3A39;"><em style="font-size: 15px; font-family: Nunito; color: #EC3A39; font-weight: bold;">Season <span id="EPISODE_NUM">'+counts+'</span></em></span>'
		+
		'<span id="add-seperator" class="or-seperator" style="margin: 24px auto 34px;"><em>Episode</em></span>'
		+
		'<div style="margin-top: -24px;">'
		+
		'	<div class="add-url-input cf"><input id="add-url" type="text" required="required" name="episode_name_'+counts+'[]" class="field" placeholder="Episode Name"></div>'
		+
		'	<div class="add-url-input cf"><input id="add-url" type="text" name="episodes_movie_flv_'+counts+'[]" class="field" placeholder="Episode Link (flv, mp4 and etc.)"></div>'
		+
		'	<div class="add-url-input cf"><input id="add-url" type="text" name="episodes_movie_iframe_'+counts+'[]" class="field" placeholder="Episode Iframe Link"></div>'
		+
		'</div>'
		+
		'<input id="AddEpisode_'+counts+'" class="AddEpisode" type="button" value="Add Episode" onclick="add_episode(1,'+counts+')" style="float: right!important; margin-top: 10px; margin-bottom: 10px; background-color: #4CAF50 !important; text-decoration: none; border: none; font-size: 16px; padding: 6px 18px; border-radius: 40px; color: #FFF; text-align: center; letter-spacing: 0.5px; position: relative; cursor: pointer; display: inline-block; overflow: hidden; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none; vertical-align: middle; z-index: 1;"></div>'
	);
}

function show_season(season_id) {
	$("#SeasonsOpen").fadeIn().css('display', 'block');
	$.get(sub_folder+"/gold-app/gold-includes/GOLD.php?season_id="+season_id, function(data, status) {
		$(".episodes-list").html(data);
	});
}

function show_seasons(seasons_post_name) {
	$("#SeasonsOpen").fadeOut();
	$.get(sub_folder+"/gold-app/gold-includes/GOLD.php?seasons_post_name="+seasons_post_name, function(data, status) {
		$(".episodes-list").html(data);
	});
}

function show_episode(episode_id) {
	$.get(sub_folder+"/gold-app/gold-includes/GOLD.php?episode_id="+episode_id, function(data, status) {
		$("#EPISODE_PLAYER").html(data);
	});
}

function FETCH_MOVIE() {
	var title = document.getElementsByName("title")[0].value;
	var year = document.getElementsByName("year")[0].value;
	$.get(sub_folder+"/gold-app/gold-includes/GOLD.php?API=1&API_TITLE="+title+"&API_YEAR="+year, function(data, status) {
		obj = JSON.parse(data);
		document.getElementsByName("year")[0].value = obj.year;
		document.getElementsByName("imdb")[0].value = obj.imdb;
		document.getElementsByName("directed_by")[0].value = obj.directors;
		document.getElementsByName("casts")[0].value = obj.casts;
		document.getElementsByName("description")[0].value = obj.description;
		document.getElementsByName("movie_iframe")[0].value = obj.youtube;
	});
}

$(function () {
	$('.episodes-list').anythingSlider({
		'theme':'episodes',
	    'expand':true,
	    'hashTags':false,
	    'showMultiple':4,
	    'changeBy': 4,
	    'buildArrows':false,
	    'buildNavigation':false,
	    'buildStartStop':false,
	    'infiniteSlides':false,
	    'resizeContents':true,
	    'stopAtEnd': true,
	    'enableKeyboard': false
	});   

	$('.prev-episode').click(function(e) {
		$('.episodes-list').data('AnythingSlider').goBack();
		e.preventDefault();
	});

	$('.next-episode').click(function(e) { 
		$('.episodes-list').data('AnythingSlider').goForward();
		e.preventDefault();
	});
});
$(document).ready(function () {
	$(".select-movie-genre").fadeIn('slow');
	$(".select-movie-genre").click(function (e) {
		e.stopPropagation();
		$("#select-movie-genre").fadeToggle(50);
	});
	$(document).click(function () {
		var $el = $("#select-movie-genre");
		if ($el.is(":visible")) {
			$el.fadeIn(50);
			$el.fadeOut(50);
		}
	});
	$(".select-movie-genre").mouseleave(function() {
		$("#select-movie-genre").css('display', 'none');
	});
});
$(function(){
	$("#comment_value").keyup(function(){
    	$(".char_num").text($(this).val().length);
	});
	$('.add_comment').autosize();
	$('#loginform').submit(function(e){
		return false;
	});
	
	$('#open_modal').leanModal({ top: 110, overlay: 0.60, closeButton: ".hidemodal" });
});

$("#login_button").live('click', function(){
	username=$("#signin-email").val();
	password=$("#signin-password").val();
	$.ajax({
		type: "POST",
		url: sub_folder+"/gold-app/gold-includes/GOLD.php",
		data: "gold=login&name="+username+"&password="+password,
		success: function(html){
			if(html=='true') {
			 	window.location=sub_folder+"/admin";
			} else {
				$("#cd-error-message").css('display', 'inline', 'important');
				$("#cd-error-message").html(html);
			}
		}
	});
	return false;
});
		
$("#cd-error-message").hide();
$(function(){
	$("#drag_elements ul").sortable({ scroll: true, scrollSensitivity: 100, opacity: 0.6, cursor: "move", update: function() {
		var order = "gold=admin_menu&"+$(this).sortable("serialize") + "&action=updateRecordsListings";
		$.post(sub_folder+"/gold-app/gold-includes/GOLD.php", order, function(theResponse){ });
	} });
	$("#main_sidebar_drag_elements ul").sortable({ scroll: true, scrollSensitivity: 100, opacity: 0.6, cursor: "move", update: function() {
		var order = "gold=admin_menu&"+$(this).sortable("serialize") + "&action=main_sidebar_updateRecordsListings";
		$.post("/gold-app/gold-includes/GOLD.php", order, function(theResponse){ });
	} });
	$("#profile_sidebar_drag_elements ul").sortable({ scroll: true, scrollSensitivity: 100, opacity: 0.6, cursor: "move", update: function() {
		var order = "gold=admin_menu&"+$(this).sortable("serialize") + "&action=profile_sidebar_updateRecordsListings";
		$.post("/gold-app/gold-includes/GOLD.php", order, function(theResponse){ });
	} });
	$("#post_sidebar_drag_elements ul").sortable({ scroll: true, scrollSensitivity: 100, opacity: 0.6, cursor: "move", update: function() {
		var order = "gold=admin_menu&"+$(this).sortable("serialize") + "&action=post_sidebar_updateRecordsListings";
		$.post("/gold-app/gold-includes/GOLD.php", order, function(theResponse){ });
	} });
});

// COMMENTS AND FACEBOOK COMMENTS
$('.comments-switch').click(function(){
	var comment_type = $(this).data('comments');
	$('#tab_disqus_comments, #tab_fb_comments').hide();
	$(comment_type).show();
	$('.comments-switch').removeClass('active');
	$(this).addClass('active');
});

$(window).load(function(){
    $("#main-nav-toggle").click(function(event)
    {
        $(".sidebar").animate({width: 'toggle'});      
    });
    $("#close-aside").click(function(event)
    {
        $(".sidebar").animate({width: 'toggle'});      
    });
});
 
//<![CDATA[
window.onload = function(){
        
    //Check File API support
    if(window.File && window.FileList && window.FileReader)
    {
        var filesInput = document.getElementById("add-file");
        
        filesInput.addEventListener("change", function(event){
            
            var files = event.target.files; //FileList object
            var output = document.getElementById("result");
            
            for(var i = 0; i< files.length; i++)
            {
                var file = files[i];
                
                //Only pics
                if(!file.type.match('image'))
                  continue;
                
                var picReader = new FileReader();
                
                picReader.addEventListener("load",function(event){
                    
					$("#upload-data").css("clear", "both");
					$("#add-seperator").hide();
					
                    var picFile = event.target;
                    
                    var div = document.createElement("div");
					
                    div.className = "preview_img";
					
                    div.innerHTML = "<img style='display: block; width: 280px; float: left;' src='" + picFile.result + "'" +
                            "title='" + picFile.name + "'/>";
							
                    output.insertBefore(div,null);            
                
                });
                
                 //Read the image
                picReader.readAsDataURL(file);
				
				
            }                               
           
        });
    }
    else
    {
        console.log("Your browser does not support File API");
    }
}
//]]> 