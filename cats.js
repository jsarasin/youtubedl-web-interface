function humanFileSize(size) {
    if(size == -1) {
        return "Unknown";
    }
    var i = size == 0 ? 0 : Math.floor( Math.log(size) / Math.log(1024) );
    return ( size / Math.pow(1024, i) ).toFixed(2) * 1 + '&nbsp;' + ['B', 'kB', 'MB', '<b>GB</b>', '<span style="color:red"><b>TB</b></span>'][i];
};

function query_video_url() {
    var target_url = $('#url-target').val();

    
    var slurp_area = $(".slurping").clone()
        .removeClass("hidden")
        .removeClass("slurping")

    slurp_area.find(".slurp_target").html(target_url);

    if(query_count >= 2) {
        $("#query_results").prepend("<hr>");
    }
    slurp_area.prependTo($("#query_results"));

    $.ajax({
    type: "POST",
        url: "query_streams.php",
        data: {
            video_url: target_url
        },
    success: function(result) {
        console.log(result);
		json_results = JSON.parse(result);
		if('Error' in json_results) {
			build_error(slurp_area, json_results['Error']);
		} else {
	        build_query_results(slurp_area,  JSON.parse(result));
		}
    },
    error: function(result) {
        alert('error' + result);
    }
});
}

function open_stream(url) {
    $('my_iframe').attr('src', url);
}

function build_error(fill_area, error_message) {
	fill_area.empty();
	fill_area.html("<div class='row'><div class='col-sm-1'>&nbsp</div><div class='col-sm-10'><div class='alert alert-danger'><strong>Error:</strong>" + error_message + "</div></div><div class='col-sm-1'>&nbsp</div></div>")
}

function build_query_results(fill_area, result) {
    fill_area.empty();
    query_count++;
    
    var hours = Math.floor(result['duration']/3660);
    var remainder = result['duration']%3660
    
    var minutes = Math.floor(remainder/60);
    remainder = remainder%60;
    
    var seconds = Math.floor(remainder);
    console.log(remainder)

    var video_length_string = "";
    
    if(hours > 0) {
        video_length_string += hours + " hours";
    }
    
    if(minutes > 0) {
        video_length_string += " " + minutes + " minutes";
    }
    
    if(seconds > 0) {
        video_length_string += " " + seconds + " seconds";
    }
    
    if(result['duration'] <= 0) {
        video_length_string = "";
    }
    var new_vid_container = $(".video_container").clone()
        .removeClass("hidden")
        .removeClass("video_container")
    
    
    new_vid_container.find(".video_title").html(result['title']);
    new_vid_container.find(".video_description").html(result['description']);
    new_vid_container.find(".video_duration").html("<b>Length:</b> " + video_length_string);
    new_vid_container.find(".video_thumbnail").attr("src", result['thumbnail']);
    new_vid_container.find(".hidden_area").attr("id", "hidden" + query_count);
    new_vid_container.find(".show_formats_collapser").attr("data-target", "#hidden" + query_count);
    new_vid_container.find(".showmore_description").click(function() {
        console.log($(this));
        console.log($(this).parent());
        
        $(this).prev().removeClass("collapsed_description");
        $(this).addClass("hidden");
        $(this).next().removeClass("hidden");
    });
    new_vid_container.find(".showless_description").click(function() {
        console.log($(this));
        console.log($(this).parent());
        
        $(this).prev().prev().addClass("collapsed_description");
        $(this).addClass("hidden");
        $(this).prev().removeClass("hidden");
    });



    /** Build the table **/
    var new_vid_container_formats = new_vid_container.find(".video_format_table")
                                    .removeClass("video_format_table");

    result['formats'].forEach((format) => {
        var new_format_row = $(".video_format_row").clone()
                            .removeClass("hidden")
                            .removeClass("video_format_row");
                            // .attr("onclick", "open_stream(\'    " + format['url'] + "\');");
        
        var video_format_entry_format = new_format_row.find(".video_format_entry_url")
                                        .removeClass("video_format_entry_url")
                                        .attr('href', format['url']);
        
        var video_format_entry_format = new_format_row.find(".video_format_entry_id")
                                        .removeClass("video_format_entry_id")
                                        .html(format['format_id']);
        
        var video_format_entry_format = new_format_row.find(".video_format_entry_ext")
                                        .removeClass("video_format_entry_ext")
                                        .html(format['format_ext']);
        
        var video_format_entry_format = new_format_row.find(".video_format_entry_format")
                                        .removeClass("video_format_entry_format")
                                        .html(format['format_string']);

        var video_format_entry_filesize = new_format_row.find(".video_format_entry_filesize")
                                                        .removeClass("video_format_entry_filesize")
                                                        .html(humanFileSize(format['filesize']));
        new_format_row.appendTo(new_vid_container_formats);
    });
    if(query_count >= 2) {
        fill_area.append("<hr>");
    }
    new_vid_container.prependTo(fill_area);

}



var query_count = 0;
